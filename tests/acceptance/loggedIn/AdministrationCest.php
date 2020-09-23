<?php namespace LoggedIn;


use AcceptanceTester;
use Exception;

class AdministrationCest
{
    private $userId;

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
    public function accessAdminSection(AcceptanceTester $I)
    {
        $I->wantTo('Access the admin section');
        $I->amOnPage('/administration/admin.php');
        $I->seeInTitle('Administration');
        $I->see('Administration', ['css' => '.heading']);
    }

    /**
     * @param AcceptanceTester $I
     * @depends accessAdminSection
     */
    public function viewLoginLogs(AcceptanceTester $I)
    {
        $I->wantTo('View Login Logs');
        $I->amOnPage('/administration/admin.php');
        $I->seeInTitle('Administration');
        $I->see('Administration', ['css' => '.heading']);
        $I->click('Logs');
        $I->seeInTitle('Logs');
        $I->dontSeeElement('.error');
    }

    /**
     * @param AcceptanceTester $I
     * @depends accessAdminSection
     */
    public function viewSystemInformation(AcceptanceTester $I)
    {
        $I->wantTo('View System Information');
        $I->amOnPage('/administration/admin.php');
        $I->seeInTitle('Administration');
        $I->see('Administration', ['css' => '.heading']);
        $I->click('System Information');
        $I->seeInTitle('System Information');
        $I->dontSeeElement('.error');
    }

    /**
     * @param AcceptanceTester $I
     * @depends accessAdminSection
     */
    public function viewCompanyDetails(AcceptanceTester $I)
    {
        $I->wantTo('View Company Details');
        $I->amOnPage('/administration/admin.php');
        $I->seeInTitle('Administration');
        $I->see('Administration', ['css' => '.heading']);
        $I->click('Company Details');
        $I->seeInTitle('Company Details');
        $I->dontSeeElement('.error');
    }

    /**
     * @param AcceptanceTester $I
     * @depends viewCompanyDetails
     */
    public function editCompanyDetails(AcceptanceTester $I)
    {
        $I->wantTo('Edit Company Details');
        $I->amOnPage('/administration/admin.php');
        $I->seeInTitle('Administration');
        $I->see('Administration', ['css' => '.heading']);
        $I->click('Company Details');
        $I->seeInTitle('Company Details');
        $I->dontSeeElement('.error');

        $orgName = $I->grabValueFrom('input[name=org_name]');
        $I->submitForm('form', [
            'org_name' => $orgName . ' - edit',
            'action'  => 'update'
        ]);
        $I->see('Success : Modification succeeded', ['css' => '.message']);
        $I->seeInField(['name' => 'org_name'], $orgName . ' - edit');
    }

    /**
     * @param AcceptanceTester $I
     * @depends editCompanyDetails
     */
    public function undoEditCompanyDetails(AcceptanceTester $I)
    {
        $I->wantTo('Undo Edit Company Details');
        $I->amOnPage('/administration/admin.php');
        $I->seeInTitle('Administration');
        $I->see('Administration', ['css' => '.heading']);
        $I->click('Company Details');
        $I->seeInTitle('Company Details');
        $I->dontSeeElement('.error');

        $orgName = $I->grabValueFrom('input[name=org_name]');
        $orgName = str_replace(" - edit", "", $orgName);
        $I->submitForm('form', [
            'org_name' => $orgName,
            'action'  => 'update'
        ]);
        $I->see('Success : Modification succeeded', ['css' => '.message']);
        $I->seeInField(['name' => 'org_name'], $orgName);
    }

    /**
     * @param AcceptanceTester $I
     * @depends accessAdminSection
     */
    public function viewSettings(AcceptanceTester $I)
    {
        $I->wantTo('View Settings');
        $I->amOnPage('/administration/admin.php');
        $I->seeInTitle('Administration');
        $I->see('Administration', ['css' => '.heading']);
        $I->click('Edit settings');
        $I->seeInTitle('Edit Settings');
        $I->dontSeeElement('.error');
    }

