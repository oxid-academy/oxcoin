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

class OrderTest extends UnitTestCase
{
    /**
     * @group finalizeOrder
     */
    public function testFinalizeOrderEarnCoinWhenUserIsCustomer()
    {

    }

    /**
     * @group finalizeOrder
     */
    public function testFinalizeOrderDoNotEarnCoinWhenUserIsMallAdmin()
    {

    }
}
