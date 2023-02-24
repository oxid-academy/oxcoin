<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidAcademy\OxCoin\Tests\Integration\Model;

use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

class BaseTest extends IntegrationTestCase
{
    protected const USER_ID = '_test_user';

    public function setUp(): void
    {
        $this->addExampleUser();
    }

    protected function addExampleUser(): void
    {
        $user = oxNew(User::class);
        $user->assign(
            [
                'oxid' => self::USER_ID,
                'username' => 'testuser@oxid-academy.com',
                'oxrights' => 'user',
                'oxacoxcoin' => 0
            ]
        );
        $user->save();
    }

    protected function loadExampleUser(): User
    {
        $user = oxNew(User::class);
        $user->load(self::USER_ID);

        return $user;
    }
}
