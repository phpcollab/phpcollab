<?php
namespace loggedIn;
use \AcceptanceTester;

class ClientsCest
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
    public function listClients(AcceptanceTester $I)
    {
        $I->wantTo('See a list of Clients');
        $I->amOnPage('/clients/listclients.php');
        $I->seeInTitle('List Clients');
        try {
            $I->seeElement('.listing');
        } catch (\Exception $e) {
            $I->seeElement('.noItemsFound');
        }
    }
}
