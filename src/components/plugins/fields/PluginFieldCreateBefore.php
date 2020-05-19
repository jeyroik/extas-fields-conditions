<?php
namespace extas\components\plugins\fields;

use extas\components\plugins\Plugin;
use extas\interfaces\IItem;

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

    /**
     * @param IItem $newItem
     * @throws \Exception
     */
    public function __invoke(IItem $newItem)
    {
        $this->check($newItem);
    }
}
