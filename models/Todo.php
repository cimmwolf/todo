<?php

namespace app\models;

use yii\behaviors\AttributeTypecastBehavior;
use yii\db\ActiveRecord;

/**
 * Это модель для таблицы "todo".
 *
 * @property int $id
 * @property int $noteId
 * @property string $name
 * @property bool|null $status
 */
class Todo extends ActiveRecord
{
    public static function tableName()
    {
        return 'todo';
    }

    public function rules()
    {
        return [
            [['noteId', 'name'], 'required'],
            [['noteId'], 'integer'],
            [['status'], 'boolean'],
            [['status'], 'default', 'value' => true]
        ];
    }

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
