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
    public function seeListOfTasksOnHomeView(AcceptanceTester $I)
    {
        $I->wantTo('See a list of tasks on my home page');
        $I->amOnPage('/general/home.php');
        $I->see('PhpCollab : Home Page');
        $I->seeElement('form', ['name' => 'xwbTForm']);
        $I->dontSeeElement('form[name="xwbTForm"] div.noItemsFound');
        $I->seeElement('#xwbT table.listing');
    }

    /**
     * @param AcceptanceTester $I
     */
    public function viewTask(AcceptanceTester $I)
    {
        $I->wantTo('View a task assigned to me');
        $I->amOnPage('/tasks/viewtask.php?id=' . $this->taskId);
        $I->see('Task : Client task');
        $I->see('Assigned to :', ['css' => 'table.content td.leftvalue']);
        $I->see('Testing User', ['css' => 'table.content a']);
        $I->see('No', ".//tr/td[contains(text(),'Published')]/following-sibling::td");
    }

    /**
     * @param AcceptanceTester $I
     */
    public function publishTask(AcceptanceTester $I)
    {
        $I->wantTo('Publish a task');
        $I->amOnPage('/tasks/viewtask.php?id=' . $this->taskId);
        $I->see('Task : Client task');
        $I->see('Assigned to :', ['css' => 'table.content td.leftvalue']);
        $I->see('Testing User', ['css' => 'table.content a']);

        $I->amOnPage('/tasks/viewtask.php?addToSite=true&action=publish&id=' . $this->taskId);
        $I->see('Success : The addition to the project site succeeded.', 'table.message td');
        $I->see('Yes', ".//tr/td[contains(text(),'Published')]/following-sibling::td");
    }

    /**
     * @param AcceptanceTester $I
     */
    public function unpublishTask(AcceptanceTester $I)
    {
        $I->wantTo('Un-publish a task');
        $I->amOnPage('/tasks/viewtask.php?id=' . $this->taskId);
        $I->see('Task : Client task');
        $I->see('Assigned to :', ['css' => 'table.content td.leftvalue']);
        $I->see('Testing User', ['css' => 'table.content a']);

        $I->amOnPage('/tasks/viewtask.php?removeToSite=true&action=publish&id=' . $this->taskId);
        $I->see('Success : The removal from the project site succeeded.');
        $I->see('No', ".//tr/td[contains(text(),'Published')]/following-sibling::td");
    }

    /**
     * @param AcceptanceTester $I
     */
    public function copyTask(AcceptanceTester $I)
    {
        $I->wantTo('Copy a task');
        $I->amOnPage('/tasks/edittask.php?project=1&task=185&docopy=true');
        $I->see('Copy Task : Client task', 'h1.heading');
        $I->seeInField(['name' => 'task_name'], 'Copy of Client task');
        $I->submitForm('form', [
            'task_name' => 'Copy of Client task - codeception',
        ]);
        $I->see('Success : Addition succeeded', 'table.message td');
        $this->newTaskId = $I->grabFromCurrentUrl('~id=([^&#]*)~');
        $this->commentLink = $I->grabAttributeFrom('.message a', 'href');
    }

    /**
     * @param AcceptanceTester $I
     */
    public function addAssignmentComment(AcceptanceTester $I)
    {
        $I->wantTo('Add an assignment comment');
        $I->amOnPage('/tasks/' . $this->commentLink); // assignmentcomment.php?task=281&id=314');
        $I->see('Assignment Comment', 'h1.heading');
        $I->see('Copy of Client task - codeception');
        $I->submitForm('form', [
            'acomm' => 'Codeception assignment comment',
        ]);
        $I->see('Success : Modification succeeded', '.message');
        $I->seeElement('#ahT');
        $I->see('Codeception assignment comment', '#ahT');
    }

    /**
     * @param AcceptanceTester $I
     */
    public function editTask(AcceptanceTester $I)
    {
        $I->wantTo('Edit a task');
        $I->amOnPage('/tasks/edittask.php?project=1&docopy=false&task=' . $this->newTaskId);
        $I->see('Copy of Client task - codeception', 'h1.heading');
        $I->seeInField(['name' => 'task_name'], 'Copy of Client task - codeception');
        $I->submitForm('form', [
            'task_name' => 'Copy of Client task - codeception - edit',
        ]);
        $I->see('Success : Modification succeeded', 'table.message td');
        $I->see('Copy of Client task - codeception - edit', ".//tr/td[contains(text(),'Name')]/following-sibling::td");
    }

    /**
     * @param AcceptanceTester $I
     */
    public function deleteTask(AcceptanceTester $I)
    {
        $I->wantTo('Delete a task');
        $I->amOnPage('/tasks/deletetasks.php?project=1&id=' . $this->newTaskId);
        $I->see('Delete Tasks', 'h1.heading');
        $I->see('#' . $this->newTaskId, '.content td.leftvalue');
        $I->see('Copy of Client task - codeception - edit');
        $I->click('input[type="submit"]');
        $I->see('Success : Deletion succeeded', '.message td');
    }
}
