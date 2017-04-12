<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('ensure that license page appears');
$I->amOnPage('/general/systemrequirements.php');
$I->see('System Requirements');