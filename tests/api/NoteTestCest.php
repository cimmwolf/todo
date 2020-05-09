<?php

use app\tests\fixtures\NoteFixture;
use Codeception\Util\HttpCode;

class NoteTestCest
{
    public function _before(ApiTester $I)
    {
    }

    public function _fixtures()
    {
        return [
            'profiles' => [
                'class' => NoteFixture::class,
            ],
        ];
    }

    public function testIndex(ApiTester $I)
    {
        $I->sendGET('/notes');
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);

        $I->amBearerAuthenticated('user-token');
        $I->sendGET('/notes');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->canSeeResponseContainsJson([['name' => 'note1']]);
        $I->cantSeeResponseContainsJson([['name' => 'note3']]);

        $I->amBearerAuthenticated('admin-token');
        $I->sendGET('/notes');
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->canSeeResponseContainsJson([['name' => 'note1'], ['name' => 'note2'], ['name' => 'note3']]);
    }

    public function testCreate(ApiTester $I)
    {
        $I->sendPOST('/notes', ['name' => 'foo']);
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);

        $I->amBearerAuthenticated('user-token');
        $I->sendPOST('/notes', ['name' => 'foo']);
        $I->seeResponseCodeIs(HttpCode::CREATED);
        $I->seeResponseContainsJson(['userId' => 1]);

        $I->sendPOST('/notes', ['name' => 'foo', 'userId' => 2]);
        $I->seeResponseCodeIs(HttpCode::FORBIDDEN);

        $I->amBearerAuthenticated('admin-token');
        $I->sendPOST('/notes', ['name' => 'foo', 'userId' => 1]);
        $I->seeResponseCodeIs(HttpCode::CREATED);
        $I->seeResponseContainsJson(['userId' => 1]);
    }

    public function testUpdate(ApiTester $I)
    {
        $I->sendPATCH('/notes/1', ['name' => 'foo']);
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);

        $I->amBearerAuthenticated('user-token');
        $I->sendPATCH('/notes/1', ['name' => 'foo']);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseContainsJson(['name' => 'foo']);

        $I->sendPATCH('/notes/3', ['name' => 'foo']);
        $I->seeResponseCodeIs(HttpCode::FORBIDDEN);

        $I->amBearerAuthenticated('admin-token');
        $I->sendPATCH('/notes/1', ['name' => 'foo']);
        $I->seeResponseCodeIs(HttpCode::OK);
    }

    public function testDelete(ApiTester $I)
    {
        $I->sendDELETE('/notes/1');
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);

        $I->amBearerAuthenticated('user-token');
        $I->sendDELETE('/notes/1');
        $I->seeResponseCodeIs(HttpCode::NO_CONTENT);

        $I->sendDELETE('/notes/3');
        $I->seeResponseCodeIs(HttpCode::FORBIDDEN);

        $I->amBearerAuthenticated('admin-token');
        $I->sendDELETE('/notes/2');
        $I->seeResponseCodeIs(HttpCode::NO_CONTENT);
    }
}
