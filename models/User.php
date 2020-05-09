<?php

namespace app\models;

use Yii;
use yii\base\{Exception, NotSupportedException};
use yii\db\{ActiveRecord, Query};
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
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
    /**
     * {@inheritdoc}
     */
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

    public static function findByUsername($username)
    {
        return User::findOne(['username' => $username]);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            [['username', 'password', 'token', 'role'], 'string', 'max' => 255],
            [['role'], 'default', 'value' => 'user']
        ];
    }

    /**
     * {@inheritdoc}
     */
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
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
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

    public function getNotesIds()
    {
        return (new Query())
            ->select('id')
            ->from(Note::tableName())
            ->where(['userId' => $this->id])
            ->column();
    }
}
