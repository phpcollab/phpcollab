<?php
class PublicCest
{
    /**
     * @param AcceptanceTester $I
     */
    public function viewLoginPage(AcceptanceTester $I)
    {
        $I->wantTo('View the login page');
        $I->amOnPage('/general/login.php');
        $I->see('Log in', ['css' => '.heading']);
        $I->seeElement('form', ['name' => 'loginForm']);
        $I->dontSeeElement('.error');
    }
}
