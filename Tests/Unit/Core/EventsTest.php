<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 24.04.19
 * Time: 08:16
 */

namespace OxidAcademy\OxCoin\Tests\Unit\Core;

use OxidAcademy\OxCoin\Core\Events;
use OxidEsales\Eshop\Application\Model\Payment;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Model\BaseModel;

class EventsTest extends \OxidEsales\TestingLibrary\UnitTestCase
{
    /**
     * Will be fired every time before executing a test method.
     */
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * Will be fired every time after executing a test method.
     */
    public function tearDown()
    {
        // Generally deleting the payment
        DatabaseProvider::getDb()->execute('delete from oxpayments where oxid = "oxcoin"');
        DatabaseProvider::getDb()->execute('delete from oxobject2payment where oxpaymentid = "oxcoin"');

        parent::tearDown();
    }

    /**
     * Test if the method Events::addPaymentMethod adds our payment to the table oxpayments.
     *
     * @group addPaymentMethod
     */
    public function testAddPaymentMethodIfItIsNotExistingYet()
    {

        // Generally deleting the payment
        DatabaseProvider::getDb()->execute('delete from oxpayments where oxid = "oxcoin"');

        // Control query
        $query = 'select 1 from oxpayments where oxid = "oxcoin" limit 1';

        // Payment should not exist
        $result = (bool) DatabaseProvider::getDb()->getOne($query);
        $this->assertFalse($result);

        // Test the method
        Events::addPaymentMethod();

        // Payment should exist
        $result = (bool) DatabaseProvider::getDb()->getOne($query);

        // Testing the outcome of the method Events::addPaymentMethod
        $this->assertTrue($result);
    }

    /**
     * We expect here that the payment will not be added again when it was already added to the table oxpayments. So we
     * pick a field which is set during the activation and change it. If the module works correct, the payment will not
     * be added again to the table and the value of the changed field will still have our value.
     *
     * @group addPaymentMethod
     */
    public function testDoNotAddPaymentMethodAgainWhenItIsAlreadyExisting()
    {
        $payment = oxNew(Payment::class);
        $payment->setId('oxcoin');
        $payment->oxpayments__oxdesc = new Field('foobar'); // The module would write the value 'oxCoin'.
        $payment->save();

        Events::addPaymentMethod();

        // Reloading the object to get that latest values from the database.
        $payment->load('oxcoin');
        $this->assertEquals('foobar', $payment->oxpayments__oxdesc->value);
    }

    /**
     * Test if the Method Events::activatePaymentMethod will actually set the value for the table field oxactive to 1.
     * So we first set it manually to 0, call the method and test if the field has the value 1 now.
     *
     * @group activatePaymentMethod
     */
    public function testActivatePaymentMethod()
    {
        $payment = oxNew(Payment::class);
        $payment->setId('oxcoin');
        $payment->oxpayments__oxactive = new Field(0);
        $payment->save();

        // Control test that the magic getter returns false as expected
        $this->assertFalse((bool) $payment->oxpayments__oxactive->value);

        Events::activatePaymentMethod();

        $payment->load('oxcoin');
        $this->assertTrue((bool) $payment->oxpayments__oxactive->value);
    }

    /**
     * @group assignPaymentMethodToDefaultShippingMethod
     */
    public function testAssignPaymentMethodToDefaultShippingMethod()
    {
        $db = DatabaseProvider::getDb();
        $db->execute('delete from oxobject2payment where oxpaymentid = "oxcoin" and oxobjectid = "oxidstandard"');

        Events::assignPaymentMethodToDefaultShippingMethod();

        $query = 'select 1 from oxobject2payment where oxpaymentid = "oxcoin" and oxobjectid = "oxidstandard"';
        $this->assertTrue((bool) $db->getOne($query));
    }

    /**
     * We expect here that the assignment payment <-> user groups will not be made again when it was already
     * added to the assignment table. So we pick a field which is set during the creation and change it. If the
     * module works correct, the assignment will not be added again to the table and the value of the changed field
     * will still have our value.
     *
     * @group assignPaymentMethodToDefaultShippingMethod
     */
    public function testAssignPaymentMethodToDefaultShippingMethodAddNothingIfAlreadyExisting()
    {
        $db = DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC);
        $db->execute('delete from oxobject2payment');

        $object2Payment = oxNew(BaseModel::class);
        $object2Payment->init('oxobject2payment');
        $object2Payment->oxobject2payment__oxpaymentid = new Field('oxcoin');
        $object2Payment->oxobject2payment__oxobjectid = new Field('oxidstandard');
        $object2Payment->oxobject2payment__oxtype = new Field('foobar');
        $object2Payment->save();
        $oxid = $object2Payment->getId();

        Events::assignPaymentMethodToDefaultShippingMethod();

        $object2Payment = oxNew(BaseModel::class);
        $object2Payment->init('oxobject2payment');
        $object2Payment->load($oxid);

        $this->assertEquals('foobar', $object2Payment->oxobject2payment__oxtype->value);
    }

    /**
     * The same as testActivatePaymentMethod but vice versa.
     *
     * @group deactivatePaymentMethod
     */
    public function testDeactivatePaymentMethod()
    {
        $payment = oxNew(Payment::class);
        $payment->setId('oxcoin');
        $payment->oxpayments__oxactive = new Field(1);
        $payment->save();

        // Control test that the magic getter returns true as expected
        $this->assertTrue((bool) $payment->oxpayments__oxactive->value);

        Events::deactivatePaymentMethod();

        // Reloading the object to get that latest values from the database.
        $payment->load('oxcoin');

        // Testing the method
        $this->assertFalse((bool) $payment->oxpayments__oxactive->value);
    }

    /**
     * Testing the method onActivate generally if all is done when activating our module.
     *
     * @group onActivate
     */
    public function testOnActivate()
    {
        Events::onActivate();

        $query = 'select oxid, oxactive from oxpayments where oxid = "oxcoin" limit 1';
        $result = DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC)->select($query);

        $this->assertEquals('oxcoin', $result->fields['oxid']); // It exists...
        $this->assertEquals('1', $result->fields['oxactive']); // ... and was activated.

        // ... and was assigned to a shipping method
        $query = 'select 1 from oxobject2payment where oxpaymentid = "oxcoin" limit 1';
        $this->assertTrue((bool) DatabaseProvider::getDb()->getOne($query));
    }

    /**
     * @group onDeactivate
     */
    public function testOnDeactivate()
    {
        $payment = oxNew(Payment::class);
        $payment->setId('oxcoin');
        $payment->oxpayments__oxactive = new Field(1);
        $payment->save();

        Events::onDeactivate();

        $query = 'select oxid, oxactive from oxpayments where oxid = "oxcoin" limit 1';
        $result = DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC)->select($query);

        $this->assertEquals('oxcoin', $result->fields['oxid']); // It exists...
        $this->assertEquals('0', $result->fields['oxactive']); // ... and was deactivated.
    }
}