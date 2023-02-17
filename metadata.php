<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Metadata version
 */
$sMetadataVersion = '2.1';

/**
 * Module information
 */
$aModule = [
    'id'           => 'oxac_oxcoin',
    'title'        => 'OXID Coin',
    'description'  => [
        'de' => 'Modul f&uuml;r die Zahlung mit der Kryptow&auml;hrung OXID Coin.',
        'en' => 'Module for the payment with the crypto currency OXID Coin.',
    ],
    'thumbnail'    => 'pictures/logo.png',
    'version'      => '3.0.0',
    'author'       => 'OXID Academy',
    'url'          => 'https://www.oxid-esales.com/academy/schulungen',
    'email'        => 'academy@oxid-esales.com',
    'events'       => [
        'onActivate'    => '\OxidAcademy\OxCoin\Core\Events::onActivate',
        'onDeactivate'  => '\OxidAcademy\OxCoin\Core\Events::onDeactivate'
    ],
    'extend' => [
        \OxidEsales\Eshop\Application\Model\User::class => \OxidAcademy\OxCoin\Model\User::class,
        \OxidEsales\Eshop\Application\Model\Order::class => \OxidAcademy\OxCoin\Model\Order::class
    ],
];
