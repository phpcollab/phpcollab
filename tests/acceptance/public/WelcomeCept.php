<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('ensure that login page appears');
$I->amOnPage('/general/login.php');
$I->see('Log In');
