<?php

namespace app\models;

use Yii;
use yii\behaviors\AttributeTypecastBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "note".
 *
 * @property int $id
 * @property string $name
 * @property int|null $userId
 */
class Note extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'note';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['userId'], 'integer'],
            [['name'], 'string', 'max' => 60],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'     => 'ID',
            'name'   => 'Name',
            'userId' => 'User ID',
        ];
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        if (empty($this->userId) || $this->isNewRecord && Yii::$app->user->identity->role != 'admin') {
            $this->userId = Yii::$app->user->id;
        }
        return true;
    }

    public function behaviors()
    {
        return [
            'typecast' => [
                'class'                 => AttributeTypecastBehavior::class,
                'typecastAfterValidate' => true,
                'typecastBeforeSave'    => true,
                'typecastAfterFind'     => true,
            ],
        ];
    }
}
