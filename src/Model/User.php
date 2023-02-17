<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidAcademy\OxCoin\Model;

use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Core\Field;

/**
 * User model extension
 *
 * @mixin Order
 * @eshopExtension
 */
class User extends User_parent
{
    public function trackOxAcCoins(float $earnedCoins): bool
    {  
        if (!$this->isLoaded()) {
            return false;
        }

        $coinsSum = (float) $this->getFieldData('oxacoxcoin') + $earnedCoins;
        $this->assign(
            [
                'oxacoxcoin' => $coinsSum
            ]
        );

        return $this->save();
    }
}
