<?php

namespace LoggedIn;

use AcceptanceTester;
use Exception;

class ProjectsCest
{
    private $projectDetails = [
        'name' => 'Codeception Project',
        'description' => 'This is a test project created by the ProjectsCest acceptance test',
        'status' => '3'
    ];
    private $projId;

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

    // tests

    /**
     * @param AcceptanceTester $I
     */
    public function listActiveProjects(AcceptanceTester $I)
    {
        $I->wantTo('See a list of active projects');
        $I->amOnPage('/projects/listprojects.php');
        $I->seeInTitle('List Active Projects');
        try {
            $I->seeElement('.listing');
        } catch (Exception $e) {
            $I->seeElement('.noItemsFound');
        }
    }

    /**
     * @param AcceptanceTester $I
     */
    public function listInactiveProjects(AcceptanceTester $I)
    {
        $I->wantTo('See a list of inactive projects');
        $I->amOnPage('/projects/listprojects.php?typeProjects=inactive');
        $I->seeInTitle('List Inactive Projects');
        try {
            $I->seeElement('.listing');
        } catch (Exception $e) {
            $I->seeElement('.noItemsFound');
        }
    }

    /**
     * @param AcceptanceTester $I
     */
    public function addProject(AcceptanceTester $I)
    {
        $I->wantTo('Add a project');
        $I->amOnPage('/projects/editproject.php');
        $I->seeInTitle('Add Project');
        $I->see('Add Project', ['css' => '.heading']);
        $I->dontSeeElement('.error');
        $I->seeElement('form', ['name' => 'epDForm']);
        $I->fillField("input[name=name]", $this->projectDetails["name"]);
        $I->fillField("textarea[name=description]", $this->projectDetails["description"]);
        $I->selectOption('select[name=status]', $this->projectDetails["status"]);
        $I->click('button[type="submit"]');
        $I->see('Success : Addition succeeded', ['css' => '.message']);
        $I->seeInCurrentUrl("/projects/viewproject.php");
        $this->projId = $I->grabFromCurrentUrl('~id=([^&#]*)~');
    }

    /**
     * @param AcceptanceTester $I
     * @depends addProject
     */
    public function viewProject(AcceptanceTester $I)
    {
        $I->wantTo('View a Project');
        $I->amOnPage('/projects/listprojects.php');
        $I->seeInTitle('List Active Projects');
        $I->seeElement('.listing');
        $I->click('.listing tr:nth-child(2) td:nth-child(2) a');
        $I->amOnPage('/projects/viewproject.php?id=' . $this->projId);
        $I->seeInTitle('View Project');
        $I->seeElement('.content');
        $I->see('Name :', ['css' => '.content']);
        $I->see('Project ID :', ['css' => '.content']);
        $I->see('Description :', ['css' => '.content']);
    }

    /**
     * @param AcceptanceTester $I
     * @depends viewProject
     */
    public function editProject(AcceptanceTester $I)
    {
        $I->wantTo('Edit a Project');
        $I->amOnPage('/projects/listprojects.php');
        $I->seeInTitle('List Active Projects');
        $I->seeElement('.listing');
        $I->click('.listing tr:nth-child(2) td:nth-child(2) a');
        $I->amOnPage('/projects/editproject.php?id=' . $this->projId . '&docopy=false');
        $I->seeInTitle('Edit Project');
        $I->seeElement('.content');
        $I->see('Name :', ['css' => '.content']);
        $I->see('Priority :', ['css' => '.content']);
        $I->see('Description :', ['css' => '.content']);
        $I->fillField("input[name=name]", $this->projectDetails["name"] . ' - edit');
        $I->click('button[type="submit"]');
        $I->see('Success : Modification succeeded', ['css' => '.message']);
    }

    /**
     * @param AcceptanceTester $I
     * @depends editProject
     */
    public function deleteProject(AcceptanceTester $I)
    {
        $I->wantTo('Delete a Project');
        $I->amOnPage('/projects/listprojects.php');
        $I->seeInTitle('List Active Projects');
        $I->seeElement('.listing');
        $I->see('Codeception Project - edit', ['css' => '.listing td']);
        $I->amOnPage('/projects/deleteproject.php?id=' . $this->projId);
        $I->see('Delete Projects', ['css' => '.heading']);
        $I->see('#' . $this->projId, '.content td.leftvalue');
        $I->see('Codeception Project - edit', ['css' => '.content']);
        $I->click('button[type="submit"]');
        $I->see('Success : Deletion succeeded', ['css' => '.message']);

    }

}
