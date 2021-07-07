<?php

/**
 * @var yii\web\View $this
 * @var drew1two\enhancedgii\crud\Generator $generator
 * @var array $relations
 */

use yii\helpers\Inflector;
use yii\helpers\StringHelper;
if (count(array_keys($relations)) === 1) {
    // TODO:- workout why this $relation array is returned to us with the relationName as the key.
    // ie:- array(1) {
    //  ["emailTemplateTokenLookups"]=>
    //  array(7) {
    //    [0]=>
    //    string(98) "return $this->hasMany(\common\models\EmailTemplateTokenLookup::className(), ['token_id' => 'id']);"
    //    [1]=>
    //    string(24) "EmailTemplateTokenLookup"
    //    [2]=>
    //    bool(true)
    //    [3]=>
    //    string(27) "email_template_token_lookup"
    //    [4]=>
    //    string(2) "id"
    //    [5]=>
    //    string(8) "token_id"
    //    [6]=>
    //    int(0)
    //  }
    //}
    // It should be returning the sub array
    // ie:-
    //  array(7) {
    //    [0]=>
    //    string(98) "return $this->hasMany(\common\models\EmailTemplateTokenLookup::className(), ['token_id' => 'id']);"
    //    [1]=>
    //    string(24) "EmailTemplateTokenLookup"
    //    [2]=>
    //    bool(true)
    //    [3]=>
    //    string(27) "email_template_token_lookup"
    //    [4]=>
    //    string(2) "id"
    //    [5]=>
    //    string(8) "token_id"
    //    [6]=>
    //    int(0)
    //  }
    $relationsSub = reset($relations);
    $relationName = $relationsSub[$generator::REL_CLASS];
} else {
    $relationName = $relations[$generator::REL_CLASS];
}
$pk = empty($generator->tableSchema->primaryKey) ? $generator->tableSchema->getColumnNames()[0] : $generator->tableSchema->primaryKey[0];
?>
<?= "<?php" ?>

use kartik\helpers\Html;
use kartik\tabs\TabsX;
use yii\helpers\Url;
/**
* @var yii\web\View $this
* @var <?= ltrim($generator->nsModel, '\\').'\\'.$relationName ?> $model
*/

/**
* @var yii\web\View $this
*/

$items = [
    [
        'label' => '<i class="fas fa-book"></i> '. Html::encode(<?= $generator->generateString(StringHelper::basename($generator->modelClass)) ?>),
        'options' => ['id' => "tab_<?= StringHelper::basename($generator->modelClass) ?>_{$model-><?= $pk ?>}"],
        'content' => $this->render('_detail', [
            'model' => $model,
        ]),
    ]
];
<?php foreach ($relations as $name => $rel): ?>
    <?php if ($rel[2] && isset($rel[3]) && !in_array($name, $generator->skippedRelations)): ?>
        if (!empty($model-><?= $name ?>)) {
            $items[] =
            [
                'label' => '<i class="fas fa-book"></i> '. Html::encode(<?= $generator->generateString(Inflector::camel2words($rel[1])) ?>),
                'options' => ['id' => "tab_<?= $rel[1] ?>_{$model-><?= $pk ?>}"],
                'content' => $this->render('_data<?= $rel[1] ?>', [
                    'model' => $model,
                    'row' => $model-><?= $name ?>,
                ]),
            ];
        }
    <?php elseif(isset($rel[$generator::REL_IS_MASTER]) && !$rel[$generator::REL_IS_MASTER]): ?>
        $items[] =
        [
            'label' => '<i class="fas fa-book"></i> '. Html::encode(<?= $generator->generateString(Inflector::camel2words($rel[1])) ?>),
            'options' => ['id' => "tab_<?= $rel[1] ?>_{$model-><?= $pk ?>}"],
            'content' => $this->render('_data<?= $rel[1] ?>', [
            'model' => $model-><?= $name ?>
            ]),
        ];
    <?php endif; ?>
<?php endforeach; ?>

echo TabsX::widget([
    'items' => $items,
    'position' => TabsX::POS_ABOVE,
    'encodeLabels' => false,
    'class' => 'tes',
    'pluginOptions' => [
        'bordered' => true,
        'sideways' => true,
        'enableCache' => false
    ],
]);
?>
