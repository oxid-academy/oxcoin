<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidAcademy\OxCoin\Tests\Integration;

use OxidAcademy\OxCoin\Core\Events;
use OxidEsales\Eshop\Application\Controller\PaymentController;
use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Application\Model\Delivery;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Model\BaseModel;
use OxidEsales\Eshop\Core\Price;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\TestingLibrary\UnitTestCase as TestCase;


class ModulesInteractionTest extends TestCase
{
    /**
     * Will be fired every time before executing a test method.
     */
    protected function setUp(): void
    {
        parent::setUp();

        Events::onActivate();
    }

    public function testPaymentListProvidedByThePaymentController()
    {
        $_POST['sShipSet'] = 'oxidstandard';

        // <Creating Shipping Cost Rules>
        $delivery = oxNew(Delivery::class);
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
        $price = oxNew(Price::class);
        $price->setPrice(1.0);

        $article = oxNew(Article::class);
        $article->setId('test_665');
        $article->setPrice($price);
        $article->save();

        $basket = oxNew(Basket::class);
        $basket->addToBasket($article->getId(), 1.0);

        Registry::getSession()->setBasket($basket);
        // </Creating a basket>


        // Creating a user
        $user = oxNew(User::class);
        $user->setId('_user_test_id');
        $user->oxusers__oxusername = new Field('_user_test_username');
        $user->save();


        // Creating a PaymentController object and inject the user object.
        $controller = oxNew(PaymentController::class);
        $controller->setUser($user);


        // From all payments at least our payment method oxcoin must be in the list.
        $ids = [];
        foreach ($controller->getPaymentList() as $payment) {
            $ids[] = $payment->oxpayments__oxid->value;
        }

        $this->assertContains('oxcoin', $ids);
    }
}
