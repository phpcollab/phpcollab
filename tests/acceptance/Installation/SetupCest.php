<?php namespace Installation;

use AcceptanceTester;
use Exception;

class SetupCest
{
    private $url;
    private $dbServer = '192.168.74.110';
    private $dbName = 'phpc_release';
    private $dbLogin = 'phpc_release';
    private $dbPassword = 'phpc_release';
    private $dbTablePrefix = 'codeception_';
    private $adminPassword = "phpcollab";

    /**
     * @param AcceptanceTester $I
     */
    public function _before(AcceptanceTester $I)
    {
    }

    /**
     * @param AcceptanceTester $I
     */
    public function setupStep1(AcceptanceTester $I)
    {
        $I->wantTo('see the setup screen (Step 1)');
        $I->amOnPage('/installation/setup.php');
        $I->see('License', ['css' => '.heading']);
        $I->dontSeeElement('.error');
    }

    /**
     * @param AcceptanceTester $I
     * @depends setupStep1
     */
    public function setupStep2(AcceptanceTester $I)
    {
        $I->wantTo('see the setup screen (Step 2)');
        $I->amOnPage('/installation/setup.php');
        $I->checkOption('#license input[name=connection]');
        $I->click('input[type="submit"]');
        $I->see('Settings', ['css' => '.heading']);
        $I->dontSeeElement('.error');
    }

    /**
     * @param AcceptanceTester $I
     * @depends setupStep2
     */
    public function setupStep3(AcceptanceTester $I)
    {
        $I->wantTo('see the setup screen (Step 3)');
        $I->amOnPage('/installation/setup.php?step=2&connection=off');
        $I->see('Settings', ['css' => '.heading']);
        $I->dontSeeElement('.error');
        $I->seeElement('form', ['name' => 'settingsForm']);
        $I->fillField(['name' => 'dbServer'], $this->dbServer);
        $I->fillField(['name' => 'dbLogin'], $this->dbLogin);
        $I->fillField(['name' => 'dbPassword'], $this->dbPassword);
        $I->fillField(['name' => 'dbName'], $this->dbName);
        $I->fillField(['name' => 'dbTablePrefix'], $this->dbTablePrefix);
        $I->fillField(['name' => 'adminPassword'], $this->adminPassword);
        $I->fillField(['name' => 'adminEmail'], $this->adminPassword);
        $I->click('input[type="submit"]');
        $I->dontSeeElement('.error');
        $I->see('Success', ['css' => '.heading']);
        $I->see('phpCollab has successfully been installed.');
        $I->see('Please log in');
    }

    public function testLogin(AcceptanceTester $I)
    {
        $I->wantTo('Access the admin section');
        $I->amOnPage('/general/login.php');
        $I->fillField(['name' => 'usernameForm'], 'admin');
        $I->fillField(['name' => 'passwordForm'], $this->adminPassword);
        $I->click('input[type="submit"]');
        $I->amOnPage('/administration/admin.php');
        $I->seeInTitle('Administration');
        $I->see('Administration', ['css' => '.heading']);

    }
}
