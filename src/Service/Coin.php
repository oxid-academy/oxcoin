<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidAcademy\OxCoin\Service;

use \OxidEsales\Eshop\Application\Model\User;
use \OxidEsales\Eshop\Application\Model\Basket;


final class Coin
{
    private float $rate;

    public function __construct(float $rate)
    {
        $this->rate = $rate;
    }

    public function trackCoins(Basket $basket, User $user = null): bool
    {
        $result = false;
        if (!$user || $user->isMallAdmin()) {
            return $result;
        }

        $earnedCoins = $basket->getNettoSum() / $this->rate;

        return $user->trackOxAcCoins($earnedCoins);
    }
}
