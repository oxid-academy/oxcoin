<?php

/**
 * Metadata version
 */
$sMetadataVersion = '2.0';

/**
 * Module information
 */
$aModule = array(
    'id'           => 'oxac\coin',
    'title'        => 'OXID Coin',
    'description'  => array(
        'de' => 'Modul für die Zahlung mit der Kryptow&auml;hrung OXID Coin.',
        'en' => 'Module for the payment with the crypto currency OXID Coin.',
    ),
    'thumbnail'    => 'logo.png',
    'version'      => '1.0.0',
    'author'       => 'OXID Academy',
    'url'          => 'https://www.oxid-esales.com/oxid-welt/academy/schulungen/',
    'email'        => 'academy@oxid-esales.com',
    'events'       => array(
        'onActivate' => '\OxidAcademy\OxCoin\Core\Events::onActivate',
        'onDeactivate' => '\OxidAcademy\OxCoin\Core\Events::onDeactivate'
    ),
);
