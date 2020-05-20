<?php
namespace extas\components\plugins\fields;

use extas\components\plugins\Plugin;
use extas\interfaces\IItem;
use extas\interfaces\repositories\IRepository;
use extas\interfaces\stages\IStageUpdateAfter;

/**
 * Class PluginFieldUpdateAfter
 *
 * @package extas\components\plugins\fields
 * @author jeyroik <jeyroik@gmail.com>
 */
class PluginFieldUpdateAfter extends Plugin implements IStageUpdateAfter
{
    use TPluginFieldCheck;

    protected string $checkName = 'getAfterUpdate';

    /**
     * @param bool $result
     * @param array $where
     * @param IItem $item
     * @param IRepository $itemRepository
     * @throws \Exception
     */
    public function __invoke(bool &$result, array $where, IItem $item, IRepository $itemRepository): void
    {
        if (empty($where)) {
            $this->check($item);
        } else {
            $items = $itemRepository->all($where);
            foreach ($items as $itemWhere) {
                $this->check($itemWhere);
            }
        }
    }
}
