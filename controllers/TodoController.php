<?php


namespace app\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\BaseActiveRecord;
use yii\filters\{AccessControl, auth\HttpBearerAuth};
use yii\rest\ActiveController;
use yii\web\ForbiddenHttpException;

class TodoController extends ActiveController
{
    public $modelClass = 'app\models\Todo';

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
            if ($action === 'create' && !in_array(
                    Yii::$app->request->post('noteId'),
                    Yii::$app->user->identity->notesIds
                )) {
                throw new ForbiddenHttpException(
                    sprintf('You can only %s todos in notes that you\'ve created.', $action)
                );
            }
            if ($model != null) {
                if (!in_array($model->noteId, Yii::$app->user->identity->notesIds)) {
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
                $query->andWhere(['in', 'noteId', Yii::$app->user->identity->notesIds]);
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
