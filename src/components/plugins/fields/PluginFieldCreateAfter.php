<?php
namespace extas\components\plugins\fields;

use extas\components\plugins\Plugin;
use extas\interfaces\IItem;

/**
 * Class PluginFieldCreateAfter
 *
 * @package extas\components\plugins\fields
 * @author jeyroik <jeyroik@gmail.com>
 */
class PluginFieldCreateAfter extends Plugin
{
    use TPluginFieldCheck;

    protected string $checkName = 'getAfterCreate';

    /**
     * @param IItem $newItem
     * @param IItem|null $sourceItem
     * @throws \Exception
     */
    public function __invoke(IItem $newItem, IItem $sourceItem)
    {
        $this->check($newItem);
    }
}
