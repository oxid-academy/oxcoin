<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidAcademy\OxCoin\Tests\Unit\Core;

use OxidAcademy\OxCoin\Core\Events;
use OxidEsales\Eshop\Application\Model\Payment;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\DbMetaDataHandler;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Model\BaseModel;
use PHPUnit\Framework\TestCase;

class EventsTest extends TestCase
{
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
     * Test if the method Events::addActivePaymentMethod adds our payment to the table oxpayments.
     *
     * @group addActivePaymentMethod
     */
    public function testaddActivePaymentMethodIfItIsNotExistingYet()
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
        Events::addActivePaymentMethod();

        // Payment should exist
        $result = (bool) DatabaseProvider::getDb()->getOne(
            $query,
            ['oxcoin']
        );

        // Testing the outcome of the method Events::addActivePaymentMethod
        $this->assertTrue($result);
    }

    /**
     * We expect here that the payment will not be added again when it was already added to the table oxpayments. So we
     * pick a field which is set during the activation and change it. If the module works correct, the payment will not
     * be added again to the table and the value of the changed field will still have our value.
     *
     * @group addActivePaymentMethod
     */
    public function testDoNotaddActivePaymentMethodAgainWhenItIsAlreadyExisting()
    {
        $payment = oxNew(Payment::class);
        $payment->setId('oxcoin');
        $payment->oxpayments__oxdesc = new Field('foobar'); // The module would write the value 'oxCoin'.
        $payment->save();

        Events::addActivePaymentMethod();

        // Reloading the object to get that latest values from the database.
        $payment->load('oxcoin');
        $this->assertEquals('foobar', $payment->oxpayments__oxdesc->value);
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
}
