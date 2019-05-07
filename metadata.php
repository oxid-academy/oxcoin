<?php

/**
 * Metadata version
 */
$sMetadataVersion = '2.1';

/**
 * Module information
 */
$aModule = [
    'id'           => 'oxac\coin',
    'title'        => 'OXID Coin',
    'description'  => [
        'de' => 'Modul für die Zahlung mit der Kryptowährung OXID Coin.',
        'en' => 'Module for the payment with the crypto currency OXID Coin.',
    ],
    'thumbnail'    => 'logo.png',
    'version'      => '1.0.1',
    'author'       => 'OXID Academy',
    'url'          => 'https://www.oxid-esales.com/oxid-welt/academy/schulungen/',
    'email'        => 'academy@oxid-esales.com',
    'events'       => [
        'onActivate' => '\OxidAcademy\OxCoin\Core\Events::onActivate',
        'onDeactivate' => '\OxidAcademy\OxCoin\Core\Events::onDeactivate'
    ],
];
