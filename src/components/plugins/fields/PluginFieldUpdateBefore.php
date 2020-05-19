<?php
namespace extas\components\plugins\fields;

use extas\components\plugins\Plugin;
use extas\interfaces\IItem;

/**
 * Class PluginFieldUpdateBefore
 *
 * @package extas\components\plugins\fields
 * @author jeyroik <jeyroik@gmail.com>
 */
class PluginFieldUpdateBefore extends Plugin
{
    use TPluginFieldCheck;

    protected string $checkName = 'getBeforeUpdate';

    /**
     * @param IItem $item
     * @param array $where
     * @throws \Exception
     */
    public function __invoke(IItem $item, array $where): void
    {
        $this->check($item);
    }
}
