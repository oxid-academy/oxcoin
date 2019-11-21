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
 * Date: 22.05.19
 * Time: 10:01
 */

namespace OxidAcademy\OxCoin\Tests\Unit\Model;

use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\TestingLibrary\UnitTestCase;

class OrderTest extends UnitTestCase
{
    /**
     * @group finalizeOrder
     */
    public function testFinalizeOrderEarnCoinWhenUserIsCustomer()
    {
        $user = $this
            ->getMockBuilder(User::class)
            ->setMethods(['isMallAdmin'])
            ->getMock()
        ;

        $user->expects($this->once())->method('isMallAdmin')->will($this->returnValue(false));

        $basket = oxNew(Basket::class);
        $basket->setNettoSum(1000.0);

        $this->assertEquals(0, $user->oxuser__oxac_oxcoin->value);

        $order = oxNew(Order::class);
        $order->finalizeOrder($basket, $user);

        $this->assertEquals(1, $user->oxuser__oxac_oxcoin->value);
    }

    /**
     * @group finalizeOrder
     */
    public function testFinalizeOrderDoNotEarnCoinWhenUserIsMallAdmin()
    {
        $user = $this
            ->getMockBuilder(User::class)
            ->setMethods(['isMallAdmin'])
            ->getMock()
        ;

        $user->expects($this->once())->method('isMallAdmin')->will($this->returnValue(true));

        $basket = oxNew(Basket::class);
        $basket->setNettoSum(1000.0);

        $this->assertEquals(0, $user->oxuser__oxac_oxcoin->value);

        $order = oxNew(Order::class);
        $order->finalizeOrder($basket, $user);

        $this->assertEquals(0, $user->oxuser__oxac_oxcoin->value);
    }
}
