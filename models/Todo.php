<?php

namespace app\models;

use yii\behaviors\AttributeTypecastBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "todo".
 *
 * @property int $id
 * @property int $noteId
 * @property string $name
 * @property bool|null $status
 */
class Todo extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'todo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['noteId', 'name'], 'required'],
            [['noteId'], 'integer'],
            [['status'], 'boolean'],
            [['status'], 'default', 'value' => true]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'     => 'ID',
            'noteId' => 'Note ID',
            'name'   => 'Name',
            'status' => 'Status',
        ];
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
