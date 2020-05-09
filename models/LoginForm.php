<?php

namespace app\models;

use yii\base\Exception;
use yii\base\Model;

/**
 * LoginForm это модель для аутентификации пользователя.
 *
 * @property User|null $user Только для чтения.
 * @property array $token
 *
 */
class LoginForm extends Model
{
    public $username;
    public $password;

    /**
     * @var User|bool
     */
    private $_user = false;

    public function formName()
    {
        return '';
    }

    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Проверяет пароль.
     * Это метод для встроенной проверки проверки пароля в rules().
     *
     * @param string $attribute атрибут, который проверяется
     * @param array $params дополнительные параметры из правила
     * @noinspection PhpUnusedParameterInspection
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Неправильное имя пользователя или пароль.');
            }
        }
    }

    /**
     * Ищет пользователя по [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }

    /**
     * Генерирует новый токен для пользователя, если аутентификация прошла успешно.
     * @return bool признак того прошла ли аутентификация успешно
     * @throws Exception
     */
    public function login()
    {
        if ($this->validate()) {
            $this->_user->generateToken();
            if ($this->_user->save()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Формирует ответ, который отправится клиенту после аутентификации
     * @return array
     */
    public function getToken()
    {
        return ['token' => $this->user->token];
    }
}
