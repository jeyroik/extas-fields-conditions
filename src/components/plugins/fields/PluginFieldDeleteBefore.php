<?php
namespace extas\components\plugins\fields;

/**
 * Class PluginFieldDeleteBefore
 *
 * @package extas\components\plugins\fields
 * @author jeyroik <jeyroik@gmail.com>
 */
class PluginFieldDeleteBefore extends PluginFieldUpdateBefore
{
    protected string $checkName = 'getBeforeDelete';
}
