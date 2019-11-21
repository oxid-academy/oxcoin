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
 * Date: 23.04.19
 * Time: 08:24
 */

namespace OxidAcademy\OxCoin\Core;

/**
 * Class Events
 * @package OxidAcademy\OxCoin\Core
 */
class Events
{
    /**
     * Execute action on activate event
     */
    public static function onActivate()
    {
        // Adding record to table oxpayment
        self::addPaymentMethod();

        // Activating the payment method
        self::activatePaymentMethod();

        // Adding the payment to a shipping method
        self::assignPaymentMethodToDefaultShippingMethod();

        self::addTableFieldToUserTable();
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
    public static function addPaymentMethod()
    {

    }

    /**
     * Activates payment method
     */
    public static function activatePaymentMethod()
    {

    }

    /**
     * Adds the payment to the default shipping set "oxidstandard".
     *
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseConnectionException
     */
    public static function assignPaymentMethodToDefaultShippingMethod()
    {

    }

    /**
     * Adds the field oxac_oxcoin to the table oxuser.
     *
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseConnectionException
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseErrorException
     */
    public static function addTableFieldToUserTable()
    {

    }

    /**
     * Disables payment method
     */
    public static function deactivatePaymentMethod()
    {

    }

    /**
     * Borrowed from the module oegdproptin
     * @author oegdproptin
     * @link https://github.com/OXID-eSales/gdpr-optin-module
     *
     * Clean temp folder content.
     *
     * @param string $clearFolderPath Sub-folder path to delete from. Should be a full, valid path inside temp folder.
     *
     * @return boolean
     */
    protected static function clearTmp($clearFolderPath = '')
    {
        $folderPath = self::_getFolderToClear($clearFolderPath);
        $directoryHandler = opendir($folderPath);

        if (!empty($directoryHandler)) {
            while (false !== ($fileName = readdir($directoryHandler))) {
                $filePath = $folderPath . DIRECTORY_SEPARATOR . $fileName;
                self::_clear($fileName, $filePath);
            }

            closedir($directoryHandler);
        }

        return true;
    }

    /**
     * Borrowed from the module oegdproptin
     * @author oegdproptin
     * @link https://github.com/OXID-eSales/gdpr-optin-module
     *
     * Check if provided path is inside eShop `tpm/` folder or use the `tmp/` folder path.
     *
     * @param string $clearFolderPath
     *
     * @return string
     */
    protected static function _getFolderToClear($clearFolderPath = '')
    {
        $templateFolderPath = Registry::getConfig()->getConfigParam('sCompileDir');

        if (!empty($clearFolderPath) and (strpos($clearFolderPath, $templateFolderPath) !== false)) {
            $folderPath = $clearFolderPath;
        } else {
            $folderPath = $templateFolderPath;
        }

        return $folderPath;
    }

    /**
     * Borrowed from the module oegdproptin
     * @author oegdproptin
     * @link https://github.com/OXID-eSales/gdpr-optin-module
     *
     * Check if resource could be deleted, then delete it's a file or
     * call recursive folder deletion if it's a directory.
     *
     * @param string $fileName
     * @param string $filePath
     */
    protected static function _clear($fileName, $filePath)
    {
        if (!in_array($fileName, ['.', '..', '.gitkeep', '.htaccess'])) {
            if (is_file($filePath)) {
                @unlink($filePath);
            } else {
                self::clearTmp($filePath);
            }
        }
    }
}
