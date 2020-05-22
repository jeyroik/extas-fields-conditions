<?php
namespace extas\interfaces\extensions\fields;

/**
 * Interface IExtensionFieldConditions
 *
 * @package extas\interfaces\extensions\fields
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IExtensionFieldConditions
{
    public const FIELD__BEFORE_CREATE = 'before_create';
    public const FIELD__AFTER_CREATE = 'after_create';
    public const FIELD__BEFORE_DELETE = 'before_delete';
    public const FIELD__AFTER_DELETE = 'after_delete';
    public const FIELD__BEFORE_UPDATE = 'before_update';
    public const FIELD__AFTER_UPDATE = 'after_update';

    public const REPLACE__FIELD_VALUE = 'field_value';
    public const REPLACE__PARENT = 'parent';

    /**
     * @return array
     */
    public function getBeforeCreate(): array;

    /**
     * @return array
     */
    public function getAfterCreate(): array;

    /**
     * @return array
     */
    public function getBeforeUpdate(): array;

    /**
     * @return array
     */
    public function getAfterUpdate(): array;

    /**
     * @return array
     */
    public function getBeforeDelete(): array;

    /**
     * @return array
     */
    public function getAfterDelete(): array;
}
