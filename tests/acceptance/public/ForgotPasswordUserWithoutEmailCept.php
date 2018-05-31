<?php
$scenario->incomplete('testing Travic CI');
$I = new AcceptanceTester($scenario);
$I->wantTo('forgot password');
$I->amOnPage('/general/sendpassword.php');
$I->fillField(['name' => 'loginForm'], 'deleteuser5');
$I->click('input[type="submit"]');
$I->seeInCurrentUrl('/general/sendpassword.php');
$I->seeElement('.headingError');
$I->see('User without email');