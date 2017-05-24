<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('login');
$I->amOnPage('/general/login.php');
$I->fillField(['name' => 'loginForm'], 'testUser');
$I->fillField(['name' => 'passwordForm'], 'testing');
$I->click('input[type="submit"]');
$I->seeInCurrentUrl('/general/home.php');
$I->click('#account > a:nth-child(1)');
$I->see('You have successfully logged out. You can log back in by typing your user name and password below.', 'table.message td');
