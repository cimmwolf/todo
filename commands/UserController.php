<?php

namespace app\commands;

use app\models\User;
use yii\console\Controller;
use yii\helpers\Console;


class UserController extends Controller
{
    public function actionCreate($username, $password, $role = null)
    {
        $user = new User(['username' => $username, 'password' => $password, 'role' => $role]);

        if (!$user->save()) {
            Console::errorSummary($user);
            die;
        }
        Console::output("Пользователь $username создан");
    }
}
