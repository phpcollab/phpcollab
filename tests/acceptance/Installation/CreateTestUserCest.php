<?php
class CreateTestUserCest
{
    public function _before(AcceptanceTester $I)
    {
        $I->amOnPage('/general/login.php');
        $I->fillField(['name' => 'usernameForm'], 'admin');
        $I->fillField(['name' => 'passwordForm'], 'phpcollab');
        $I->click('input[type="submit"]');
    }

    /**
     * @param AcceptanceTester $I
     */
    public function createTestUser(AcceptanceTester $I)
    {
        $I->wantTo('I want to create a test user (testUser)');
        $I->amOnPage('/users/edituser.php');
        $I->seeInTitle('Add User');
        $I->see('Add User', ['css' => '.heading']);
        $I->submitForm('form', [
            'username' => 'testUser',
            'full_name'  => 'Codeception Test User',
            'email' => 'codeception_user@example.com',
            'password' => 'testing',
            'password_confirm' => 'testing'
        ]);
        $I->dontSeeElement('.headingError');
        $I->dontSeeElement('.error');
        $I->seeInCurrentUrl('/users/listusers.php?msg=add');
        $I->see('Success : Addition succeeded', ['css' => '.message']);
        $I->seeLink('Codeception Test User');
    }
}
