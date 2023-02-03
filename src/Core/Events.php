<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidAcademy\OxCoin\Core;

use OxidEsales\Eshop\Application\Model\Payment;
use OxidEsales\Eshop\Core\Model\BaseModel;

/**
 * Class Events
 * @package OxidAcademy\OxCoin\Core
 */
class Events
{
    private const PAYMENT_RELATION_OXID = '_oxcoin2oxidstandard';

    /**
     * Execute action on activate event
     */
    public static function onActivate()
    {
        // Add record to table oxpayment
        self::addActivePaymentMethod();

        // Adding the payment to a shipping method
        self::assignPaymentMethodToDefaultShippingMethod();
    }

    /**
     * Execute actions on deactivate event
     *
     * 1. Deactivates the payment method.
     */
    public static function onDeactivate()
    {
        self::deactivatePaymentMethod();
    }

    /**
     * Add payment method and set default values.
     */
    public static function addActivePaymentMethod()
    {
        $payment = oxNew(Payment::class);
        if (!$payment->load('oxcoin')) {
            $payment->setId('oxcoin');
            $payment->assign(
                [
                    'oxactive' => 1,
                    'oxdesc' => 'oxCoin',
                    'oxaddsum' => 0.01,
                    'oxaddsumtype' => 'abs',
                    'oxfromboni' => 0,
                    'oxfromamount' => 0,
                    'oxtoamount' => 10000
                ]
            );

            $payment->save();
        }
    }

    /**
     * Adds the payment to the default shipping set "oxidstandard".
     *
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseConnectionException
     */
    public static function assignPaymentMethodToDefaultShippingMethod()
    {
        $object2Payment = oxNew(BaseModel::class);
        $object2Payment->init('oxobject2payment');

        if (!$object2Payment->load(self::PAYMENT_RELATION_OXID)) {
            $object2Payment->assign(
                [
                    'oxid' => self::PAYMENT_RELATION_OXID,
                    'oxpaymentid' => 'oxcoin',
                    'oxobjectid' => 'oxidstandard',
                    'oxtype' => 'oxdelset'
                ]
            );
            $object2Payment->save();
        }
    }

    /**
     * Deactivate payment method
     */
    public static function deactivatePaymentMethod()
    {
        $payment = oxNew(Payment::class);
        $payment->load('oxcoin');
        $payment->assign(
            [
                'oxactive' => 0
            ]
        );
        $payment->save();
    }
}
