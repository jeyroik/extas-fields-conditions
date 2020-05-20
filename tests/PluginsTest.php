<?php
namespace tests;

use Dotenv\Dotenv;
use extas\components\conditions\ConditionRepository;
use extas\interfaces\conditions\IConditionRepository;
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
use extas\components\plugins\Plugin;
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

    protected IRepository $pluginRepo;
    protected IRepository $extRepo;
    protected IRepository $fieldRepo;

    protected function setUp(): void
    {
        parent::setUp();
        $env = Dotenv::create(getcwd() . '/tests/');
        $env->load();
        $this->extRepo = new ExtensionRepository();
        $this->fieldRepo = new FieldRepository();
        $this->pluginRepo = new PluginRepository();
        $this->addReposForExt([
            'fieldRepository' => FieldRepository::class,
            'conditionRepository' => ConditionRepository::class
        ]);
        $this->createRepoExt(['fieldRepository', 'conditionRepository']);
        $this->extRepo->create(new Extension([
            Extension::FIELD__CLASS => ExtensionFieldConditions::class,
            Extension::FIELD__INTERFACE => IExtensionFieldConditions::class,
            Extension::FIELD__SUBJECT => 'extas.field',
            Extension::FIELD__METHODS => [
                "getBeforeCreate", "getBeforeUpdate", "getBeforeDelete",
                "getAfterCreate", "getAfterUpdate", "getAfterDelete"
            ]
        ]));
        $this->createSnuffConditions(['in', 'not_in']);
    }

    protected function tearDown(): void
    {
        $this->deleteSnuffPlugins();
        $this->deleteSnuffExtensions();
        $this->deleteSnuffConditions();
        $this->deleteSnuffItems(['name' => 'test']);

        $this->extRepo->delete([Extension::FIELD__CLASS => ExtensionFieldConditions::FIELD__CLASS]);
        $this->fieldRepo->delete([Field::FIELD__NAME => 'name']);
        $this->pluginRepo->delete([Plugin::FIELD__CLASS => [
            PluginFieldDeleteAfter::class,
            PluginFieldDeleteBefore::class,
            PluginFieldCreateAfter::class,
            PluginFieldCreateBefore::class,
            PluginFieldUpdateAfter::class,
            PluginFieldUpdateBefore::class
        ]]);
    }

    public function testBeforeCreate()
    {
        $this->createSnuffPlugin(PluginFieldCreateBefore::class, ['extas.snuff_items.create.before']);
        $this->createField(ExtensionFieldConditions::FIELD__BEFORE_CREATE);

        $item = $this->createSnuffItem(['name' => 'test']);

        $this->snuffRepository()->create($item);

        $this->expectExceptionMessage('Condition failed');
        $this->snuffRepository()->create($item);
    }

    public function testAfterCreate()
    {
        $this->createSnuffPlugin(PluginFieldCreateAfter::class, ['extas.snuff_items.create.after']);
        $this->createField(ExtensionFieldConditions::FIELD__AFTER_CREATE);

        $item = $this->createSnuffItem(['name' => 'test']);

        $this->snuffRepository()->create($item);

        $this->expectExceptionMessage('Condition failed');
        $this->snuffRepository()->create($item);
    }

    public function testBeforeUpdate()
    {
        $this->createSnuffPlugin(PluginFieldUpdateBefore::class, ['extas.snuff_items.update.before']);
        $this->createField(ExtensionFieldConditions::FIELD__BEFORE_UPDATE);

        $item = $this->createSnuffItem(['name' => 'test']);

        $this->snuffRepository()->create($item);

        $this->expectExceptionMessage('Condition failed');
        $this->snuffRepository()->update($item);
    }

    public function testAfterUpdate()
    {
        $this->createSnuffPlugin(PluginFieldUpdateAfter::class, ['extas.snuff_items.update.after']);
        $this->createField(ExtensionFieldConditions::FIELD__AFTER_UPDATE);

        $item = $this->createSnuffItem(['name' => 'test']);

        $this->snuffRepository()->create($item);

        $this->expectExceptionMessage('Condition failed');
        $this->snuffRepository()->update($item);
    }

    public function testBeforeDelete()
    {
        $this->createSnuffPlugin(PluginFieldDeleteBefore::class, ['extas.snuff_items.delete.before']);
        $this->createField(ExtensionFieldConditions::FIELD__BEFORE_DELETE, 'not_in');

        $item = $this->createSnuffItem(['name' => 'test']);

        $this->expectExceptionMessage('Condition failed');
        $this->snuffRepository()->delete([], $item);
    }

    public function testAfterDelete()
    {
        $this->createSnuffPlugin(PluginFieldDeleteAfter::class, ['extas.snuff_items.delete.after']);
        $this->createField(ExtensionFieldConditions::FIELD__AFTER_DELETE, 'not_in');

        $item = $this->createSnuffItem(['name' => 'test']);
        $this->snuffRepository()->create($item);

        $this->expectExceptionMessage('Condition failed');
        $this->snuffRepository()->delete([], $item);
    }

    /**
     * @param string $stage
     * @param string $condition
     */
    protected function createField(string $stage, string $condition = 'in'): void
    {
        $this->fieldRepo->create(new Field([
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
                        IRepositoryValue::FIELD__QUERY => ['name' => '@value'],
                        IRepositoryValue::FIELD__FIELD => 'name'
                    ]
                ]
            ]
        ]));
    }
}
