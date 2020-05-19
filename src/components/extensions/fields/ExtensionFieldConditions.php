<?php
namespace extas\components\extensions\fields;

use extas\components\conditions\ConditionParameter;
use extas\components\extensions\Extension;
use extas\interfaces\conditions\IConditionParameter;
use extas\interfaces\extensions\fields\IExtensionFieldConditions;
use extas\interfaces\fields\IField;

/**
 * Class ExtensionFieldConditions
 *
 * @package extas\components\extensions\fields
 * @author jeyroik <jeyroik@gmail.com>
 */
class ExtensionFieldConditions extends Extension implements IExtensionFieldConditions
{
    /**
     * @param IField $field
     * @return array|IConditionParameter[]
     */
    public function getBeforeCreate(IField $field = null): array
    {
        return $this->getConditions(static::FIELD__BEFORE_CREATE, $field);
    }

    /**
     * @param IField $field
     * @return array|IConditionParameter[]
     */
    public function getBeforeUpdate(IField $field = null): array
    {
        return $this->getConditions(static::FIELD__BEFORE_UPDATE, $field);
    }

    /**
     * @param IField $field
     * @return array|IConditionParameter[]
     */
    public function getBeforeDelete(IField $field = null): array
    {
        return $this->getConditions(static::FIELD__BEFORE_DELETE, $field);
    }

    /**
     * @param IField $field
     * @return array|IConditionParameter[]
     */
    public function getAfterCreate(IField $field = null): array
    {
        return $this->getConditions(static::FIELD__AFTER_CREATE, $field);
    }

    /**
     * @param IField $field
     * @return array|IConditionParameter[]
     */
    public function getAfterUpdate(IField $field = null): array
    {
        return $this->getConditions(static::FIELD__AFTER_UPDATE, $field);
    }

    /**
     * @param IField $field
     * @return array|IConditionParameter[]
     */
    public function getAfterDelete(IField $field = null): array
    {
        return $this->getConditions(static::FIELD__AFTER_DELETE, $field);
    }

    /**
     * @param string $stage
     * @param IField $field
     * @return IConditionParameter[]
     */
    protected function getConditions(string $stage, IField $field = null): array
    {
        $conditionsData = $field[$stage] ?? [];
        $conditions = [];

        foreach ($conditionsData as $condition) {
            $conditions[] = new ConditionParameter($condition);
        }

        return $conditions;
    }
}
