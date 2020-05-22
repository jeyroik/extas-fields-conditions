<?php
namespace extas\components\plugins\fields;

use extas\components\plugins\Plugin;
use extas\interfaces\IItem;
use extas\interfaces\repositories\IRepository;
use extas\interfaces\stages\IStageCreateAfter;

/**
 * Class PluginFieldCreateAfter
 *
 * @package extas\components\plugins\fields
 * @author jeyroik <jeyroik@gmail.com>
 */
class PluginFieldCreateAfter extends Plugin implements IStageCreateAfter
{
    use TPluginFieldCheck;

    protected string $checkName = 'getAfterCreate';

    /**
     * @param IItem $newItem
     * @param IItem|null $sourceItem
     * @param IRepository $repository
     * @throws \Exception
     */
    public function __invoke(IItem &$newItem, IItem $sourceItem, IRepository $repository = null): void
    {
        $this->check($newItem);
    }
}
