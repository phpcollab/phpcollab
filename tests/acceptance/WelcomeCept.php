<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('ensure that login page appears');
$I->amOnPage('/');
$I->see('Log In');
