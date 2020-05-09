<?php


namespace app\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\BaseActiveRecord;
use yii\filters\{AccessControl, auth\HttpBearerAuth};
use yii\rest\ActiveController;
use yii\web\ForbiddenHttpException;

class NoteController extends ActiveController
{
    public $modelClass = 'app\models\Note';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
        ];
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];
        return $behaviors;
    }

    public function checkAccess($action, $model = null, $params = [])
    {
        if (Yii::$app->user->identity->role != 'admin') {
            if ($action === 'create'
                && !empty(Yii::$app->request->post('userId'))
                && Yii::$app->user->id != Yii::$app->request->post('userId')) {
                throw new ForbiddenHttpException(
                    sprintf('You can only %s notes that belongs to you.', $action)
                );
            }
            if ($model != null) {
                if ($model->userId !== Yii::$app->user->id) {
                    throw new ForbiddenHttpException(
                        sprintf('You can only %s notes that you\'ve created.', $action)
                    );
                }
            }
        }
    }

    public function actions()
    {
        $actions = parent::actions();

        unset($actions['options']);
        unset($actions['view']);

        $actions['index']['prepareDataProvider'] = function ($action, $filter) {
            $requestParams = Yii::$app->getRequest()->getBodyParams();
            if (empty($requestParams)) {
                $requestParams = Yii::$app->getRequest()->getQueryParams();
            }

            /* @var $modelClass BaseActiveRecord */
            $modelClass = $this->modelClass;

            $query = $modelClass::find();
            if (!empty($filter)) {
                $query->andWhere($filter);
            }

            if (Yii::$app->user->identity->role != 'admin') {
                $query->andWhere(['userId' => Yii::$app->user->id]);
            }

            return Yii::createObject(
                [
                    'class'      => ActiveDataProvider::class,
                    'query'      => $query,
                    'pagination' => [
                        'params' => $requestParams,
                    ],
                    'sort'       => [
                        'params' => $requestParams,
                    ],
                ]
            );
        };

        return $actions;
    }
}
