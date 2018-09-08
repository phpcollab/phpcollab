<?php
namespace loggedIn;
use \AcceptanceTester;

class CalendarCest
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
    public function listCalendar(AcceptanceTester $I)
    {
        $I->wantTo('See a Calendar');
        $I->amOnPage('/calendar/viewcalendar.php');
        $I->seeInTitle('View Calendar');
        try {
            $I->seeElement('.listing');
        } catch (\Exception $e) {
            $I->seeElement('.noItemsFound');
        }
    }
}
