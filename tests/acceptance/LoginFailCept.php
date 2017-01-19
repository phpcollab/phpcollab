<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('login');
$I->amOnPage('/general/login.php');
$I->fillField(['name' => 'loginForm'], 'testUser');
$I->fillField(['name' => 'passwordForm'], 'invalidpassword');
$I->click('save', 'input[type="submit"]');
$I->seeElement('.headingError');
