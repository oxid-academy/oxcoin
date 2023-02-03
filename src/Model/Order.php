<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidAcademy\OxCoin\Model;

use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Core\Field;

/**
 * Class Order
 * @package OxidAcademy\OxCoin\Application\Model
 */
class Order extends Order_parent
{
    /**
     * @param Basket $basket
     * @param $user
     * @param bool $recalculatingOrder
     * @return int
     */
    public function finalizeOrder(Basket $basket, $user, $recalculatingOrder = false)
    {
        $orderState = parent::finalizeOrder($basket, $user, $recalculatingOrder);

        if (!$user->isMallAdmin()) {
            $earnedCoins = $basket->getNettoSum() / 1000.0;
            $coinsSum = (float) $user->getFieldData('oxacoxcoin') + $earnedCoins;
            $user->assign(
                [
                   'oxacoxcoin' => $coinsSum
                ]
            );
            $user->save();
        }

        return $orderState;
    }
}
