<?php
/**
 * This is the template for generating the model class of a specified table.
 *
 * @var yii\web\View $this
 * @var drew1two\enhancedgii\model\Generator $generator
 * @var string $tableName full table name
 * @var string $className class name
 * @var string $queryClassName query class name
 * @var yii\db\TableSchema $tableSchema
 * @var boolean $isTree
 * @var string[] $labels list of attribute labels (name => label)
 * @var string[] $rules list of validation rules
 * @var array $relations list of relations (name => relation declaration)
 */

// Used to check if a feature is enabled (by the field being filled in) and if the field actually exists in the database
$enabled = new stdClass();
foreach (['deletedBy', 'createdBy', 'createdAt', 'updatedBy', 'updatedAt', 'deletedBy', 'deletedAt', 'UUIDColumn', 'optimisticLock'] AS $check) $enabled->$check = ($generator->$check && isset($generator->tableSchema->columns[$generator->$check]));

echo "<?php\n";
?>

namespace <?= $generator->nsModel ?>\base;

use Yii;
use yii\base\NotSupportedException;
<?php if ($enabled->createdAt || $enabled->updatedAt): ?>
use yii\behaviors\TimestampBehavior;
<?php endif; ?>
<?php if ($enabled->createdBy || $enabled->updatedBy): ?>
use yii\behaviors\BlameableBehavior;
<?php endif; ?>
<?php if ($generator->UUIDColumn): ?>
use mootensai\behaviors\UUIDBehavior;
<?php endif; ?>
<?php
$baseModelClassStr = str_replace('\\','/',$generator->baseModelClass);
$baseModelClassArray = explode('/',$baseModelClassStr);
$baseModelClassName = $baseModelClassArray[array_key_last($baseModelClassArray)];
if (count($baseModelClassArray) > 1) {
    echo 'use '.$generator->baseModelClass.';'."\n";
}
?>
<?php if ($generator->isBaseIdentityClass): ?>
use yii\web\IdentityInterface;
<?php endif; ?>

/**
 * This is the base model class for table "<?= $generator->generateTableName($tableName) ?>".
 *
<?php foreach ($tableSchema->columns as $column): ?>
<?php if ($column->type === 'bigint') {$column->phpType = 'integer';} ?>
 * @property <?= "{$column->phpType} \${$column->name}\n" ?>
<?php endforeach; ?>
<?php if (!empty($relations)): ?>
 *
<?php foreach ($relations as $name => $relation): ?>
<?php if (!in_array($name, $generator->skippedRelations)): ?>
 * @property <?= '\\' . $generator->nsModel . '\\' . $relation[$generator::REL_CLASS] . ($relation[$generator::REL_IS_MULTIPLE] ? '[]' : '') . ' $' . lcfirst($name) . "\n" ?>
<?php endif; ?>
<?php endforeach; ?>
<?php endif; ?>
 */
class <?= $className ?> extends <?= ($isTree) ? '\kartik\tree\models\Tree' : $baseModelClassName ?><?= ($generator->isBaseIdentityClass) ? ' implements IdentityInterface' . "\n" : '' . "\n" ?>
{
<?= (!$isTree) ? "  use \\drew1two\\relation\\RelationTrait;\n" : "" ?>

<?php if ($generator->generateYiiUserModelMethods || $generator->generateStatusDeclarations): ?>
    const STATUS_DELETED = 0;
    const STATUS_INACTIVE = 9;
    const STATUS_ACTIVE = 10;
<?php endif; ?>

<?php if ($enabled->deletedBy): ?>
    private $_rt_softdelete;
    private $_rt_softrestore;

    public function __construct(){
        parent::__construct();
        $this->_rt_softdelete = [
            '<?= $generator->deletedBy ?>' => <?= (empty($generator->deletedByValue)) ? 1 : $generator->deletedByValue ?>,
<?php if ($enabled->deletedAt): ?>
            '<?= $generator->deletedAt ?>' => <?= (empty($generator->deletedAtValue)) ? 1 : $generator->deletedAtValue ?>,
<?php endif; ?>
        ];
        $this->_rt_softrestore = [
            '<?= $generator->deletedBy ?>' => <?= (empty($generator->deletedByValueRestored)) ? 0 : $generator->deletedByValueRestored ?>,
<?php if ($enabled->deletedAt): ?>
            '<?= $generator->deletedAt ?>' => <?= (empty($generator->deletedAtValueRestored)) ? 0 : $generator->deletedAtValueRestored ?>,
<?php endif; ?>
        ];
    }
<?php endif; ?>
<?php if (!$isTree): ?>

    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public static function relationNames()
    {
        return [<?= "\n\t\t\t'" . implode("',\n\t\t\t'", array_keys($relations)) . "'\n\t\t" ?>];
    }

<?php endif; ?>
<?php
    if ($generator->generateYiiUserModelMethods) {
        $arr1 = "'statusDefault' => [['status'], 'default', 'value' => self::STATUS_INACTIVE]";
        $arr2 = "'statusRange' => [['status'], 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_DELETED]]";
        array_unshift($rules, $arr2);
        array_unshift($rules, $arr1);
    }
?>
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [<?= "\n\t\t\t" . implode(",\n\t\t\t", $rules) . "\n\t\t" ?>];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '<?= $generator->generateTableName($tableName) ?>';
    }
