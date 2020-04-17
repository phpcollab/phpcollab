<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('forgot password');
$I->amOnPage('/general/sendpassword.php');
$I->fillField(['name' => 'username'], 'deleteUser1');
$I->click('input[type="submit"]');
$I->seeInCurrentUrl('/general/sendpassword.php');
$I->see('If the user you specified exists in our system, we will send a password to their email address.', '.message');
