<?php
namespace loggedIn;
use \AcceptanceTester;

class SearchCest
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
    public function listSearch(AcceptanceTester $I)
    {
        $I->wantTo('See a list of Search');
        $I->amOnPage('/search/createsearch.php');
        $I->seeInTitle('Search');
        $I->seeElement('form', ['name' => 'searchForm']);
        $I->dontSeeElement('.xdebug-error');
        $I->dontSeeElement('.xdebug-var-dump');
        $I->dontSee('Fatal error');
        $I->dontSee('Warning');
    }
}
