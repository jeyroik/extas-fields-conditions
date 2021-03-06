<?php
namespace tests\fields;

use Dotenv\Dotenv;
use extas\components\conditions\Condition;
use extas\components\conditions\ConditionRepository;
use extas\components\repositories\TSnuffRepositoryDynamic;
use extas\components\values\RepositoryValue;
use extas\components\values\Value;
use extas\components\values\ValueRepository;
use extas\interfaces\values\IValue;
use PHPUnit\Framework\TestCase;
use extas\components\plugins\TSnuffPlugins;
use extas\components\conditions\TSnuffConditions;
use extas\components\extensions\Extension;
use extas\components\extensions\ExtensionRepository;
use extas\components\extensions\fields\ExtensionFieldConditions;
use extas\components\extensions\TSnuffExtensions;
use extas\components\fields\Field;
use extas\components\fields\FieldRepository;
use extas\components\items\TSnuffItems;
use extas\components\plugins\fields\PluginFieldCreateAfter;
use extas\components\plugins\fields\PluginFieldCreateBefore;
use extas\components\plugins\fields\PluginFieldDeleteAfter;
use extas\components\plugins\fields\PluginFieldDeleteBefore;
use extas\components\plugins\fields\PluginFieldUpdateAfter;
use extas\components\plugins\fields\PluginFieldUpdateBefore;
use extas\components\plugins\PluginRepository;
use extas\interfaces\conditions\IConditionParameter;
use extas\interfaces\extensions\fields\IExtensionFieldConditions;
use extas\interfaces\repositories\IRepository;
use extas\interfaces\samples\parameters\ISampleParameter;
use extas\interfaces\values\IRepositoryValue;

/**
 * Class PluginsTest
 *
 * @package tests
 * @author jeyroik <jeyroik@gmail.com>
 */
class PluginsTest extends TestCase
{
    use TSnuffExtensions;
    use TSnuffItems;
    use TSnuffConditions;
    use TSnuffPlugins;
    use TSnuffRepositoryDynamic;

    protected function setUp(): void
    {
        parent::setUp();
        $env = Dotenv::create(getcwd() . '/tests/');
        $env->load();

        $this->createSnuffDynamicRepositories([
            ['conditions', 'name', Condition::class]
        ]);

        $this->registerSnuffRepos([
            'fieldRepository' => FieldRepository::class,
            'valueRepository' => ValueRepository::class
        ]);

        $this->createWithSnuffRepo('extensionRepository', new Extension([
            Extension::FIELD__CLASS => ExtensionFieldConditions::class,
            Extension::FIELD__INTERFACE => IExtensionFieldConditions::class,
            Extension::FIELD__SUBJECT => 'extas.field',
            Extension::FIELD__METHODS => [
                "getBeforeCreate", "getBeforeUpdate", "getBeforeDelete",
                "getAfterCreate", "getAfterUpdate", "getAfterDelete"
            ]
        ]));
        $this->createSnuffConditions(['in', 'not_in']);
        $this->createWithSnuffRepo(
            'valueRepository' ,
            new Value([Value::FIELD__CLASS => RepositoryValue::class])
        );
    }

    protected function tearDown(): void
    {
        $this->deleteSnuffDynamicRepositories();
    }

    public function testBeforeCreate()
    {
        $this->createSnuffPlugin(PluginFieldCreateBefore::class, ['extas.snuff_items.create.before']);
        $this->createField(ExtensionFieldConditions::FIELD__BEFORE_CREATE);

        $item = $this->createSnuffItem(['name' => 'test__create_before']);

        $this->snuffRepository()->create($item);

        $this->expectExceptionMessage('Condition failed');
        $this->snuffRepository()->create($item);
    }

    public function testAfterCreate()
    {
        $this->createSnuffPlugin(PluginFieldCreateAfter::class, ['extas.snuff_items.create.after']);
        $this->createField(ExtensionFieldConditions::FIELD__AFTER_CREATE);

        $item = $this->createSnuffItem(['name' => 'test__create_after']);

        $this->expectExceptionMessage('Condition failed');
        $this->snuffRepository()->create($item);
    }

    public function testBeforeUpdate()
    {
        $this->createSnuffPlugin(PluginFieldUpdateBefore::class, ['extas.snuff_items.update.before']);
        $this->createField(ExtensionFieldConditions::FIELD__BEFORE_UPDATE);

        $item = $this->createSnuffItem(['name' => 'test__update_before']);

        $this->snuffRepository()->create($item);

        $this->expectExceptionMessage('Condition failed');
        $this->snuffRepository()->update($item);
    }

    public function testAfterUpdate()
    {
        $this->createSnuffPlugin(PluginFieldUpdateAfter::class, ['extas.snuff_items.update.after']);
        $this->createField(ExtensionFieldConditions::FIELD__AFTER_UPDATE);

        $item = $this->createSnuffItem(['name' => 'test__update_after']);

        $this->snuffRepository()->create($item);

        $this->expectExceptionMessage('Condition failed');
        $this->snuffRepository()->update($item);
    }

    public function testBeforeDelete()
    {
        $this->createSnuffPlugin(PluginFieldDeleteBefore::class, ['extas.snuff_items.delete.before']);
        $this->createField(ExtensionFieldConditions::FIELD__BEFORE_DELETE, 'in');

        $item = $this->createSnuffItem(['name' => 'test__delete_before']);

        $this->expectExceptionMessage('Condition failed');
        $this->snuffRepository()->delete([], $item);
    }

    public function testAfterDelete()
    {
        $this->createSnuffPlugin(PluginFieldDeleteAfter::class, ['extas.snuff_items.delete.after']);
        $this->createField(ExtensionFieldConditions::FIELD__AFTER_DELETE, 'in');

        $item = $this->createSnuffItem(['name' => 'test__delete_after']);
        $this->snuffRepository()->create($item);

        $this->expectExceptionMessage('Condition failed');
        $this->snuffRepository()->delete([], $item);
    }

    /**
     * @param string $stage
     * @param string $condition
     */
    protected function createField(string $stage, string $condition = 'not_in'): void
    {
        $this->createWithSnuffRepo('fieldRepository', new Field([
            Field::FIELD__NAME => 'name',
            Field::FIELD__PARAMETERS => [
                'subject' => [
                    ISampleParameter::FIELD__NAME => 'subject',
                    ISampleParameter::FIELD__VALUE => 'snuff.item'
                ]
            ],
            $stage => [
                [
                    IConditionParameter::FIELD__CONDITION => $condition,
                    IConditionParameter::FIELD__VALUE => [
                        IRepositoryValue::FIELD__REPOSITORY_NAME => 'snuffRepository',
                        IRepositoryValue::FIELD__METHOD => 'all',
                        IRepositoryValue::FIELD__QUERY => [
                            'name' => '@' . IExtensionFieldConditions::REPLACE__FIELD_VALUE
                        ],
                        IRepositoryValue::FIELD__FIELD => 'name'
                    ]
                ]
            ]
        ]));
    }
}
