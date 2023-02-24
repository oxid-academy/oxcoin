<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidAcademy\OxCoin\Tests\Integration\Model;

class UserTest extends BaseTest
{
    public function testTrackOxAcCoins()
    {
        $user = $this->loadExampleUser();
        $this->assertEquals(0, $user->getOxAcCoins());

        $this->assertTrue($user->trackOxAcCoins(1.23));
        $this->assertEqualsWithDelta(1.23, $user->getOxAcCoins(), 0.001);

        $this->assertTrue($user->trackOxAcCoins(3.0));
        $this->assertEqualsWithDelta(4.23, $user->getOxAcCoins(), 0.001);

        $this->assertTrue($user->trackOxAcCoins(-2.0));
        $this->assertEqualsWithDelta(2.23, $user->getOxAcCoins(), 0.001);
    }
}
