<?php
/**
 * This is the template for generating the model class of a specified table.
 *
 * @var yii\web\View $this
 * @var drew1two\enhancedgii\model\Generator $generator
 * @var string $tableName full table name
 * @var string $className class name
 * @var yii\db\TableSchema $tableSchema
 * @var string[] $labels list of attribute labels (name => label)
 * @var string[] $rules list of validation rules
 * @var array $relations list of relations (name => relation declaration)
 */

echo "<?php\n";
?>

namespace <?= $generator->nsModel ?>;

use Yii;
use <?= $generator->nsModel ?>\base\<?= $className ?> as Base<?= $className ?>;

/**
 * This is the model class for table "<?= $tableName ?>".
 */
class <?= $className ?> extends Base<?= $className . "\n" ?>
{
<?php if ($generator->generateYiiUserModelMethods) {
    $arr1 = "[['status'], 'default', 'value' => self::STATUS_INACTIVE]";
    $arr2 = "[['status'], 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_DELETED]]";
    array_unshift($rules, $arr2);
    array_unshift($rules, $arr1);
}?>
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [<?= "\n            " . implode(",\n            ", $rules) . "\n        " ?>]);
    }
	
<?php if ($generator->generateAttributeHints): ?>
    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return [
<?php foreach ($labels as $name => $label): ?>
<?php if (!in_array($name, $generator->skippedColumns)): ?>
            <?= "'$name' => " . $generator->generateString($label) . ",\n" ?>
<?php endif; ?>
<?php endforeach; ?>
        ];
    }
<?php endif; ?>
}
