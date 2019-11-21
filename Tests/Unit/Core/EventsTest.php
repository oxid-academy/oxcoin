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

class EventsTest extends UnitTestCase
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
	// Your test code coes here...


	// Parent call
        parent::tearDown();
    }

    /**
     * Test if the method Events::addPaymentMethod adds our payment to the table oxpayments.
     *
     * @group addPaymentMethod
     */
    public function testAddPaymentMethodIfItIsNotExistingYet()
    {

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

    }

    /**
     * Test if the Method Events::activatePaymentMethod will actually set the value for the table field oxactive to 1.
     * So we first set it manually to 0, call the method and test if the field has the value 1 now.
     *
     * @group activatePaymentMethod
     */
    public function testActivatePaymentMethod()
    {

    }

    /**
     * @group assignPaymentMethodToDefaultShippingMethod
     */
    public function testAssignPaymentMethodToDefaultShippingMethod()
    {

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

    }

    /**
     * The same as testActivatePaymentMethod but vice versa.
     *
     * @group deactivatePaymentMethod
     */
    public function testDeactivatePaymentMethod()
    {

    }

    /**
     * Testing the method onActivate generally if all is done when activating our module.
     *
     * @group onActivate
     */
    public function testOnActivate()
    {

    }

    /**
     * @group onDeactivate
     */
    public function testOnDeactivate()
    {

    }

    /**
     * Testing the method onActivate adds our field oxac_oxcoin to the table oxuser.
     *
     * @group addTableFieldToUserTable
     */
    public function testAddFieldToUserTableWhenItIsNotExisting()
    {

    }

    /**
     * Testing the method onActivate. Check if the field oxac_oxcoin is only added to the table oxuser, when it is not
     * existing yet. If it would be overwritten, then the saved value 665.0 would be 0.0 again.
     *
     * @group addTableFieldToUserTable
     */
    public function testDoNotAddTheFieldOxAcOxcoinToTheUserTableWhenItIsAlreadyExisting()
    {

    }
}
