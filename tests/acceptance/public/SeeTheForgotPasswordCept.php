<?php
$scenario->incomplete('testing Travic CI');
$I = new AcceptanceTester($scenario);
$I->wantTo('see the forgot password page');
$I->amOnPage('/general/login.php');
$I->click(['link' => 'Forgot password?']);
$I->see('Enter your login to receive new password');
