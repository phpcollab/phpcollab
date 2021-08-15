<?php namespace LoggedIn;


use AcceptanceTester;

class TasksCest
{
    private $taskId = 185;
    private $newTaskId;
    private $commentLink;

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
    public function seeHomeViewTaskList(AcceptanceTester $I)
    {
        $I->wantTo('See a list of tasks on my home page');
        $I->amOnPage('/general/home.php');
        $I->see('PhpCollab : Home Page', ['css' => 'header > h1']);
        $I->seeElement('form', ['name' => 'home_tasksForm']);
        $I->dontSeeElement('form[name="home_tasksForm"] div.noItemsFound');
        $I->seeElement('#home_tasks table.listing');
    }

    /**
     * @param AcceptanceTester $I
     * @depends seeHomeViewTaskList
     */
    public function viewTask(AcceptanceTester $I)
    {
        $I->wantTo('View a task assigned to me');
        $I->amOnPage('/tasks/viewtask.php?id=' . $this->taskId);
        $I->see('Task : Client task', ['css' => '.heading']);
        $I->see('Assigned to :', ['css' => 'table.content .leftvalue']);
        $I->see('Testing User', ['css' => 'table.content a']);
        $I->see('No', ".//tr/td[contains(text(),'Published')]/following-sibling::td");
    }

    /**
     * @param AcceptanceTester $I
     * @depends viewTask
     */
    public function publishTask(AcceptanceTester $I)
    {
        $I->wantTo('Publish a task');
        $I->amOnPage('/tasks/viewtask.php?id=' . $this->taskId);
        $I->see('Task : Client task', ['css' => '.heading']);
        $I->see('Assigned to :', ['css' => 'table.content .leftvalue']);
        $I->see('Testing User', ['css' => 'table.content a']);

        $I->amOnPage('/tasks/viewtask.php?addToSite=true&action=publish&id=' . $this->taskId);
        $I->see('Success : The addition to the project site succeeded.', '.message');
        $I->see('Yes', ".//tr/td[contains(text(),'Published')]/following-sibling::td");
    }

    /**
     * @param AcceptanceTester $I
     * @depends publishTask
     */
    public function unpublishTask(AcceptanceTester $I)
    {
        $I->wantTo('Un-publish a task');
        $I->amOnPage('/tasks/viewtask.php?id=' . $this->taskId);
        $I->see('Task : Client task', ['css' => '.heading']);
        $I->see('Assigned to :', ['css' => 'table.content .leftvalue']);
        $I->see('Testing User', ['css' => 'table.content a']);

        $I->amOnPage('/tasks/viewtask.php?removeToSite=true&action=publish&id=' . $this->taskId);
        $I->see('Success : The removal from the project site succeeded.', ['css' => '.message']);
        $I->see('No', ".//tr/td[contains(text(),'Published')]/following-sibling::td");
    }

    /**
     * @param AcceptanceTester $I
     * @depends unpublishTask
     */
    public function copyTask(AcceptanceTester $I)
    {
        $I->wantTo('Copy a task');
        $I->amOnPage('/tasks/edittask.php?project=1&task=185&docopy=true');
        $I->see('Copy Task : Client task', '.heading');
        $I->seeInField(['name' => 'task_name'], 'Copy of Client task');
        $I->fillField(['name' => 'task_name'], 'Copy of Client task - codeception');
        $I->click('button[type="submit"]');
        $I->see('Success : Addition succeeded', '.message');
        $this->newTaskId = $I->grabFromCurrentUrl('~id=([^&#]*)~');
        $this->commentLink = $I->grabAttributeFrom('.message a', 'href');
    }

    /**
     * @param AcceptanceTester $I
     * @depends copyTask
     */
    public function addAssignmentComment(AcceptanceTester $I)
    {
        $I->wantTo('Add an assignment comment');
        $I->amOnPage('/tasks/' . $this->commentLink);
        $I->see('Assignment Comment', '.heading');
        $I->see('Copy of Client task - codeception', ['css' => '.content']);
        $I->fillField(['name' => 'comment'], 'Codeception assignment comment');
        $I->click('button[type="submit"]');
        $I->see('Success : Modification succeeded', '.message');
        $I->seeElement('#ahT');
        $I->see('Codeception assignment comment', '#ahT');
    }

    /**
     * @param AcceptanceTester $I
     * @depends copyTask
     */
    public function editTask(AcceptanceTester $I)
    {
        $I->wantTo('Edit a task');
        $I->amOnPage('/tasks/edittask.php?project=1&docopy=false&task=' . $this->newTaskId);
        $I->see('Copy of Client task - codeception', '.heading');
        $I->seeInField(['name' => 'task_name'], 'Copy of Client task - codeception');
        $I->fillField(['name' => 'task_name'], 'Copy of Client task - codeception - edit');
        $I->click('button[type="submit"]');
        $I->see('Success : Modification succeeded', '.message');
        $I->see('Copy of Client task - codeception - edit', ".//tr/td[contains(text(),'Name')]/following-sibling::td");
    }

    /**
     * @param AcceptanceTester $I
     * @depends copyTask
     */
    public function deleteTask(AcceptanceTester $I)
    {
        $I->wantTo('Delete a task');
        $I->amOnPage('/tasks/deletetasks.php?project=1&id=' . $this->newTaskId);
        $I->see('Delete Tasks', '.heading');
        $I->see('#' . $this->newTaskId, '.content .leftvalue');
        $I->see('Copy of Client task - codeception - edit', ['css' => '.content']);
        $I->click('button[type="submit"]');
        $I->see('Success : Deletion succeeded', '.message');
    }
}
