<?php

namespace app\models;

use Yii;
use yii\base\{Exception, NotSupportedException};
use yii\db\{ActiveRecord, Query};
use yii\web\IdentityInterface;

/**
 * Это модель для таблицы "user".
 *
 * @property int $id
 * @property string $username
 * @property string $password
 * @property string|null $token
 * @property string|null $role
 *
 * @property array $notesIds
 */
class User extends ActiveRecord implements IdentityInterface
{
    public static function tableName()
    {
        return 'user';
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['token' => $token]);
    }

    /**
     * Метод-помощник для поиска пользователей по [[username]]
     * @param $username
     * @return User|null
     */
    public static function findByUsername($username)
    {
        return User::findOne(['username' => $username]);
    }

    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            [['username', 'password', 'token', 'role'], 'string', 'max' => 255],
            [['role'], 'default', 'value' => 'user']
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'       => 'ID',
            'username' => 'Username',
            'password' => 'Password',
            'token'    => 'Token',
            'role'     => 'Role',
        ];
    }

    public function getId()
    {
        return $this->id;
    }


    /**
     * @return string|void
     * @throws NotSupportedException
     */
    public function getAuthKey()
    {
        throw new NotSupportedException('"getAuthKey" is not implemented.');
    }

    /**
     * @param string $authKey
     * @return bool|void
     * @throws NotSupportedException
     */
    public function validateAuthKey($authKey)
    {
        throw new NotSupportedException('"validateAuthKey" is not implemented.');
    }

    /**
     * Проверяет пароль
     *
     * @param string $password
     * @return bool
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    /**
     * @param bool $insert
     * @return bool
     * @throws Exception
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        if (!empty($this->getDirtyAttributes(['password']))) {
            $this->password = Yii::$app->security->generatePasswordHash($this->password);
        }
        return true;
    }

    /**
     * @throws Exception
     */
    public function generateToken()
    {
        $this->token = Yii::$app->security->generateRandomString();
    }

    /**
     * Возвращает список ID Заметок, к которым у пользователя есть доступ
     * @return array
     */
    public function getNotesIds()
    {
        return (new Query())
            ->select('id')
            ->from(Note::tableName())
            ->where(['userId' => $this->id])
            ->column();
    }
}
