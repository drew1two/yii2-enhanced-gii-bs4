<?php
/**
 * @var yii\web\View $this
 * @var drew1two\enhancedgii\migration\Generator $generator
 * @var kartik\widgets\ActiveForm $form
 */

echo $form->field($generator, 'tableName');
echo $form->field($generator, 'migrationPath');
echo $form->field($generator, 'migrationTime')->widget('\yii\widgets\MaskedInput', [
    'mask' => '999999_999999'
]);
echo $form->field($generator, 'migrationName');
echo $form->field($generator, 'db');
echo $form->field($generator, 'useTablePrefix')->checkbox();
echo $form->field($generator, 'generateRelations')->checkbox();
echo $form->field($generator, 'createTableIfNotExists')->dropDownList(['0' => 'Throw Error', '1' => 'Skip table']);
echo $form->field($generator, 'disableFkc')->checkbox();
echo $form->field($generator, 'isSafeUpDown')->checkbox();
