<?php
$scenario->incomplete('testing Travic CI');
$I = new AcceptanceTester($scenario);
$I->wantTo('ensure that system requirements page appears');
$I->amOnPage('/general/systemrequirements.php');
$I->see('System Requirements');