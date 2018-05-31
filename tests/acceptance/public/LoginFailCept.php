<?php
$scenario->incomplete('testing Travic CI');
$I = new AcceptanceTester($scenario);
$I->wantTo('login');
$I->amOnPage('/general/login.php');
$I->fillField(['name' => 'loginForm'], 'testUser');
$I->fillField(['name' => 'passwordForm'], 'invalidpassword');
$I->click('input[type="submit"]');
$I->seeElement('.headingError');
