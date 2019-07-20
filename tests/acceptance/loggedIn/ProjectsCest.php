<?php
namespace loggedIn;
use \AcceptanceTester;

class ProjectsCest
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
    public function listActiveProjects(AcceptanceTester $I)
    {
        $I->wantTo('See a list of active projects');
        $I->amOnPage('/projects/listprojects.php');
        $I->seeInTitle('List Active Projects');
        try {
            $I->seeElement('.listing');
        } catch (\Exception $e) {
            $I->seeElement('.noItemsFound');
        }
    }

    public function listInactiveProjects(AcceptanceTester $I)
    {
        $I->wantTo('See a list of inactive projects');
        $I->amOnPage('/projects/listprojects.php?typeProjects=inactive');
        $I->seeInTitle('List Inactive Projects');
        try {
            $I->seeElement('.listing');
        } catch (\Exception $e) {
            $I->seeElement('.noItemsFound');
        }
    }

    public function viewProject(AcceptanceTester $I)
    {
        $I->wantTo('View a Project');
        $I->amOnPage('/projects/listprojects.php');
        $I->seeInTitle('List Active Projects');
        $I->seeElement('.listing');
        $I->click('.listing tr:nth-child(2) td:nth-child(2) a');
        $I->amOnPage('/projects/viewproject.php?id=1');
        $I->seeInTitle('View Project');
        $I->seeElement('.content');
        $I->see('Name :');
        $I->see('Project ID :');
        $I->see('Description :');
    }

}
