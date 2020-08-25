<?php

use phpCollab\Organizations\Organizations;
use phpCollab\Teams\Teams;
use phpCollab\Util;

$checkSession = "true";
include_once '../includes/library.php';

$setTitle .= " : List Clients";

include APP_ROOT . '/themes/' . THEME . '/header.php';

$organizations = new Organizations();
$teams = new Teams();

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../clients/listclients.php", $strings["organizations"], 'in'));
$blockPage->itemBreadcrumbs($strings["organizations"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$blockPage->setLimitsNumber(1);

$block1 = new phpCollab\Block();

$block1->form = "clientList";
$block1->openForm("../clients/listclients.php#" . $block1->form . "Anchor", null, $csrfHandler);

$block1->heading($strings["organizations"]);

$block1->openPaletteIcon();
if ($session->get("profile") == "0" || $session->get("profile") == "1") {
    $block1->paletteIcon(0, "add", $strings["add"]);
    $block1->paletteIcon(1, "remove", $strings["delete"]);
}
$block1->paletteIcon(2, "info", $strings["view"]);
if ($session->get("profile") == "0" || $session->get("profile") == "1") {
    $block1->paletteIcon(3, "edit", $strings["edit"]);
}
$block1->closePaletteIcon();

$block1->setLimit($blockPage->returnLimit(1));
$block1->setRowsLimit(20);

$block1->sorting("organizations", $sortingUser["organizations"], "org.name ASC", $sortingFields = array(0 => "org.name", 1 => "org.phone", 2 => "org.url"));

if ($clientsFilter == "true" && $session->get("profile") == "2") {
    /**
     * If the user role is "user"
     */
    $teamMember = "false";

    $myTeams = $teams->getTeamByMemberId($session->get("id"));

    if (count($myTeams) == "0") {
        $listClients = "false";
    } else {
        $clientsOk = '';
        foreach ($myTeams as $team) {
            $clientsOk .= $team['tea_org2_id'] . ',';
        }

        if ($clientsOk == "") {
            $listClients = "false";
        } else {
            $listOrganizations = $organizations->getFilteredOrganizations($clientsOk, $block1->sortingValue);
        }
    }
} elseif ($clientsFilter == "true" && $session->get("profile") == "1") {
    /**
     * If the user role is "project manager"
     */
    $listOrganizations = $organizations->getOrganizationsByOwner($session->get("id"), $block1->sortingValue);
} else {
    $listOrganizations = $organizations->getListOfOrganizations($block1->sortingValue);
}

$block1->setRecordsTotal(count($listOrganizations));

if ($listClients == "false") {
    $comptListOrganizations = 0;
}

if ($listOrganizations) {
    $block1->openResults();
    $block1->labels($labels = array(0 => $strings["name"], 1 => $strings["phone"], 2 => $strings["url"]), "false");

    foreach ($listOrganizations as $org) {
        $block1->openRow();
        $block1->checkboxRow($org["org_id"]);
        $block1->cellRow($blockPage->buildLink("../clients/viewclient.php?id=" . $org["org_id"], $org["org_name"], 'in'));
        $block1->cellRow( Util::isBlank($org["org_phone"]) );
        $block1->cellRow( $blockPage->buildLink($org["org_url"], $org["org_url"], 'out') );
        $block1->closeRow();
    }
    $block1->closeResults();

    $block1->limitsFooter("1", $blockPage->getLimitsNumber(), "", "");
} else {
    $block1->noresults();
}
$block1->closeFormResults();

$block1->openPaletteScript();
if ($session->get("profile") == "0" || $session->get("profile") == "1") {
    $block1->paletteScript(0, "add", "../clients/addclient.php?", "true,false,false", $strings["add"]);
    $block1->paletteScript(1, "remove", "../clients/deleteclients.php?", "false,true,true", $strings["delete"]);
}
$block1->paletteScript(2, "info", "../clients/viewclient.php?", "false,true,false", $strings["view"]);
if ($session->get("profile") == "0" || $session->get("profile") == "1") {
    $block1->paletteScript(3, "edit", "../clients/editclient.php?", "false,true,false", $strings["edit"]);
}
$block1->closePaletteScript(count($listOrganizations), array_column($listOrganizations, 'org_id'));

include APP_ROOT . '/themes/' . THEME . '/footer.php';
