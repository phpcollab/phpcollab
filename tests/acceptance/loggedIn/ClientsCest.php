<?php
namespace loggedIn;
use \AcceptanceTester;
use Exception;

class ClientsCest
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

    /**
     * @param AcceptanceTester $I
     */
    public function listClients(AcceptanceTester $I)
    {
        $I->wantTo('See a list of Clients');
        $I->amOnPage('/clients/listclients.php');
        $I->seeInTitle('List Clients');
        try {
            $I->seeElement('.listing');
        } catch (Exception $e) {
            $I->seeElement('.noItemsFound');
        }
    }

    /**
     * @param AcceptanceTester $I
     */
    public function viewClient(AcceptanceTester $I)
    {
        $I->wantTo('View details about a client');
        $I->amOnPage('/clients/listclients.php');
        $I->seeInTitle('List Clients');
        $I->amGoingTo('select the first client in the list and navigate to it');
        $I->dontSeeElement('.noItemsFound');
        $I->click('.listing tr:nth-child(2) td:nth-child(2) a');
        $I->seeElement('.content');
    }
}
