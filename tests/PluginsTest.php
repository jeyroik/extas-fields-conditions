<?php
namespace tests;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;

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
            'fieldRepository' => FieldRepository::class
        ]);
        $this->createRepoExt(['fieldRepository']);
        $this->extRepo->create(new Extension([
            Extension::FIELD__CLASS => ExtensionFieldConditions::class,
            Extension::FIELD__INTERFACE => IExtensionFieldConditions::class,
            Extension::FIELD__SUBJECT => 'extas.field',
            Extension::FIELD__METHODS => [
                "getBeforeCreate", "getBeforeUpdate", "getBeforeDelete",
                "getAfterCreate", "getAfterUpdate", "getAfterDelete"
            ]
        ]));
        $this->createSnuffConditions(['empty', 'not_empty']);
    }

    protected function tearDown(): void
    {
        $this->deleteSnuffExtensions();
        $this->deleteSnuffConditions();
        $this->deleteSnuffItems(['name' => 'test']);

        $this->extRepo->delete([Extension::FIELD__CLASS => ExtensionFieldConditions::FIELD__CLASS]);
        $this->fieldRepo->delete([Field::FIELD__NAME => 'test']);
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
        $this->pluginRepo->create(new Plugin([
            Plugin::FIELD__CLASS => PluginFieldCreateBefore::class,
            Plugin::FIELD__STAGE => 'test.create.before'
        ]));
        $this->createField(ExtensionFieldConditions::FIELD__BEFORE_CREATE);

        $item = $this->createSnuffItem(['name' => 'test']);

        $this->snuffRepository()->create($item);

        $this->expectExceptionMessage('Condition failed');
        $this->snuffRepository()->create($item);
    }

    public function testAfterCreate()
    {
        $this->pluginRepo->create(new Plugin([
            Plugin::FIELD__CLASS => PluginFieldCreateAfter::class,
            Plugin::FIELD__STAGE => 'test.create.after'
        ]));
        $this->createField(ExtensionFieldConditions::FIELD__AFTER_CREATE);

        $item = $this->createSnuffItem(['name' => 'test']);

        $this->snuffRepository()->create($item);

        $this->expectExceptionMessage('Condition failed');
        $this->snuffRepository()->create($item);
    }

    public function testBeforeUpdate()
    {
        $this->pluginRepo->create(new Plugin([
            Plugin::FIELD__CLASS => PluginFieldUpdateBefore::class,
            Plugin::FIELD__STAGE => 'test.update.before'
        ]));
        $this->createField(ExtensionFieldConditions::FIELD__BEFORE_UPDATE);

        $item = $this->createSnuffItem(['name' => 'test']);

        $this->snuffRepository()->create($item);

        $this->expectExceptionMessage('Condition failed');
        $this->snuffRepository()->update($item);
    }

    public function testAfterUpdate()
    {
        $this->pluginRepo->create(new Plugin([
            Plugin::FIELD__CLASS => PluginFieldUpdateAfter::class,
            Plugin::FIELD__STAGE => 'test.update.after'
        ]));
        $this->createField(ExtensionFieldConditions::FIELD__AFTER_UPDATE);

        $item = $this->createSnuffItem(['name' => 'test']);

        $this->snuffRepository()->create($item);

        $this->expectExceptionMessage('Condition failed');
        $this->snuffRepository()->update($item);
    }

    public function testBeforeDelete()
    {
        $this->pluginRepo->create(new Plugin([
            Plugin::FIELD__CLASS => PluginFieldDeleteBefore::class,
            Plugin::FIELD__STAGE => 'test.delete.before'
        ]));
        $this->createField(ExtensionFieldConditions::FIELD__BEFORE_DELETE, 'not_empty');

        $item = $this->createSnuffItem(['name' => 'test']);

        $this->expectExceptionMessage('Condition failed');
        $this->snuffRepository()->delete($item);
    }

    public function testAfterDelete()
    {
        $this->pluginRepo->create(new Plugin([
            Plugin::FIELD__CLASS => PluginFieldDeleteAfter::class,
            Plugin::FIELD__STAGE => 'test.delete.after'
        ]));
        $this->createField(ExtensionFieldConditions::FIELD__AFTER_DELETE, 'not_empty');

        $item = $this->createSnuffItem(['name' => 'test']);
        $this->snuffRepository()->create($item);

        $this->expectExceptionMessage('Condition failed');
        $this->snuffRepository()->delete($item);
    }

    /**
     * @param string $stage
     * @param string $condition
     */
    protected function createField(string $stage, string $condition = 'empty'): void
    {
        $this->fieldRepo->create(new Field([
            Field::FIELD__NAME => 'name',
            Field::FIELD__PARAMETERS => [
                'subject' => [
                    ISampleParameter::FIELD__NAME => 'subject',
                    ISampleParameter::FIELD__VALUE => 'test'
                ]
            ],
            $stage => [
                IConditionParameter::FIELD__CONDITION => $condition,
                IConditionParameter::FIELD__VALUE => [
                    IRepositoryValue::FIELD__REPOSITORY_NAME => 'snuffRepository',
                    IRepositoryValue::FIELD__METHOD => 'all',
                    IRepositoryValue::FIELD__QUERY => ['name' => '@value']
                ]
            ]
        ]));
    }
}