<?php
namespace loggedIn;
use \AcceptanceTester;
use \Codeception\Util\Locator;

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
        $I->click('//*[@class=\'listing\']/descendant::tr[2]/descendant::td[2]/descendant::a');
        $I->see('Details');
        $I->see('Comments');
    }
}
