<?php
namespace extas\components\plugins\fields;

/**
 * Class PluginFieldDeleteAfter
 *
 * @package extas\components\plugins\fields
 * @author jeyroik <jeyroik@gmail.com>
 */
class PluginFieldDeleteAfter extends PluginFieldUpdateAfter
{
    protected string $checkName = 'getAfterDelete';
}
