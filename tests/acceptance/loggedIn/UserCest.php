<?php
namespace loggedIn;
use \AcceptanceTester;

class UserCest
{
    /**
     * @param AcceptanceTester $I
     */
    public function _before(AcceptanceTester $I)
    {
        $I->amOnPage('/general/login.php');
        $I->fillField(['name' => 'usernameForm'], 'testUser');
        $I->fillField(['name' => 'passwordForm'], 'testing');
        $I->click('input[type="submit"]');
    }

    /**
     * @param AcceptanceTester $I
     */
    public function _after(AcceptanceTester $I)
    {
    }

    // tests

    /**
     * @param AcceptanceTester $I
     */
    public function viewHomePage(AcceptanceTester $I)
    {
        $I->wantTo('See the home page');
        $I->amOnPage('/general/home.php');
        $I->see('PhpCollab : Home Page');
    }

    /**
     * @param AcceptanceTester $I
     * @depends viewHomePage
     */
    public function viewProfile(AcceptanceTester $I)
    {
        $I->wantTo('View my Profile');
        $I->amOnPage('/general/home.php');
        $I->click('#account > a:nth-child(2)');
        $I->seeElement('form', ['name' => 'user_edit_profileForm']);
    }

    /**
     * @param AcceptanceTester $I
     * @depends viewProfile
     */
    public function changePassword(AcceptanceTester $I)
    {
        $I->wantTo('Change my password');
        $I->amOnPage('/general/home.php');
        $I->click('#account > a:nth-child(2)');
        $I->seeElement('form', ['name' => 'user_edit_profileForm']);
        $I->click('.breadcrumbs > a:nth-child(1)');
        $I->seeElement('form', ['name' => 'change_passwordForm']);
    }

    /**
     * @param AcceptanceTester $I
     * @depends viewProfile
     */
    public function changeNotifications(AcceptanceTester $I)
    {
        $I->wantTo('Change my notifications');
        $I->amOnPage('/general/home.php');
        // Click the Preferences item
        $I->click('#account > a:nth-child(2)');
        $I->seeElement('form', ['name' => 'user_edit_profileForm']);
        $I->click('.breadcrumbs > a:nth-child(2)');
        $I->seeElement('form', ['name' => 'user_avertForm']);
    }

    /**
     * @param AcceptanceTester $I
     * @depends viewHomePage
     */
    public function logout(AcceptanceTester $I)
    {
        $I->wantTo('Logout');
        $I->amOnPage('/general/home.php');
        $I->click('#account > a:nth-child(1)');
        $I->see('You have successfully logged out. You can log back in by typing your user name and password below.', '.message');
    }
}
