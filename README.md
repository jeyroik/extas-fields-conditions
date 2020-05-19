![PHP Composer](https://github.com/jeyroik/extas-fields-conditions/workflows/PHP%20Composer/badge.svg?branch=master&event=push)
![codecov.io](https://codecov.io/gh/jeyroik/extas-fields-conditions/coverage.svg?branch=master)
<a href="https://github.com/phpstan/phpstan"><img src="https://img.shields.io/badge/PHPStan-enabled-brightgreen.svg?style=flat" alt="PHPStan Enabled"></a>

# Описание

Пакет позволяет настроить для полей условия (проверки) для стадий 
- перед созданием
- после создания
- перед обновлением
- после обновления
- перед удаленеим
- после удаления

# Использование

Для нашей сущности `item` настроим две проверки перед созданием:
- Проверим, что значение не равно `test`.
- Проверим, что сущность с текущим значением отсутствует.

`extas.json`
```json
{
  "fields": [
    {
      "name": "value",
      "parameters": {
        "subject": {
          "name": "subject",
          "value": "item"
        }
      },
      "before_create": [
        {
          "condition": "neq",
          "value": "test"
        },
        {
          "condition": "empty",
          "value": {
            "repository": "itemRepository",
            "method": "all",
            "query": {"value": "@value"}
          }
        }
      ]
    }
  ]
}
```
```php
/**
 * @method itemRepo()
 */
$item = new class ([
    'value' => 'test'
]) extends \extas\components\Item {
    use \extas\components\THasValue;
    protected function getSubjectForExtension() : string{
        return 'item';
    }
};

try {
    $this->itemRepo()->create($item); // Exception "Condition failed"
} catch (\Exception $e) {

}
$item->setValue('unique');
$this->itemRepo()->create($item); // ok
$this->itemRepo()->create($item); // Exception "Condition failed"
```