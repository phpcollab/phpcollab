<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('login');
$I->amOnPage('/general/login.php');
$I->fillField(['name' => 'loginForm'], 'testUser');
$I->fillField(['name' => 'passwordForm'], 'testing');
$I->click('input[type="submit"]');
$I->seeInCurrentUrl('/general/home.php');
$I->see('Home Page', '#header');
