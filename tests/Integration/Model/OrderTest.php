<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidAcademy\OxCoin\Tests\Unit\Model;

use OxidAcademy\OxCoin\Core\Events;
use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Field;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    private const USER_ID = '_test_user';

    /**
     * Will be fired every time before executing a test method.
     */
    protected function setUp(): void
    {
        parent::setUp();

        Events::onActivate();

        $user = oxNew(User::class);
        $user->setId(self::USER_ID);
        $user->assign(
            [
                'username' => 'testuser@oxid-academy.com',
                'oxrights' => 'user'
            ]
        );
        $user->save();
    }

    /**
     * @group finalizeOrder
     */
    public function testFinalizeOrderEarnCoinWhenUserIsCustomer()
    {
        $user = oxNew(User::class);
        $user->load(self::USER_ID);

        $basket = oxNew(Basket::class);
        $basket->setNettoSum(1000.0);

        $this->assertEquals(0, $user->getFieldData('oxacoxcoin'));

        $order = oxNew(Order::class);
        $order->finalizeOrder($basket, $user);

        $this->assertEquals(1, $user->getFieldData('oxacoxcoin'));
    }

    /**
     * @group finalizeOrder
     */
    public function testFinalizeOrderDoNotEarnCoinWhenUserIsMallAdmin()
    {
        $user = $this
            ->getMockBuilder(User::class)
            ->onlyMethods(['isMallAdmin'])
            ->getMock();

        $user->expects($this->any())
            ->method('isMallAdmin')
            ->will($this->returnValue(true));

        $user->load(self::USER_ID);

        $basket = oxNew(Basket::class);
        $basket->setNettoSum(1000.0);

        $this->assertEquals(0, $user->getFieldData('oxacoxcoin'));

        $order = oxNew(Order::class);
        $order->finalizeOrder($basket, $user);

        $this->assertEquals(0, $user->getFieldData('oxacoxcoin'));
    }
}
