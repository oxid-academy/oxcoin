<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidAcademy\OxCoin\Tests\Integration\Model;

use OxidAcademy\OxCoin\Core\Events;
use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

class OrderTest extends BaseTest
{
    public function testFinalizeOrderEarnCoin()
    {
        $user = $this->loadExampleUser();

        $this->assertEqualsWithDelta(0, $user->getOxAcCoins(), 0.001);

        $basket = oxNew(Basket::class);
        $basket->setNettoSum(1200.0);

        $order = oxNew(Order::class);
        $order->finalizeOrder($basket, $user);

        $this->assertEqualsWithDelta(1.2, $user->getOxAcCoins(), 0.001);
    }

    public function testFinalizeOrderWithoutBasketUser()
    {
        //this user is not existingin database (not loaded, no oxid)
        $user = oxNew(User::class);
        $this->assertEqualsWithDelta(0, $user->getOxAcCoins(), 0.001);

        $basket = oxNew(Basket::class);
        $basket->setNettoSum(600.0);

        $order = oxNew(Order::class);
        $order->finalizeOrder($basket, $user);

        $this->assertEqualsWithDelta(0, $user->getOxAcCoins(), 0.001);
    }
}