    /**
     * @param AcceptanceTester $I
     * @depends accessAdminSection
     */
    public function viewEditDatabase(AcceptanceTester $I)
    {
        $I->wantTo('View Edit Database Screen');
        $I->amOnPage('/administration/admin.php');
        $I->seeInTitle('Administration');
        $I->see('Administration', ['css' => '.heading']);
        $I->click('Edit database');
        $I->seeInTitle('Edit Database');
        $I->dontSeeElement('.error');
    }

    /**
     * @param AcceptanceTester $I
     * @depends accessAdminSection
     */
    public function viewUserManagement(AcceptanceTester $I)
    {
        $I->wantTo('View User List');
        $I->amOnPage('/administration/admin.php');
        $I->seeInTitle('Administration');
        $I->see('Administration', ['css' => '.heading']);
        $I->click('User Management');
        $I->seeInTitle('List Users');
        $I->dontSeeElement('.error');
        $I->seeInCurrentUrl('/users/listusers.php');
    }

    /**
     * @param AcceptanceTester $I
     * @depends viewUserManagement
     */
    public function addNewUserWithError(AcceptanceTester $I)
    {
        $I->wantTo('Add New User with non-alphanumeric username Error');
        $I->amOnPage('/users/edituser.php');
        $I->seeInTitle('Add User');
        $I->see('Add User', ['css' => '.heading']);
        $I->submitForm('form', [
            'username' => 'codeception_user',
            'full_name'  => 'Codeception User',
            'email' => 'codeception_user@example.com'
        ]);
        $I->seeElement('.headingError');
        $I->seeElement('.error');
        $I->see('Alpha-numeric only in login', ['css' => '.error']);
    }

    /**
     * @param AcceptanceTester $I
     * @depends viewUserManagement
     */
    public function addNewUserWithoutPasswordAndPasswordConfirm(AcceptanceTester $I)
    {
        $I->wantTo('Add New User Without Password and Password Confirm');
        $I->amOnPage('/users/edituser.php');
        $I->seeInTitle('Add User');
        $I->see('Add User', ['css' => '.heading']);
        $I->submitForm('form', [
            'username' => 'CodeceptionUser',
            'full_name'  => 'Codeception User',
            'email' => 'codeception_user@example.com'
        ]);
        $I->seeElement('.headingError');
        $I->seeElement('.error');
        $I->see('The two passwords you entered did not match. Please re-enter your new password.', ['css' => '.error']);
    }

    /**
     * @param AcceptanceTester $I
     * @depends viewUserManagement
     */
    public function addNewUserWitOnlyPassword(AcceptanceTester $I)
    {
        $I->wantTo('Add New User With Only Password');
        $I->amOnPage('/users/edituser.php');
        $I->seeInTitle('Add User');
        $I->see('Add User', ['css' => '.heading']);
        $I->submitForm('form', [
            'username' => 'CodeceptionUser',
            'full_name'  => 'Codeception User',
            'email' => 'codeception_user@example.com',
            'password' => 'abc123'
        ]);
        $I->seeElement('.headingError');
        $I->seeElement('.error');
        $I->see('The two passwords you entered did not match. Please re-enter your new password.', ['css' => '.error']);
    }

    /**
     * @param AcceptanceTester $I
     * @depends viewUserManagement
     */
    public function addNewUserWithOnlyPasswordConfirm(AcceptanceTester $I)
    {
        $I->wantTo('Add New User With Only Password Confirm');
        $I->amOnPage('/users/edituser.php');
        $I->seeInTitle('Add User');
        $I->see('Add User', ['css' => '.heading']);
        $I->submitForm('form', [
            'username' => 'CodeceptionUser',
            'full_name'  => 'Codeception User',
            'email' => 'codeception_user@example.com',
            'password_confirm' => 'abc123'
        ]);
        $I->seeElement('.headingError');
        $I->seeElement('.error');
        $I->see('The two passwords you entered did not match. Please re-enter your new password.', ['css' => '.error']);
    }

