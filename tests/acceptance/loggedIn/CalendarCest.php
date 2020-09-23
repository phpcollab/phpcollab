<?php

namespace loggedIn;

use AcceptanceTester;
use Codeception\Util\Locator;
use DateTime;
use Exception;

class CalendarCest
{
    protected $eventName;
    protected $eventId;
    protected $today;

    public function __construct()
    {
        $this->eventName = "Codeception Event Test";
        $this->today = new DateTime();
    }

    /**
     * @param AcceptanceTester $I
     */
    public function _before(AcceptanceTester $I)
    {
        $I->amOnPage('/general/login.php');
        $I->fillField(['name' => 'usernameForm'], 'testAdmin');
        $I->fillField(['name' => 'passwordForm'], 'testing');
        $I->click('input[type="submit"]');
    }

    /**
     * @param AcceptanceTester $I
     */
    public function _after(AcceptanceTester $I)
    {
    }

    /**
     * @param AcceptanceTester $I
     */
    public function viewMonthCalendar(AcceptanceTester $I)
    {
        $I->wantTo('See a Calendar for ' . $this->today->format('F'));
        $I->amOnPage('/calendar/viewcalendar.php');
        $I->seeInTitle('View Calendar');
        $I->see($this->today->format('F Y'), ['css' => 'h1.heading']);
        try {
            $I->seeElement('.listing');
        } catch (Exception $e) {
            $I->seeElement('.noItemsFound');
        }
    }

    /**
     * @param AcceptanceTester $I
     * @depends viewMonthCalendar
     */
    public function viewDayCalendar(AcceptanceTester $I)
    {
        $I->wantTo('See a Calendar for ' . $this->today->format('l d F Y'));
        $I->amOnPage('/calendar/viewcalendar.php?dateCalend=' . $this->today->format('Y-m-d') . '&type=dayList');
        $I->seeInTitle('View Calendar');
        $I->see($this->today->format('l d F Y'), ['css' => 'h1.heading']);
        try {
            $I->seeElement('.listing');
        } catch (Exception $e) {
            $I->seeElement('.noItemsFound');
        }
    }

    /**
     * @param AcceptanceTester $I
     * @depends viewDayCalendar
     */
    public function addCalendarEvent(AcceptanceTester $I)
    {
        $I->wantTo("Add calendar event");
        $I->amOnPage('/calendar/viewcalendar.php?type=calendEdit&dateCalend=' . $this->today->format('Y-m-d'));
        $I->submitForm('form', [
            'shortname' => $this->eventName,
            'subject' => 'My test event subject',
            'description' => 'My test event description'
        ]);
        $I->see('Success : Addition succeeded', ['css' => '.message']);
        $this->eventId = $I->grabFromCurrentUrl('~id=(\d+)~');
    }

    /**
     * @param AcceptanceTester $I
     *
     */
    public function editCalendarEvent(AcceptanceTester $I)
    {
        $I->wantTo("Edit calendar event");
        $I->amOnPage('/calendar/viewcalendar.php?type=dayList&dateCalend=' . $this->today->format('Y-m-d'));
        $I->seeElement('.listing');
        $I->seeLink($this->eventName);
        $I->click(Locator::contains('a', $this->eventName));
        $I->seeElement('.content');
        $I->see('Subject :', ['css' => '.content']);
        $I->see('Description :', ['css' => '.content']);
        $I->see('Short name', ['css' => '.content']);
        $I->amOnPage('/calendar/viewcalendar.php?type=calendEdit&dateCalend=' . $this->today->format('Y-m-d') . '&id=' . $this->eventId);
        $I->see('Edit: ' . $this->eventName, ['css' => '.heading']);
        $I->submitForm('form', [
            'shortname' => $this->eventName . ' - edited',
            'subject' => 'My test event subject - edited',
            'description' => 'My test event description - edited'
        ]);
        $I->see('Success : Modification succeeded', ['css' => '.message']);
        $I->amOnPage('/calendar/viewcalendar.php?dateCalend=' . $this->today->format('Y-m-d') . '&type=calendDetail&msg=update&id=' . $this->eventId);
        $I->see($this->eventName . " - edited", ['css' => '.content']);
    }

    /**
     * @param AcceptanceTester $I
     * @depends editCalendarEvent
     */
    public function deleteCalendarEvent(AcceptanceTester $I)
    {
        $I->wantTo('Delete calendar event');
        $I->amOnPage('/calendar/viewcalendar.php?dateCalend=' . $this->today->format('Y-m-d') . '&type=dayList');
        $I->seeElement('.listing');
        $I->seeLink($this->eventName . ' - edited');
        $I->click(Locator::contains('a', $this->eventName));
        $I->seeElement('.content');
        $I->see('Subject :', ['css' => '.content']);
        $I->see('Description :', ['css' => '.content']);
        $I->see('Short name', ['css' => '.content']);
        $I->amOnPage('/calendar/deletecalendar.php?id=' . $this->eventId);
        $I->see("Delete Calendar", ['css' => '.heading']);
        $I->seeElement('.content');
        $I->see('#' . $this->eventId, ['css' => '.content']);
        $I->see($this->eventName, ['css' => '.content']);
        $I->click('Delete');
        $I->see('Success : Deletion succeeded', ['css' => '.message']);
    }
}
