<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidAcademy\OxCoin\Tests\Unit;

use OxidAcademy\OxCoin\Service\Coin;
use OxidEsales\Eshop\Application\Model\Basket as EshopModelBasket;
use OxidEsales\Eshop\Application\Model\User as EshopModelUser;
use OxidEsales\TestingLibrary\UnitTestCase as TestCase;

class CoinTest extends TestCase
{
    public function testTrackingSuccess(): void
    {
        $user = $this->getUserMock(1.0);
        $basket = $this->getBasketMock( 100.0);
        $service = new Coin(100.0);

        $this->assertTrue($service->trackCoins($basket, $user));
    }

    public function testTrackingFailure(): void
    {
        $user = $this->getUserMock(3.0, 'once', false);
        $basket = $this->getBasketMock( 150.0);
        $service = new Coin(50.0);

        $this->assertFalse($service->trackCoins($basket, $user));
    }

    public function testTrackingFailureWithoutUser(): void
    {
        $basket = $this->getBasketMock( 150.0);
        $service = new Coin(50.0);

        $this->assertFalse($service->trackCoins($basket));
    }

    public function testTrackingFailureForMallAdmin(): void
    {
        $user = $this->getUserMock(3.0, 'never');
        $user->expects($this->any())
            ->method('isMallAdmin')
            ->willReturn(true);
        $basket = $this->getBasketMock( 150.0);
        $service = new Coin(50.0);

        $this->assertFalse($service->trackCoins($basket, $user));
    }

    private function getUserMock(
        float $coins,
        string $invoked = 'once',
        bool $success = true
    ): EshopModelUser
    {
        $user = $this->getMockBuilder(EshopModelUser::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['isMallAdmin', 'trackOxAcCoins'])
            ->getMock();
        $user->expects($this->$invoked())
            ->method('trackOxAcCoins')
            ->with($this->equalTo($coins))
            ->willReturn($success);

        return $user;
    }

    private function getBasketMock(float $sum): EshopModelBasket
    {
        $basket = $this->getMockBuilder(EshopModelBasket::class)
            ->disableOriginalConstructor()
            ->getMock();
        $basket->expects($this->any())
            ->method('getNettoSum')
            ->willReturn($sum) ;

        return $basket;
    }
}
