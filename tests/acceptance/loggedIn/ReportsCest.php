<?php
namespace loggedIn;
use \AcceptanceTester;

class ReportsCest
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
    public function listReports(AcceptanceTester $I)
    {
        $I->wantTo('See a list of Reports');
        $I->amOnPage('/reports/listreports.php');
        $I->seeInTitle('My Reports');
        try {
            $I->seeElement('.listing');
        } catch (\Exception $e) {
            $I->seeElement('.noItemsFound');
        }
    }
}
