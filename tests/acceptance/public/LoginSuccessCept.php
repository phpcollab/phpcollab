<?php
$I = new AcceptanceTester($scenario);

$I->wantTo('login');
$I->amOnPage('/general/login.php');
$I->fillField(['name' => 'usernameForm'], 'testUser');
$I->fillField(['name' => 'passwordForm'], 'testing');
$I->click('input[type="submit"]');
$I->seeInCurrentUrl('/general/home.php');
$I->seeInTitle('Home Page');
