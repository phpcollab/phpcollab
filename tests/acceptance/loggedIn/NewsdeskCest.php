<?php
namespace loggedIn;
use \AcceptanceTester;

class NewsdeskCest
{
    public function _before(AcceptanceTester $I)
    {
        $I->amOnPage('/general/login.php');
        $I->fillField(['name' => 'usernameForm'], 'testUser');
        $I->fillField(['name' => 'passwordForm'], 'testing');
        $I->click('input[type="submit"]');
    }

    public function _after(AcceptanceTester $I)
    {
    }

    // tests
    public function listPosts(AcceptanceTester $I)
    {
        $I->wantTo('See a list of Newsdesk posts');
        $I->amOnPage('/newsdesk/listnews.php');
        $I->see('News list');
    }

    public function viewPost(AcceptanceTester $I)
    {
        $I->wantTo('View a newsdesk post');
        $I->amOnPage('/newsdesk/listnews.php');
        $I->see('News list');
        $I->click('.listing tr:nth-child(2) td:nth-child(2) a');
        $I->see('Details');
        $I->see('Comments');
    }
}
