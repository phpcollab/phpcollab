<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('ensure that license page appears');
$I->amOnPage('/general/license.php');
$I->see('License');
