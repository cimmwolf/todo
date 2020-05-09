<?php

use app\tests\fixtures\UserFixture;
use Codeception\Util\HttpCode;

class UserTestCest
{
    public function _before(ApiTester $I)
    {
    }

    public function _fixtures()
    {
        return [
            'profiles' => [
                'class' => UserFixture::class,
            ],
        ];
    }

    public function testWrongRegistration(ApiTester $I)
    {
        $I->sendPost('/register', ['username' => 'new-user', 'password' => '']);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);

        $I->sendPost('/register', ['username' => '', 'password' => 'password']);
        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
    }

    public function testRegistration(ApiTester $I)
    {
        $I->sendPost('/register', ['username' => 'new-user', 'password' => 'password']);
        $I->seeResponseCodeIs(HttpCode::CREATED);
        $I->seeResponseMatchesJsonType(['token' => 'string']);
    }

    public function testRegisterExistUsername(ApiTester $I)
    {
        $I->sendPost('/register', ['username' => 'exist-user', 'password' => 'password']);
        $I->seeResponseCodeIs(HttpCode::CONFLICT);
    }

    public function testLogin(ApiTester $I)
    {
        $I->sendPost('/login', ['username' => 'exist-user', 'password' => 'password']);
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseMatchesJsonType(['token' => 'string']);
    }

    public function testWrongLogin(ApiTester $I)
    {
        $I->sendPost('/login', ['username' => 'exist-user', 'password' => 'wrong-password']);
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);

        $I->sendPost('/login', ['username' => 'wrong-user', 'password' => 'password']);
        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
    }
}
