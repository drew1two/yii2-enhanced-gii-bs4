<?php

/**
 * This is the template for generating the ActiveQuery class.
 * @var yii\web\View $this
 * @var drew1two\enhancedgii\crud\Generator $generator
 * @var string $className class name
 * @var string $modelClassName related model class name
 */

$modelFullClassName = $modelClassName;
if ($generator->nsModel !== $generator->queryNs) {
    $modelFullClassName = '\\' . $generator->queryNs . '\\' . $modelFullClassName;
}

echo "<?php\n";
?>

namespace <?= $generator->queryNs ?>;

/**
 * This is the ActiveQuery class for [[<?= $modelFullClassName ?>]].
 *
 * @see <?= $modelFullClassName . "\n" ?>
 */
class <?= $className ?> extends <?= '\\' . ltrim($generator->queryBaseClass, '\\') . "\n" ?>
{
/*
    /**
     * Select active users.
     *
     * @param ActiveQuery $query
     */
    public function active()
    {
        $this->andWhere(['status' => User::STATUS_ACTIVE]);
        return $this;
    }

    /**
     * Select inactive users.
     *
     * @param ActiveQuery $query
     */
    public function inactive()
    {
        $this->andWhere(['status' => User::STATUS_INACTIVE]);
        return $this;
    }

    /**
     * Select deleted users.
     *
     * @param ActiveQuery $query
     */
    public function deleted()
    {
        $this->andWhere(['status' => User::STATUS_DELETED]);
        return $this;
    }
*/

    /**
     * @inheritdoc
     * @return <?= $modelFullClassName ?>[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return <?= $modelFullClassName ?>|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
