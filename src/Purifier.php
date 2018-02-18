<?php

namespace Mews\Purifier;

/**
 * Laravel 5 HTMLPurifier package
 *
 * @copyright Copyright (c) 2015 MeWebStudio
 * @version   2.0.0
 * @author    Muharrem ERİN
 * @contact me@mewebstudio.com
 * @web http://www.mewebstudio.com
 * @date      2014-04-02
 * @license   MIT
 */

use Exception;
use HTMLPurifier;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Filesystem\Filesystem;

class Purifier
{
    /**
     * @var \Mews\Purifier\PurifierConfigBuilder
     */
    protected $configBuilder;

    /**
     * @var Filesystem
     */
    protected $files;

    /**
     * @var Repository
     */
    protected $config;

    /**
     * @var HTMLPurifier
     */
    protected $purifier;

    /**
     * Constructor
     *
     * @param \Mews\Purifier\PurifierConfigBuilder $configBuilder
     * @param Filesystem $files
     * @param Repository $config
     * @throws Exception
     */
    public function __construct(PurifierConfigBuilder $configBuilder, Filesystem $files, Repository $config)
    {
        $this->configBuilder = $configBuilder;
        $this->config = $config;
        $this->files = $files;

        $this->setUp();
    }

    /**
     * Setup
     *
     * @throws Exception
     */
    private function setUp()
    {
        if (!$this->config->has('purifier')) {
            throw new Exception('Configuration parameters not loaded!');
        }

        $this->checkCacheDirectory();

        $config = $this->configBuilder->getConfig();

        // Create HTMLPurifier object
        $this->purifier = new HTMLPurifier($config);
    }

    /**
     * Check/Create cache directory
     */
    private function checkCacheDirectory()
    {
        $cachePath = $this->config->get('purifier.cachePath');

        if ($cachePath) {
            if (!$this->files->isDirectory($cachePath)) {
                $this->files->makeDirectory($cachePath, $this->config->get('purifier.cacheFileMode', 0755));
            }
        }
    }

    /**
     * @param      $dirty
     * @param null $config
     * 
     * @return mixed
     */
    public function clean($dirty, $config = null)
    {
        if (is_array($dirty)) {
            return array_map(function ($item) use ($config) {
                return $this->clean($item, $config);
            }, $dirty);
        }

        return $this->purifier->purify($dirty, $config ? $this->configBuilder->getConfig($config) : null);
    }

    /**
     * Get HTMLPurifier instance.
     *
     * @return \HTMLPurifier
     */
    public function getInstance()
    {
        return $this->purifier;
    }
}
