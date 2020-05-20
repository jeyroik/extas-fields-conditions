<?php
namespace extas\components\plugins\fields;

use extas\components\values\RepositoryValue;
use extas\interfaces\conditions\IConditionParameter;
use extas\interfaces\fields\IField;
use extas\interfaces\IHasValue;
use extas\interfaces\IItem;
use extas\interfaces\values\IRepositoryValue;

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
    /**
     * @param IItem|null|IHasValue $item
     * @throws \Exception
     */
    public function check(?IItem $item): bool
    {
        if (!$item) {
            return false;
        }

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
     * @param IConditionParameter $condition
     * @param $currentValue
     * @throws \Exception
     */
    protected function isConditionValid(IConditionParameter $condition, $currentValue)
    {
        $data = $condition->getValue();
        $data[IRepositoryValue::FIELD__REPLACES] = ['value' => $currentValue];
        $repositoryValue = new RepositoryValue($data);

        if ($repositoryValue->isValid()) {
            $condition->setValue($repositoryValue->buildValue());
        }

        if (!$condition->isConditionTrue($currentValue)) {
            throw new \Exception('Condition failed');
        }
    }
}
