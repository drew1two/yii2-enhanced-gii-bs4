<?php

/**
 * This is the template for generating the StatusTrait class.
 * @var yii\web\View $this
 * @var drew1two\enhancedgii\crud\Generator $generator
 */

echo "<?php\n"; ?>


namespace common\traits;


trait StatusTrait
{
    private ?string $_status = null;

    /**
     * @return string Model status.
     */
    public function getStatus()
    {
        if ($this->_status === null) {
            $statuses = self::getStatusArray();
            $this->_status = $statuses[$this->status];
        }
        return $this->_status;
    }

    /**
     * @return array Status array.
     */
    public static function getStatusArray()
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_DELETED => 'Deleted',
        ];
    }

    /**
     * @return array Status array.
     */
    public static function getSimpleStatusArray()
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
        ];
    }

    /**
     * @getStatusName
     *
     */
    public function getStatusName()
    {
        $status = $this->status;
        if ($status === self::STATUS_DELETED) {
            return 'Deleted';
        } elseif ($status === self::STATUS_INACTIVE) {
            return 'Inactive';
        } else {
            return 'Active';
        }
    }

    /**
     * @param $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
//            if ($this->deleted_by > 0 && $this->status > 0) {
//                $this->status = self::STATUS_DELETED;
//            }
            return true;
        }
        return false;
    }

    /**
     * @param $insert
     * @return bool
     */
    public function afterSave($insert,$changedAttributes)
    {
        parent::afterSave($insert,$changedAttributes);
        $redoSave = false;
        // if our status has changed we need to change the deleted_by accordingly.
        if (array_key_exists('status', $changedAttributes) && $changedAttributes['status'] != $this->status) {
            $redoSave = true;
            if ($this->status > 0 && $this->deleted_by > 0) {
                $this->deleted_by = 0;
            } elseif ($this->status == 0) {// if status is now 0 we can set deleted_by to the current user.
                $this->deleted_by = \Yii::$app->user->id;
                $this->deleted_at = gmdate('Y-m-d H:i:s');
            }
        } else {// status hasn't changed, so now we check if the deleted_by has been changed and adjust 'status' accordingly
            if (array_key_exists('deleted_by',$changedAttributes) && $changedAttributes['deleted_by'] != $this->deleted_by) {
                // we can only check if the deleted_by is now other than 0, in this case we can set the status to 0 (deleted).
                // if the deleted_by has been set to 0, where just not going to know which status we should adopt.  Maybe we
                // have to redirect them in this case to the update page asking them to choose manually.
                // so really... this would only happen when we're 'Un-Deleting'. So we don't offer them the ability to Un-delete,
                // problem solved :) .  But we should warn about hitting the 'Delete' button again. This would Permanently delete it.
                if ($this->deleted_by > 0) {
                    $redoSave = true;
                    $this->status = 0;
                }
            }

        }
        if ($redoSave) {
            $this->save(false);
        }

    }
}