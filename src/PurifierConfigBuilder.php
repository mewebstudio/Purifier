<?php
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

namespace Mews\Purifier;

use HTMLPurifier_Config;
use HTMLPurifier_HTMLDefinition;
use Illuminate\Contracts\Config\Repository;

class PurifierConfigBuilder
{
    /**
     * @var \Illuminate\Contracts\Config\Repository
     */
    private $config;

    /**
     * PurifierConfig constructor.
     *
     * @param \Illuminate\Contracts\Config\Repository $config
     */
    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    /**
     * @param null $config
     *
     * @return mixed|null
     */
    public function getConfig($config = null)
    {
        // Create a new configuration object
        $configObject = HTMLPurifier_Config::createDefault();

        // Allow configuration to be modified
        if (! $this->config->get('purifier.finalize')) {
            $configObject->autoFinalize = false;
        }

        // Set default config
        $defaultConfig = [];
        $defaultConfig['Core.Encoding'] = $this->config->get('purifier.encoding');
        $defaultConfig['Cache.SerializerPath'] = $this->config->get('purifier.cachePath');
        $defaultConfig['Cache.SerializerPermissions'] = $this->config->get('purifier.cacheFileMode', 0755);

        if (! $config) {
            $config = $this->config->get('purifier.settings.default');
        } elseif (is_string($config)) {
            $config = $this->config->get('purifier.settings.' . $config);
        }

        if (! is_array($config)) {
            $config = [];
        }

        // Merge configurations
        $config = $defaultConfig + $config;

        // Load to Purifier config
        $configObject->loadArray($config);

        // Load custom definition if set
        if ($definitionConfig = $this->config->get('purifier.settings.custom_definition')) {
            $this->addCustomDefinition($definitionConfig, $configObject);
        }

        // Load custom elements if set
        if ($elements = $this->config->get('purifier.settings.custom_elements')) {
            if ($def = $configObject->maybeGetRawHTMLDefinition()) {
                $this->addCustomElements($elements, $def, $configObject);
            }
        }

        // Load custom attributes if set
        if ($attributes = $this->config->get('purifier.settings.custom_attributes')) {
            if ($def = $configObject->maybeGetRawHTMLDefinition()) {
                $this->addCustomAttributes($attributes, $def);
            }
        }

        return $configObject;
    }

    /**
     * Add a custom definition
     *
     * @see http://htmlpurifier.org/docs/enduser-customize.html
     * @param array $definitionConfig
     * @param HTMLPurifier_Config $configObject Defaults to using default config
     *
     * @return HTMLPurifier_Config $configObject
     */
    private function addCustomDefinition(array $definitionConfig, $configObject = null)
    {
        if (! $configObject) {
            $configObject = HTMLPurifier_Config::createDefault();
            $configObject->loadArray($this->getConfig());
        }

        // Setup the custom definition
        $configObject->set('HTML.DefinitionID', $definitionConfig['id']);
        $configObject->set('HTML.DefinitionRev', $definitionConfig['rev']);

        // Enable debug mode
        if (! isset($definitionConfig['debug']) || $definitionConfig['debug']) {
            $configObject->set('Cache.DefinitionImpl', null);
        }

        // Start configuring the definition
        if ($def = $configObject->maybeGetRawHTMLDefinition()) {
            // Create the definition attributes
            if (! empty($definitionConfig['attributes'])) {
                $this->addCustomAttributes($definitionConfig['attributes'], $def);
            }

            // Create the definition elements
            if (! empty($definitionConfig['elements'])) {
                $this->addCustomElements($definitionConfig['elements'], $def, $configObject);
            }
        }

        return $configObject;
    }

    /**
     * Add provided attributes to the provided definition
     *
     * @param array $attributes
     * @param HTMLPurifier_HTMLDefinition $definition
     *
     * @return HTMLPurifier_HTMLDefinition $definition
     */
    private function addCustomAttributes(array $attributes, $definition)
    {
        foreach ($attributes as $attribute) {
            // Get configuration of attribute
            $required = ! empty($attribute[3]) ? true : false;
            $onElement = $attribute[0];
            $attrName = $required ? $attribute[1] . '*' : $attribute[1];
            $validValues = $attribute[2];

            $definition->addAttribute($onElement, $attrName, $validValues);
        }

        return $definition;
    }

    /**
     * Add provided elements to the provided definition
     *
     * @param array $elements
     * @param HTMLPurifier_HTMLDefinition $definition
     * @param HTMLPurifier_Config $configObject
     *
     * @return HTMLPurifier_HTMLDefinition $definition
     */
    private function addCustomElements(array $elements, HTMLPurifier_HTMLDefinition $definition, HTMLPurifier_Config $configObject) {
        $customElements = [];

        foreach ($elements as $element) {
            // Get configuration of element
            $name = $element[0];
            $contentSet = $element[1];
            $allowedChildren = $element[2];
            $attributeCollection = $element[3];
            $attributes = isset($element[4]) ? $element[4] : null;

            $customElements[] = $name;

            if (! empty($attributes)) {
                $definition->addElement($name, $contentSet, $allowedChildren, $attributeCollection, $attributes);
            } else {
                $definition->addElement($name, $contentSet, $allowedChildren, $attributeCollection);
            }
        }

        // There is no custom elements to append to allowed - return
        if (empty($customElements)) {
            return;
        }

        // We must append custom elements to the list of allowed elements if it is provided
        if ($allowed = $this->getAllowedProperty($configObject)) {
            $allowed = $allowed . ',' . implode(',', $customElements);

            $this->setAllowedProperty($allowed, $configObject);
        }
    }

    /**
     * @param \HTMLPurifier_Config $configObject
     * @return mixed
     */
    private function getAllowedProperty(HTMLPurifier_Config $configObject)
    {
        if (null !== $configObject->get('HTML.Allowed')) {
            return $configObject->get('HTML.Allowed');
        }

        if (null !== $configObject->get('HTML.AllowedElements')) {
            $allowedArray = $configObject->get('HTML.AllowedElements');

            return implode(',', array_keys($allowedArray));
        }

        return;
    }

    /**
     * @param $allowed
     * @param \HTMLPurifier_Config $configObject
     * @return mixed
     */
    private function setAllowedProperty($allowed, HTMLPurifier_Config $configObject)
    {
        if (null !== $configObject->get('HTML.Allowed')) {
            $this->setPropertyDirectly('HTML.Allowed', $allowed, $configObject);

            return;
        }

        if (null !== $configObject->get('HTML.AllowedElements')) {
            $allowedArray = array_fill_keys(array_keys(array_flip(explode(',', $allowed))), true);
            $this->setPropertyDirectly('HTML.AllowedElements', $allowedArray, $configObject);

            return;
        }

        return;
    }

    /**
     * @param $name
     * @param $value
     * @param \HTMLPurifier_Config $configObject
     */
    private function setPropertyDirectly($name, $value, HTMLPurifier_Config $configObject)
    {
        // We can not use $configObject->set() because it resets definitions
        // when setting properties from HTML namespace
        // So we  use closure to access protected property and set modified value directly
        // Closure much faster than ReflectionProperty
        $plistClosure = \Closure::bind(function ($configObject) {
            return $configObject->plist;
        }, null, HTMLPurifier_Config::class);

        /** @var \HTMLPurifier_PropertyList $plist */
        $plist = $plistClosure($configObject);

        $plist->set($name, $value);
    }
}