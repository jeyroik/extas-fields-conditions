<?php
namespace extas\components\plugins\fields;

use extas\components\plugins\Plugin;

/**
 * Class PluginFieldCreateBefore
 *
 * @package extas\components\plugins\fields
 * @author jeyroik <jeyroik@gmail.com>
 */
class PluginFieldCreateBefore extends Plugin
{
    use TPluginFieldCheck;

    protected string $checkName = 'getBeforeCreate';
}
