<?php

namespace app\controllers;

use app\models\LoginForm;
use app\models\User;
use Yii;
use yii\base\Exception;
use yii\filters\VerbFilter;
use yii\rest\Controller;
use yii\web\HttpException;
use yii\web\Response;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = [
            'class'   => VerbFilter::class,
            'actions' => [
                'logout'   => ['post'],
                'register' => ['post'],
            ]
        ];
        return $behaviors;
    }

    /**
     * Login action.
     *
     * @return array
     * @throws Exception
     */
    public function actionLogin()
    {
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $model->token;
        }
        throw new HttpException(401);
    }

    /**
     * Регистрирует нового пользователя.
     *
     * @return array
     * @throws HttpException|Exception
     */
    public function actionRegister()
    {
        $username = Yii::$app->request->post('username');
        $password = Yii::$app->request->post('password');
        if (empty($username) || empty($password)) {
            throw new HttpException(400);
        }

        if (!empty(User::findByUsername($username))) {
            throw new HttpException(409);
        }

        $user = new User(['username' => $username, 'password' => $password]);
        $user->generateToken();
        if (!$user->save()) {
            Yii::$app->response->statusCode = 500;
            return $user->getErrorSummary(false);
        }

        Yii::$app->response->setStatusCode(201);
        return ['token' => $user->token];
    }
}
