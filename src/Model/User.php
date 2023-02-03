<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidAcademy\OxCoin\Model;

use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Application\Model\User as EshopModelUser;
use OxidEsales\Eshop\Core\Field;

/**
 * User model extension
 *
 * @mixin EshopModelUser
 * @eshopExtension
 */
class User extends User_parent
{
    public function trackOxAcCoins(float $earnedCoins): bool
    {  
        if (!$this->isLoaded()) {
            return false;
        }

        $coinsSum = $this->getOxAcCoins() + $earnedCoins;

        return $this->saveOxAcCoins($coinsSum);
    }

    public function getOxAcCoins(): float
    {
        return (float) $this->getFieldData('oxacoxcoin');
    }

    protected function saveOxAcCoins(float $coins): bool
    {
        $this->assign(
            [
                'oxacoxcoin' => $coins
            ]
        );

        return (bool) $this->save();
    }
}