<?php if ($generator->db !== 'db'): ?>

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('<?= $generator->db ?>');
    }
<?php endif; ?>
<?php if ($enabled->optimisticLock): ?>

    /**
     *
     * @return string
     * overwrite function optimisticLock
     * return string name of field are used to stored optimistic lock
     *
     */
    public function optimisticLock() {
        return '<?= $generator->optimisticLock ?>';
    }
<?php endif; ?>

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
<?php foreach ($labels as $name => $label): ?>
<?php if (!in_array($name, $generator->skippedColumns)): ?>
            <?= "'$name' => " . $generator->generateString($label) . ",\n" ?>
<?php endif; ?>
<?php endforeach; ?>
        ];
    }
<?php foreach ($relations as $name => $relation): ?>
    <?php if (!in_array($name, $generator->skippedRelations)): ?>

    /**
     * @return \yii\db\ActiveQuery
     */
    public function get<?= ucfirst($name) ?>()
    {
        <?= $relation[0] . "\n" ?>
    }
    <?php endif; ?>
<?php endforeach; ?>
<?php if ($enabled->createdAt || $enabled->updatedAt
        || $enabled->createdBy || $enabled->updatedBy
        || $enabled->UUIDColumn):
    echo "\n"; ?>
    /**
     * {@inheritdoc}
     * @return array mixed
     */
    public function behaviors()
    {
        return <?= ($isTree) ? "array_merge(parent::behaviors(), " : ""; ?>[
<?php if ($enabled->createdAt || $enabled->updatedAt):?>
            'timestamp' => [
                'class' => TimestampBehavior::class,
<?php if ($enabled->createdAt):?>
                'createdAtAttribute' => '<?= $generator->createdAt?>',
<?php else :?>
                'createdAtAttribute' => false,
<?php endif; ?>
<?php if ($enabled->updatedAt):?>
                'updatedAtAttribute' => '<?= $generator->updatedAt?>',
<?php else :?>
                'updatedAtAttribute' => false,
<?php endif; ?>
<?php if (!empty($generator->timestampValue) && $generator->timestampValue != 'time()'):?>
                'value' => <?= $generator->timestampValue?>,
<?php endif; ?>
            ],
<?php endif; ?>
<?php if ($enabled->createdBy || $enabled->updatedBy):?>
            'blameable' => [
                'class' => BlameableBehavior::class,
<?php if ($enabled->createdBy):?>
                'createdByAttribute' => '<?= $generator->createdBy?>',
<?php else :?>
                'createdByAttribute' => false,
<?php endif; ?>
<?php if ($enabled->updatedBy):?>
                'updatedByAttribute' => '<?= $generator->updatedBy?>',
<?php else :?>
                'updatedByAttribute' => false,
<?php endif; ?>
<?php if (!empty($generator->blameableValue) && $generator->blameableValue != '\\Yii::$app->user->id'):?>
                'value' => <?= $generator->blameableValue?>,
<?php endif; ?>
            ],
<?php endif; ?>
<?php if ($generator->UUIDColumn):?>
            'uuid' => [
                'class' => UUIDBehavior::class,
<?php if (!empty($generator->UUIDColumn) && isset($generator->tableSchema->columns[$generator->UUIDColumn])):?>
                'column' => '<?= $generator->UUIDColumn?>',
<?php endif; ?>
            ],
<?php endif; ?>
        ]<?= ($isTree) ? ")" : "" ?>;
    }
<?php endif; ?>
<?php if ($queryClassName): ?>
<?php
    $queryClassFullName = '\\' . $generator->queryNs . '\\' . $queryClassName;
    echo "\n";
?>
<?php if ($enabled->deletedBy): ?>
    /**
     * The following code shows how to apply a default condition for all queries:
     *
     * ```php
     * class Customer extends ActiveRecord
     * {
     *   public static function find()
     *   {
     *       return parent::find()->where(['deleted' => false]);
     *   }
     * }
     *
     * // Use andWhere()/orWhere() to apply the default condition
     * // SELECT FROM customer WHERE `deleted`=:deleted AND age>30
     * $customers = Customer::find()->andWhere('age>30')->all();
     *
     * // Use where() to ignore the default condition
     * // SELECT FROM customer WHERE age>30
     * $customers = Customer::find()->where('age>30')->all();
     * ```
     */
<?php endif; ?>

    /**
     * {@inheritdoc}
     * @return <?= $queryClassFullName ?> the active query used by this AR class.
     */
    public static function find()
    {
<?php if ($enabled->deletedBy): ?>
        $query = new <?= $queryClassFullName ?>(get_called_class());
        return $query->where(['<?= $tableName ?>.<?= $generator->deletedBy ?>' => <?= $generator->deletedByValueRestored ?>]);
<?php else: ?>
        return new <?= $queryClassFullName ?>(get_called_class());
<?php endif; ?>
    }
<?php endif; ?>

<?php if ($generator->isBaseIdentityClass): ?>
    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

<?php endif; ?>

<?php if ($generator->generateYiiUserModelMethods): ?>
    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds user by verification email token
     *
     * @param string $token verify email token
     * @return static|null
     */
    public static function findByVerificationToken($token) {
        return static::findOne([
            'verification_token' => $token,
            'status' => self::STATUS_INACTIVE
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Generates new token for email verification
     */
    public function generateEmailVerificationToken()
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
<?php endif; ?>
}
