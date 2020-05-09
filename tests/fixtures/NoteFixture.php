<?php

namespace app\tests\fixtures;

use yii\test\ActiveFixture;

class NoteFixture extends ActiveFixture
{
    public $modelClass = 'app\models\Note';
    public $depends = [
        'app\tests\fixtures\UserFixture',
    ];
}
