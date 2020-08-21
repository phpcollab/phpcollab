<?php
namespace loggedIn;
use \AcceptanceTester;

class ReportsCest
{

    private $reportName = 'Codeception Report';
    private $reportId;

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

    /**
     * @param AcceptanceTester $I
     */
    public function noReports(AcceptanceTester $I)
    {
        $I->wantTo('See no Reports listed');
        $I->amOnPage('/reports/listreports.php');
        $I->seeInTitle('My Reports');
        $I->seeElement('.noItemsFound');
    }

    /**
     * @param AcceptanceTester $I
     */
    public function createReport(AcceptanceTester $I)
    {
        $I->wantTo('Create a Report');
        $I->amOnPage('/reports/createreport.php');
        $I->see('Create Report', ['css' => 'h1.heading']);
        $I->seeElement('form', ['name' => 'customsearchForm']);
        $I->click('input[type="submit"]');
        $I->amOnPage('/reports/resultsreport.php');
        $I->seeInTitle('Report Results');
        $I->see('Report Results', ['css' => 'h1.heading']);
    }

    /**
     * @param AcceptanceTester $I
     */
    public function saveReport(AcceptanceTester $I)
    {
        $I->wantTo('Save a Report');
        $I->amOnPage('/reports/createreport.php');
        $I->see('Create Report', ['css' => 'h1.heading']);
        $I->seeElement('form', ['name' => 'customsearchForm']);
        $I->submitForm('form[name=customsearchForm]', [
            'Save'
        ]);

        $I->amOnPage('/reports/resultsreport.php');
        $I->seeInTitle('Report Results');
        $I->see('Report Results', ['css' => 'h1.heading']);

        $I->amGoingTo('enter a report name and save');
        $I->fillField(['name' => 'report_name'], $this->reportName);
        $I->fillField(['name' => 'filterOrganization'], 'ALL');
        $I->fillField(['name' => 'filterProject'], 'ALL');
        $I->fillField(['name' => 'filterAssignedTo'], 'ALL');
        $I->fillField(['name' => 'filterStatus'], 'ALL');
        $I->fillField(['name' => 'filterStatus'], 'ALL');
        $I->click('button[type="submit"]');
        $I->see('Success : Created report');
        $I->see($this->reportName, ['css' => 'table.listing']);
        $I->seeInCurrentUrl('/reports/listreports.php?msg=addReport');

        // Since there should only be one report, grab its ID from the row checkbox so we can use it for the delete report test
        $this->reportId = preg_replace("/[^0-9s]/", "", $I->grabAttributeFrom('table.listing tr:last-child img', 'name'));
    }

    /**
     * @param AcceptanceTester $I
     */
    public function listReports(AcceptanceTester $I)
    {
        $I->wantTo('See a list of Reports');
        $I->amOnPage('/reports/listreports.php');
        $I->seeInTitle('My Reports');
        $I->seeElement('.listing');
        $I->see($this->reportName, ['css' => 'table.listing a']);
    }

    /**
     * @param AcceptanceTester $I
     */
    public function viewSavedReport(AcceptanceTester $I)
    {
        $I->wantTo('View a saved Report');
        $I->amOnPage('/reports/listreports.php');
        $I->seeInTitle('My Reports');
        $I->seeElement('.listing');
        $I->see($this->reportName, ['css' => 'table.listing a']);
        $I->click($this->reportName);
        $I->seeInCurrentUrl('/reports/resultsreport.php?id=' . $this->reportId);
        $I->see($this->reportName, ['css' => '.breadcrumbs']);
    }

    /**
     * @param AcceptanceTester $I
     */
    public function deleteReport(AcceptanceTester $I)
    {
        $I->wantTo('Delete a report');
        $I->amOnPage('/reports/deletereports.php?id=' . $this->reportId);
        $I->seeInTitle('Delete Report');
        $I->see('#' . $this->reportId);
        $I->see('Codeception Report', ['css' => '.content td']);
        $I->click('button[type="submit"]');
        $I->see('Success : Deleted reports');
        $I->seeElement('.noItemsFound');
    }
}
