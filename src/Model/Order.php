<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidAcademy\OxCoin\Model;

use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Application\Model\Order as EshopModelOrder;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidAcademy\OxCoin\Service\Coin as CoinService;

/**
 * Order model extension
 *
 * @mixin EshopModelOrder
 * @eshopExtension
 */
class Order extends Order_parent
{
    /**
     * @param Basket $basket
     * @param User   $user
     * @param bool   $recalculatingOrder
     * @return int
     */
    public function finalizeOrder(Basket $basket, $user, $recalculatingOrder = false)
    {
        $orderState = parent::finalizeOrder($basket, $user, $recalculatingOrder);

        if (!$recalculatingOrder) {
            ContainerFactory::getInstance()
                ->getContainer()
                ->get(CoinService::class)
                ->trackCoins($basket, $user);
        }

        return $orderState;
    }
}
