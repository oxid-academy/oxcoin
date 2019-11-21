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
 * Time: 08:19
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

    }
}
