<?php

namespace Mews\Tests\Purifier;

use HTMLPurifier;
use Illuminate\Config\Repository;
use Illuminate\Filesystem\Filesystem;
use Mews\Purifier\Purifier;
use Mews\Purifier\PurifierConfigBuilder;

class PurifierTest extends AbstractTestCase
{
    public function setUp()
    {
        parent::setUp();

        // Clean Purifier Definitions cache
        array_map('unlink', glob($this->getBasePath() . '/storage/app/purifier/HTML/*'));
        array_map('unlink', glob($this->getBasePath() . '/storage/app/purifier/CSS/*'));
        array_map('unlink', glob($this->getBasePath() . '/storage/app/purifier/URI/*'));
    }

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
        $configBuilder = $this->app->make(PurifierConfigBuilder::class);
        new Purifier($configBuilder, new Filesystem(), new Repository());
    }

    public function testGetConfigMethod()
    {
        $configBuilder = $this->app->make(PurifierConfigBuilder::class);
        /** @var \HTMLPurifier_Config $config */
        $config = $this->invokeMethod($configBuilder, 'getConfig', [null]);

        $this->assertEquals('utf-8', $config->get('Core.Encoding'));
        $this->assertEquals(storage_path('app/purifier'), $config->get('Cache.SerializerPath'));
        $this->assertEquals(493, $config->get('Cache.SerializerPermissions'));
        $this->assertEquals('HTML 4.01 Transitional', $config->get('HTML.Doctype'));
        $this->assertEquals('div,b,strong,i,em,u,a[href|title],ul,ol,li,p[style],br,span[style],img[width|height|alt|src],section,nav,article,aside,header,footer,address,hgroup,figure,figcaption,video,source,s,var,sub,sup,mark,wbr,ins,del,u', $config->get('HTML.Allowed'));
        $this->assertEquals(explode(',', 'font,font-size,font-weight,font-style,font-family,text-decoration,padding-left,color,background-color,text-align'), array_keys($config->get('CSS.AllowedProperties')));
        $this->assertEquals(true, $config->get('AutoFormat.AutoParagraph'));
        $this->assertEquals(true, $config->get('AutoFormat.RemoveEmpty'));

        $config = $this->invokeMethod($configBuilder, 'getConfig', ['Core.Encoding']);

        $this->assertEquals('utf-8', $config->get('Core.Encoding'));
        $this->assertEquals(storage_path('app/purifier'), $config->get('Cache.SerializerPath'));
        $this->assertEquals(493, $config->get('Cache.SerializerPermissions'));
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

    public function testCleaningWithCustomConfig()
    {
        $purifier = $this->app->make('purifier');

        $html = '<span id="some-id">This is my H1 title';
        $pureHtml = $purifier->clean($html, ['Attr.EnableID' => true]);
        $this->assertSame('<span id="some-id">This is my H1 title</span>', $pureHtml);

        $html = '<span id="some-id">This is my H1 title';
        $pureHtml = $purifier->clean($html, 'test');
        $this->assertSame('<span id="some-id">This is my H1 title</span>', $pureHtml);
    }

    public function testCustomDefinitions()
    {
        /** @var HTMLPurifier $purifier */
        $purifier = $this->app->make('purifier');

        $html = '<section>HTML5 section tag</section>';
        $pureHtml = $purifier->clean($html);

        $this->assertSame('<section>HTML5 section tag</section>', $pureHtml);
    }
}