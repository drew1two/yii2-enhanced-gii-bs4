<?php

/**
 * This is the template for generating the StatusDefinition interface class.
 * @var yii\web\View $this
 * @var drew1two\enhancedgii\crud\Generator $generator
 */

echo "<?php\n"; ?>


namespace common\interfaces;


interface StatusDefinition
{
    const STATUS_ACTIVE = 10;
    const STATUS_INACTIVE = 9;
    const STATUS_DELETED = 0;
}