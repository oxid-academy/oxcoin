<?php
/**
 * This Software is the property of OXID eSales and is protected
 * by copyright law - it is NOT Freeware.
 *
 * Any unauthorized use of this software without a valid license key
 * is a violation of the license agreement and will be prosecuted by
 * civil and criminal law.
 *
 * @author        OXID Academy
 * @link          https://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2019
 *
 * User: michael
 * Date: 24.04.19
 * Time: 08:16
 */

namespace OxidAcademy\OxCoin\Tests\Unit\Core;

use OxidAcademy\OxCoin\Core\Events;
use OxidEsales\Eshop\Application\Model\Payment;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\DbMetaDataHandler;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Model\BaseModel;
use OxidEsales\TestingLibrary\UnitTestCase;

class EventsTest extends UnitTestCase
{
    /**
     * Will be fired every time before executing a test method.
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Will be fired every time after executing a test method.
     */
    public function tearDown(): void
    {
        // Generally deleting the payment
        DatabaseProvider::getDb()->execute(
            'DELETE FROM oxpayments WHERE oxid = ?',
            ['oxcoin']
        );
        DatabaseProvider::getDb()->execute(
            'DELETE FROM oxobject2payment WHERE oxpaymentid = ?',
            ['oxcoin']
        );

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
        DatabaseProvider::getDb()->execute(
            'DELETE FROM oxpayments WHERE oxid = ?',
            ['oxcoin']
        );

        // Control query
        $query = 'SELECT 1 FROM oxpayments WHERE oxid = ? LIMIT 1';

        // Payment should not exist
        $result = (bool) DatabaseProvider::getDb()->getOne(
            $query,
            ['oxcoin']
        );
        $this->assertFalse($result);

        // Test the method
        Events::addPaymentMethod();

        // Payment should exist
        $result = (bool) DatabaseProvider::getDb()->getOne(
            $query,
            ['oxcoin']
        );

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
        $db->execute(
            'DELETE FROM oxobject2payment WHERE oxpaymentid = ? AND oxobjectid = ?',
            [
                'oxcoin',
                'oxidstandard'
            ]
        );

        Events::assignPaymentMethodToDefaultShippingMethod();

        $query = 'SELECT 1 FROM oxobject2payment WHERE oxpaymentid = ? AND oxobjectid = ?';
        $this->assertTrue((bool) $db->getOne(
            $query,
            [
                'oxcoin',
                'oxidstandard'
            ]
        ));
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

        $query = 'SELECT oxid, oxactive FROM oxpayments WHERE oxid = ? LIMIT 1';
        $result = DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC)->select(
            $query,
            ['oxcoin']
        );

        $this->assertEquals('oxcoin', $result->fields['oxid']); // It exists...
        $this->assertEquals('1', $result->fields['oxactive']); // ... and was activated.

        // ... and was assigned to a shipping method
        $query = 'SELECT 1 FROM oxobject2payment WHERE oxpaymentid = ? LIMIT 1';
        $this->assertTrue((bool) DatabaseProvider::getDb()->getOne(
            $query,
            ['oxcoin']
        ));
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

        $query = 'SELECT oxid, oxactive FROM oxpayments WHERE oxid = ? LIMIT 1';
        $result = DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC)->select(
            $query,
            ['oxcoin']
        );

        $this->assertEquals('oxcoin', $result->fields['oxid']); // It exists...
        $this->assertEquals('0', $result->fields['oxactive']); // ... and was deactivated.
    }

    /**
     * Testing the method onActivate adds our field oxac_oxcoin to the table oxuser.
     *
     * @group addTableFieldToUserTable
     */
    public function testAddFieldToUserTableWhenItIsNotExisting()
    {
        $db = DatabaseProvider::getDb();
        $databaseMetaDataHandler = oxNew(DbMetaDataHandler::class);

        $table = 'oxuser';
        $field = 'oxac_oxcoin';

        $db->execute('ALTER TABLE '.$table.' DROP COLUMN '.$field.';');

        $this->assertFalse($databaseMetaDataHandler->fieldExists($field, $table));

        Events::addTableFieldToUserTable();

        $this->assertTrue($databaseMetaDataHandler->fieldExists($field, $table));
    }

    /**
     * Testing the method onActivate. Check if the field oxac_oxcoin is only added to the table oxuser, when it is not
     * existing yet. If it would be overwritten, then the saved value 665.0 would be 0.0 again.
     *
     * @group addTableFieldToUserTable
     */
    public function testDoNotAddTheFieldOxAcOxcoinToTheUserTableWhenItIsAlreadyExisting()
    {
        $db = DatabaseProvider::getDb();
        $databaseMetaDataHandler = oxNew(DbMetaDataHandler::class);

        Events::addTableFieldToUserTable();

        $this->assertTrue($databaseMetaDataHandler->fieldExists('oxac_oxcoin', 'oxuser'));

        $user = oxNew(User::class);
        $user->setId('_test_oxid');
        $user->oxuser__oxusername = new Field('_test_user');
        $user->oxuser__oxac_oxcoin = new Field(665.0);
        $user->save();

        $controlQuery = 'select oxac_oxcoin from oxuser where oxid = "_test_oxid"';

        $amount = $db->getOne($controlQuery);
        $this->assertEquals(665, $amount);

        Events::addTableFieldToUserTable();

        $user->load($user->getId());
        $amount = $db->getOne($controlQuery);
        $this->assertEquals(665, $amount);
    }
}
