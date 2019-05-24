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
 */

/**
 * Metadata version
 */
$sMetadataVersion = '2.1';

/**
 * Module information
 */
$aModule = [
    'id'           => 'oxac/coin',
    'title'        => 'OXID Coin',
    'description'  => [
        'de' => 'Modul f&uuml;r die Zahlung mit der Kryptow&auml;hrung OXID Coin.',
        'en' => 'Module for the payment with the crypto currency OXID Coin.',
    ],
    'thumbnail'    => 'logo.png',
    'version'      => '1.0.2',
    'author'       => 'OXID Academy',
    'url'          => 'https://www.oxid-esales.com/oxid-welt/academy/schulungen/',
    'email'        => 'academy@oxid-esales.com',
    'events'       => [
        'onActivate' => '\OxidAcademy\OxCoin\Core\Events::onActivate',
        'onDeactivate' => '\OxidAcademy\OxCoin\Core\Events::onDeactivate'
    ],
    'extend' => [
        \OxidEsales\Eshop\Application\Model\Order::class => \OxidAcademy\OxCoin\Application\Model\Order::class
    ],
];
