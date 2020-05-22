<?php
namespace extas\components\plugins\fields;

use extas\components\values\WithComplexValue;
use extas\interfaces\conditions\IConditionParameter;
use extas\interfaces\extensions\fields\IExtensionFieldConditions;
use extas\interfaces\fields\IField;
use extas\interfaces\IHasComplexValue;
use extas\interfaces\IHasValue;
use extas\interfaces\IItem;
use extas\interfaces\values\IValueDispatcher;

/**
 * Class TPluginFieldCheck
 *
 * @property string $checkName
 * @method fieldRepository()
 *
 * @package extas\components\plugins\fields
 * @author jeyroik <jeyroik@gmail.com>
 */
trait TPluginFieldCheck
{
    protected IItem $item;

    /**
     * @param IItem|null|IHasValue $item
     * @throws \Exception
     */
    public function check(?IItem $item): bool
    {
        if (!$item) {
            return false;
        }

        $this->item = $item;

        /**
         * @var IField[] $fields
         */
        $subject = $item->__subject();
        $fields = $this->fieldRepository()->all([
            IField::FIELD__PARAMETERS . '.subject.value' => $subject
        ]);

        foreach ($fields as $field) {
            if ($item->has($field->getName())) {
                $field->setValue($item[$field->getName()]);
                $this->validateField($field);
            }
        }

        return true;
    }

    /**
     * @param IField $item
     * @throws \Exception
     */
    protected function validateField(IField $item)
    {
        $checkMethod = $this->checkName;
        $conditions = $item->$checkMethod();
        $currentValue = $item->getValue();

        foreach ($conditions as $condition) {
            $this->isConditionValid($condition, $currentValue);
        }
    }

    /**
     * @param IConditionParameter|IHasComplexValue $condition
     * @param $currentValue
     * @throws \Exception
     */
    protected function isConditionValid(IConditionParameter $condition, $currentValue)
    {
        $complex = new WithComplexValue([
            WithComplexValue::FIELD__VALUE => $condition->getValue(),
            IValueDispatcher::FIELD__REPLACES => $this->getReplaces($currentValue)
        ]);
        $condition->setValue($complex->buildValue());

        if (!$condition->isConditionTrue($currentValue)) {
            throw new \Exception('Condition failed');
        }
    }

    /**
     * @param $currentValue
     * @return array
     */
    protected function getReplaces($currentValue): array
    {
        return [
            IExtensionFieldConditions::REPLACE__FIELD_VALUE => $currentValue,
            IExtensionFieldConditions::REPLACE__PARENT => $this->item
        ];
    }
}
