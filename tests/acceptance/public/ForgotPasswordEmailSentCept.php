<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('forgot password');
$I->amOnPage('/general/sendpassword.php');
$I->fillField(['name' => 'loginForm'], 'deleteUser1');
$I->click('input[type="submit"]');
$I->seeInCurrentUrl('/general/sendpassword.php');
$I->see('Success : Password sent');
