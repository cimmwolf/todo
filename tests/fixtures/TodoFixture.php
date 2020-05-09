<?php

namespace app\tests\fixtures;

use yii\test\ActiveFixture;

class TodoFixture extends ActiveFixture
{
    public $modelClass = 'app\models\Todo';
    public $depends = [
        'app\tests\fixtures\NoteFixture',
    ];
}
