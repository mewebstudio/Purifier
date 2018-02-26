<?php

namespace Mews\Tests\Purifier;

use HTMLPurifier;
use Illuminate\Config\Repository;
use Illuminate\Filesystem\Filesystem;
use Mews\Purifier\Purifier;

class PurifierTest extends AbstractTestCase
{
    public function testConstruct()
    {
        $purifier = $this->app->make('purifier');
        $this->assertInstanceOf(Purifier::class, $purifier);
        $this->assertAttributeInstanceOf(HTMLPurifier::class, 'purifier', $purifier);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Configuration parameters not loaded!
     */
    public function testExpectionIsThrownWhenConfigIsBad()
    {
        new Purifier(new Filesystem(), new Repository());
    }

    public function testConfigureMethod()
    {
        $purifier = $this->app->make('purifier');
        $this->assertInstanceOf(\HTMLPurifier_Config::class, $this->invokeMethod($purifier, 'configure', [\HTMLPurifier_Config::createDefault()]));
    }

    public function testGetConfigMetthod()
    {
        $purifier = $this->app->make('purifier');
        $config = $this->invokeMethod($purifier, 'getConfig', [null]);

        $expectedConfig = [
            'Core.Encoding'               => 'UTF-8',
            'Cache.SerializerPath'        => storage_path('app/purifier'),
            'Cache.SerializerPermissions' => 493,
            'HTML.Doctype'                => 'HTML 4.01 Transitional',
            'HTML.Allowed'                => "div,b,strong,i,em,a[href|title],ul,ol,li,p[style],br,span[style],img[width|height|alt|src]",
            'CSS.AllowedProperties'       => 'font,font-size,font-weight,font-style,font-family,text-decoration,padding-left,color,background-color,text-align',
            'AutoFormat.AutoParagraph'    => true,
            'AutoFormat.RemoveEmpty'      => true,
        ];

        $this->assertSame($expectedConfig, $config);
        $config = $this->invokeMethod($purifier, 'getConfig', ['Core.Encoding']);

        $expectedConfig = [
            'Core.Encoding'               => 'UTF-8',
            'Cache.SerializerPath'        => storage_path('app/purifier'),
            'Cache.SerializerPermissions' => 493,
        ];
        $this->assertSame($expectedConfig, $config);
    }

    public function testGetInstanceMethod()
    {
        $purifier = $this->app->make('purifier');
        $this->assertInstanceOf(HTMLPurifier::class, $purifier->getInstance());
    }

    public function testCleaning()
    {
        $purifier = $this->app->make('purifier');
        $html = '<b>Simple and short';
        $pureHtml = $purifier->clean($html);
        $this->assertSame('<p><b>Simple and short</b></p>', $pureHtml);

        $html = [
            '<script>alert(\'XSS\');</script>',
            '<b>Simple and short',
        ];
        $pureHtml = $purifier->clean($html);
        $this->assertSame(['', '<p><b>Simple and short</b></p>'], $pureHtml);
    }
}