    /**
     * @param AcceptanceTester $I
     * @depends viewUserManagement
     */
    public function addNewUserWithNonMatchingPasswords(AcceptanceTester $I)
    {
        $I->wantTo('Add New User Without Matching Passwords');
        $I->amOnPage('/users/edituser.php');
        $I->seeInTitle('Add User');
        $I->see('Add User', ['css' => '.heading']);
        $I->submitForm('form', [
            'username' => 'CodeceptionUser',
            'full_name'  => 'Codeception User',
            'email' => 'codeception_user@example.com',
            'password' => 'abc123',
            'password_confirm' => 'xyz456'
        ]);
        $I->seeElement('.headingError');
        $I->seeElement('.error');
        $I->see('The two passwords you entered did not match. Please re-enter your new password.', ['css' => '.error']);
    }

    /**
     * @param AcceptanceTester $I
     * @depends viewUserManagement
     */
    public function addNewUser(AcceptanceTester $I)
    {
        $I->wantTo('Add New User (Codeception User)');
        $I->amOnPage('/users/edituser.php');
        $I->seeInTitle('Add User');
        $I->see('Add User', ['css' => '.heading']);
        $I->submitForm('form', [
            'username' => 'CodeceptionUser',
            'full_name'  => 'Codeception User',
            'email' => 'codeception_user@example.com',
            'password' => 'abc123',
            'password_confirm' => 'abc123'
        ]);
        $I->dontSeeElement('.headingError');
        $I->dontSeeElement('.error');
        $I->seeInCurrentUrl('/users/listusers.php?msg=add');
        $I->see('Success : Addition succeeded', ['css' => '.message']);
        $I->seeLink('Codeception User');
    }

    /**
     * @param AcceptanceTester $I
     * @depends viewUserManagement
     */
    public function addDuplicateUserError(AcceptanceTester $I)
    {
        $I->wantTo('Add Duplicate New User (Error)');
        $I->amOnPage('/users/edituser.php');
        $I->seeInTitle('Add User');
        $I->see('Add User', ['css' => '.heading']);
        $I->submitForm('form', [
            'username' => 'CodeceptionUser',
            'full_name'  => 'Codeception User',
            'email' => 'codeception_user@example.com',
            'password' => 'abc123',
            'password_confirm' => 'abc123'
        ]);
        $I->seeElement('.headingError');
        $I->seeElement('.error');
        $I->see('There is already a user with this name. Please choose a variation of the user\'s name.', ['css' => '.error']);
    }

    /**
     * @param AcceptanceTester $I
     * @depends addNewUser
     */
    public function viewUser(AcceptanceTester $I)
    {
        $I->wantTo('View User (Codeception User)');
        $I->amOnPage('/users/listusers.php');
        $I->seeLink('Codeception User');
        $I->click('Codeception User');
        $I->seeInCurrentUrl('/users/viewuser.php');
        $I->seeInTitle('User Management');
        $I->see('Codeception User', ".//tr/td[contains(text(),'Full name')]/following-sibling::td");
        $I->dontSeeElement('.xdebug-error');
        $this->userId = $I->grabFromCurrentUrl('~id=(\d+)~');
    }

    /**
     * @param AcceptanceTester $I
     * @depends viewUser
     */
    public function editUser(AcceptanceTester $I)
    {
        $I->wantTo('Edit User (Codeception User)');
        $I->amOnPage('/users/edituser.php?id=' . $this->userId);
        $I->seeInTitle('Edit User (CodeceptionUser)');
        $I->seeElement('form', ['name' => 'user_editForm']);
        $I->submitForm('form', [
            'full_name'  => 'Codeception User - edit',
        ]);
        $I->seeInCurrentUrl('/users/listusers.php?msg=update');
        $I->see('Success : Modification succeeded', ['css' => '.message']);
        $I->seeLink('Codeception User - edit');
    }

    /**
     * @param AcceptanceTester $I
     * @depends editUser
     */
    public function deleteUser(AcceptanceTester $I)
    {
        $I->wantTo('Delete User (Codeception User - edit)');
        $I->amOnPage('/users/deleteusers.php?id=' . $this->userId);
        $I->seeInTitle('Delete User');
        $I->see('Delete User Accounts', ['css' => '.heading']);
        $I->click('form button[type=submit]');
        $I->seeInCurrentUrl('/users/listusers.php?msg=delete');
        $I->see('Success : Deletion succeeded', ['css' => '.message']);
        $I->dontSeeLink('Codeception User - edit');
    }
}
