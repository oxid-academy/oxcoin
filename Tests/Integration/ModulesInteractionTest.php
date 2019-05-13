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
 * Date: 06.05.19
 * Time: 08:34
 */

namespace OxidAcademy\OxCoin\Tests\Integration;

use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Model\BaseModel;
use OxidEsales\Eshop\Core\Module\Module;
use OxidEsales\Eshop\Core\Module\ModuleList;

class ModulesInteractionTest extends \OxidEsales\TestingLibrary\UnitTestCase
{
    /**
     * The example module oxacFeeFreePayment will kick out this example module oxCoin from the payment list as oxCoin
     * has a fee.
     * To simulate the integration tests we can switch if the FeeFreePayment module should be active or not. If it is
     * active, the Integration test will fail (correctly).
     *
     * @throws \Exception
     */
    protected function helperActivateAllModules($activateFeeFreePayment = false)
    {
        $moduleDirectory = $this->getConfig()->getModulesDir();
        $moduleList = oxNew(ModuleList::class);
        $listOfAllModules = $moduleList->getModulesFromDir($moduleDirectory);

        foreach ($listOfAllModules as $module) {

            /** @var Module $module */
            if (
                !$module->isActive() &&
                (
                    $module->getId() != 'oxac/feefreepayments' ||
                    $activateFeeFreePayment
                )
            ) {
                parent::_getModuleLoader()->installModule($module->getModulePath());
            }
        }
    }

    public function testPaymentListProvidedByThePaymentController()
    {
        $this->helperActivateAllModules();


        $_POST['sShipSet'] = 'oxidstandard';

        // <Creating Shipping Cost Rules>
        $delivery = oxNew(\OxidEsales\Eshop\Application\Model\Delivery::class);
        $delivery->setId('_delivery_test_id');
        $delivery->oxdelivery__oxactive = new Field(1);
        $delivery->oxdelivery__oxparamend = new Field(999);
        $delivery->save();

        $del2delset = oxNew(BaseModel::class);
        $del2delset->init('oxdel2delset');
        $del2delset->oxdel2delset__oxdelid = new Field($delivery->getId());
        $del2delset->oxdel2delset__oxdelsetid = new Field('oxidstandard');
        $del2delset->save();
        // </Creating Shipping Cost Rules>


        // Assigning the payment to the shipping method oxidstandard.
        $delset2payment = oxNew(BaseModel::class);
        $delset2payment->init('oxobject2payment');
        $delset2payment->oxobject2payment__oxpaymentid = new Field('oxcoin');
        $delset2payment->oxobject2payment__oxobjectid = new Field('oxidstandard');
        $delset2payment->oxobject2payment__oxtype = new Field('oxdelset');
        $delset2payment->save();


        // <Creating a basket>
        $price = oxNew(\OxidEsales\Eshop\Core\Price::class);
        $price->setPrice(1.0);

        $article = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
        $article->setId('test_665');
        $article->setPrice($price);
        $article->save();

        $basket = oxNew(\OxidEsales\Eshop\Application\Model\Basket::class);
        $basket->addToBasket($article->getId(), 1.0);

        \OxidEsales\Eshop\Core\Registry::getSession()->setBasket($basket);
        // </Creating a basket>


        // Creating a user
        $user = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
        $user->setId('_user_test_id');
        $user->oxusers__oxusername = new Field('_user_test_username');
        $user->save();


        // Creating a PaymentController object and inject the user object.
        $controller = oxNew(\OxidEsales\Eshop\Application\Controller\PaymentController::class);
        $controller->setUser($user);


        // From all payments at least our payment method oxcoin must be in the list.
        $ids = [];
        foreach ($controller->getPaymentList() as $payment) {
            $ids[] = $payment->oxpayments__oxid->value;
        }

        $this->assertContains('oxcoin', $ids);
    }
}
