<?php
namespace extas\components\plugins\fields;

use extas\components\plugins\Plugin;
use extas\interfaces\IItem;
use extas\interfaces\repositories\IRepository;
use extas\interfaces\stages\IStageUpdateBefore;

/**
 * Class PluginFieldUpdateBefore
 *
 * @package extas\components\plugins\fields
 * @author jeyroik <jeyroik@gmail.com>
 */
class PluginFieldUpdateBefore extends Plugin implements IStageUpdateBefore
{
    use TPluginFieldCheck;

    protected string $checkName = 'getBeforeUpdate';

    /**
     * @param IItem|null $item
     * @param array $where
     * @param IRepository $itemRepository
     * @throws \Exception
     */
    public function __invoke(?IItem &$item, array &$where, IRepository $itemRepository): void
    {
        if (!empty($where)) {
            $items = $itemRepository->all($where);

            foreach ($items as $itemWhere) {
                $this->check($itemWhere);
            }
        } else {
            $this->check($item);
        }
    }
}
