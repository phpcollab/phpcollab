<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('See "Too many attempts" message');
$I->amOnPage('/general/sendpassword.php');
$I->fillField(['name' => 'username'], 'deleteUser1');
$I->click('input[type="submit"]');
$I->seeInCurrentUrl('/general/sendpassword.php');
$I->amOnPage('/general/sendpassword.php');
$I->fillField(['name' => 'username'], 'deleteUser1');
$I->click('input[type="submit"]');
$I->seeInCurrentUrl('/general/sendpassword.php');
$I->see('Attention : You have tried too many times', '.error');
