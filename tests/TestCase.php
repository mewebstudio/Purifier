<?php namespace Mews\Tests;

use Mockery;

class TestCase extends \Illuminate\Foundation\Testing\TestCase
{
    public function createApplication()
    {
        $app = require __DIR__ . '/../bootstrap/app.php';

        return $app;
    }

    public function tearDown()
    {
        parent::tearDown();
        Mockery::close();
    }
}
