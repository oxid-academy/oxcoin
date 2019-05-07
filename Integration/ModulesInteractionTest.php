<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 06.05.19
 * Time: 08:34
 */

namespace OxidAcademy\OxCoin\Tests\Integration;

use OxidEsales\Eshop\Application\Controller\PaymentController;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Module\Module;
use OxidEsales\TestingLibrary\ModuleLoader;
use OxidEsales\TestingLibrary\UnitTestCase;

class ModulesInteractionTest extends UnitTestCase
{
    /**
     * The example module oxacFeeFreePayment will kick out this example module oxCoin from the payment list as oxCoin
     * has a fee.
     * To simulate the integration tests we can switch if the FeeFreePayment module should be active or not. If it is
     * active, the Integration test will fail (correctly).
     *
     * @param bool $activateFeeFreePayment
     */
    protected function helperActivateAllModules($activateFeeFreePayment = false)
    {
        // Get the module Directory
        $moduleDirectory = $this->getConfig()->getModulesDir();

        //Build a list with all modules
        $moduleList = oxNew(\OxidEsales\Eshop\Core\Module\ModuleList::class);
        $modules = $moduleList->getModulesFromDir($moduleDirectory);


        // Build an array with the vendor id and the module id. (e.g. oe/gdproptin)
        $modulesToActivate = [];
        foreach ($modules as $module) {
            /** @var Module $module */
            if ($module->getId() != 'oxacfeefreepayments') {
                $modulesToActivate[] = $module->getModulePath();
            }
        }

        // Activate all collected modules
        $moduleLoader = new ModuleLoader();
        $moduleLoader->activateModules($modulesToActivate);
    }

    public function testPaymentListProvidedByThePaymentController()
    {
        $this->helperActivateAllModules();


        $_POST['sShipSet'] = 'oxidstandard';

        // Creating a user object for the payment list
        $user = oxNew(User::class);
        $user->load('oxdefaultadmin');

        $controller = oxNew(PaymentController::class);
        $controller->setUser($user);



        $ids = [];
        foreach ($controller->getPaymentList() as $payment) {
            $ids[] = $payment->oxpayments__oxid->value;
        }

        $this->assertContains('oxcoin', $ids);
    }
}