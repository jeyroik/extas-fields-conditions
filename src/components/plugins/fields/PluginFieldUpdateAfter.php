<?php
namespace extas\components\plugins\fields;

use extas\components\plugins\Plugin;
use extas\interfaces\IItem;

/**
 * Class PluginFieldUpdateAfter
 *
 * @package extas\components\plugins\fields
 * @author jeyroik <jeyroik@gmail.com>
 */
class PluginFieldUpdateAfter extends Plugin
{
    use TPluginFieldCheck;

    protected string $checkName = 'getAfterUpdate';

    /**
     * @param bool $result
     * @param array $where
     * @param IItem $item
     * @throws \Exception
     */
    public function __invoke(bool $result, array $where, IItem $item): void
    {
        $this->check($item);
    }
}
