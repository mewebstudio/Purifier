<?php

namespace Mews\Tests\Purifier;

use GrahamCampbell\TestBenchCore\ServiceProviderTrait;
use Mews\Purifier\Purifier;

class ServiceProviderTest extends AbstractTestCase
{
    use ServiceProviderTrait;

    public function testPurifierIsInjectable()
    {
        $this->assertIsInjectable(Purifier::class);
    }
}