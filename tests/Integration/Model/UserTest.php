<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidAcademy\OxCoin\Tests\Unit\Model;

use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

class UserTest extends IntegrationTestCase
{
    private const USER_ID = '_test_user';

    public function testTrackOxAcCoins()
    {
        $this->addExampleUser();

        $user = $this->loadExampleUser();
        $this->assertEquals(0, $user->getOxAcCoins());

        $this->assertTrue($user->trackOxAcCoins(1.23));
        $this->assertEqualsWithDelta(1.23, $user->getOxAcCoins(), 1);

        $this->assertTrue($user->trackOxAcCoins(3.0));
        $this->assertEqualsWithDelta(4.23, $user->getOxAcCoins(), 1);

        $this->assertTrue($user->trackOxAcCoins(-2.0));
        $this->assertEqualsWithDelta(2.23, $user->getOxAcCoins(), 1);
    }

    private function addExampleUser(): void
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

    private function loadExampleUser(): User
    {
        $user = oxNew(User::class);
        $user->load(self::USER_ID);

        return $user;
    }
}
