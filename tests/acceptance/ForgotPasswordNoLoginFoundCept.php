<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('forgot password');
$I->amOnPage('/general/sendpassword.php');
$I->fillField(['name' => 'loginForm'], 'xyz123');
$I->click('send', 'input[type="submit"]');
$I->seeInCurrentUrl('/general/sendpassword.php');
$I->see('Login not found in database');