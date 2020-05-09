<?php

use app\tests\fixtures\TodoFixture;
use Codeception\Util\HttpCode;

class TodoTestCest
{
    public function _before(ApiTester $I)
    {
    }

    public function _fixtures()
    {
        return [
            'profiles' => [
                'class' => TodoFixture::class,
            ],
        ];
    }

    public function testIndex(ApiTester $I)
    {
        $I->sendGET('/todos');
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);

        $I->amBearerAuthenticated('user-token');
        $I->sendGET('/todos');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->canSeeResponseContainsJson([['name' => 'todo1']]);
        $I->cantSeeResponseContainsJson([['name' => 'todo3']]);

        $I->amBearerAuthenticated('admin-token');
        $I->sendGET('/todos');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->canSeeResponseContainsJson([['name' => 'todo1'], ['name' => 'todo2'], ['name' => 'todo3']]);
    }

    public function testCreate(ApiTester $I)
    {
        $I->sendPOST('/todos', ['name' => 'foo', 'noteId' => 1]);
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);

        $I->amBearerAuthenticated('user-token');
        $I->sendPOST('/todos', ['name' => 'foo', 'noteId' => 2]);
        $I->seeResponseCodeIs(HttpCode::CREATED);

        $I->sendPOST('/todos', ['name' => 'foo', 'noteId' => 3]);
        $I->seeResponseCodeIs(HttpCode::FORBIDDEN);

        $I->amBearerAuthenticated('admin-token');
        $I->sendPOST('/todos', ['name' => 'foo', 'noteId' => 1]);
        $I->seeResponseCodeIs(HttpCode::CREATED);
        $I->seeResponseContainsJson(['noteId' => 1]);
    }

    public function testUpdate(ApiTester $I)
    {
        $I->sendPATCH('/todos/1', ['name' => 'foo']);
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);

        $I->amBearerAuthenticated('user-token');
        $I->sendPATCH('/todos/1', ['name' => 'foo']);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseContainsJson(['name' => 'foo']);

        $I->sendPATCH('/todos/3', ['name' => 'foo']);
        $I->seeResponseCodeIs(HttpCode::FORBIDDEN);

        $I->amBearerAuthenticated('admin-token');
        $I->sendPATCH('/todos/1', ['name' => 'foo']);
        $I->seeResponseCodeIs(HttpCode::OK);
    }

    public function testDelete(ApiTester $I)
    {
        $I->sendDELETE('/todos/1');
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);

        $I->amBearerAuthenticated('user-token');
        $I->sendDELETE('/todos/1');
        $I->seeResponseCodeIs(HttpCode::NO_CONTENT);

        $I->sendDELETE('/todos/3');
        $I->seeResponseCodeIs(HttpCode::FORBIDDEN);

        $I->amBearerAuthenticated('admin-token');
        $I->sendDELETE('/todos/2');
        $I->seeResponseCodeIs(HttpCode::NO_CONTENT);
    }
}
