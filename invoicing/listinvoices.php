<?php

use phpCollab\Invoices\Invoices;
use phpCollab\Organizations\Organizations;
use phpCollab\Projects\Projects;
use phpCollab\Teams\Teams;

$checkSession = "true";
include_once '../includes/library.php';

$setTitle .= " : List Invoices";

$invoices = new Invoices();
$teams = new Teams();
$organizations = new Organizations();
$projects = new Projects();

$typeInvoices = (!empty($request->query->get("typeInvoices"))) ? $request->query->get("typeInvoices") : "open";
$clientId = (!empty($request->query->get("client"))) ? $request->query->get("client") : 0;
$status = (!empty($request->query->get("status"))) ? $request->query->get("status") : 0;

$strings = $GLOBALS["strings"];
$invoiceStatus = $GLOBALS["invoiceStatus"];
$msgLabel = $GLOBALS["msgLabel"];
$statusPublish = $GLOBALS["statusPublish"];

if ($typeInvoices == "") {
    $typeInvoices = "open";
}

$clientDetail = null;

if ($clientsFilter == "true" && $session->get("profile") == "2") {
    $teamMember = "false";

    $memberTest = $teams->getTeamByTeamMemberAndOrgId($session->get("idSession"), $clientId);

    $comptMemberTest = count($memberTest["tea_id"]);

    if ($comptMemberTest == "0") {
        phpCollab\Util::headerFunction("../clients/listclients.php?msg=blankClient");
    } else {
        $clientDetail = $organizations->getOrganizationById($clientId);
    }
} else if ($clientsFilter == "true" && $session->get("profile") == "1") {
    $clientDetail = $organizations->getOrganizationByIdAndOwner($clientId, $session->get("idSession"));
} else {
    $clientDetail = $organizations->getOrganizationById($clientId);
}

if (empty($clientDetail)) {
    phpCollab\Util::headerFunction("../clients/listclients.php?msg=blankClient");
}

include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../clients/listclients.php?", $strings["clients"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../clients/viewclient.php?id=" . $clientDetail["org_id"], $clientDetail["org_name"], "in"));
$blockPage->itemBreadcrumbs($strings["invoices"]);

if ($typeInvoices == "open") {
    $blockPage->itemBreadcrumbs($invoiceStatus[0] . " | " . $blockPage->buildLink("../invoicing/listinvoices.php?client=$clientId&typeInvoices=sent", $invoiceStatus[1], "in") . " | " . $blockPage->buildLink("../invoicing/listinvoices.php?client=$clientId&typeInvoices=paid", $invoiceStatus[2], "in"));
} else if ($typeInvoices == "sent") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../invoicing/listinvoices.php?client=$clientId&typeInvoices=open", $invoiceStatus[0], "in") . " | " . $invoiceStatus[1] . " | " . $blockPage->buildLink("../invoicing/listinvoices.php?client=$clientId&typeInvoices=paid", $invoiceStatus[2], "in"));
} else if ($typeInvoices == "paid") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../invoicing/listinvoices.php?client=$clientId&typeInvoices=open", $invoiceStatus[0], "in") . " | " . $blockPage->buildLink("../invoicing/listinvoices.php?client=$clientId&typeInvoices=sent", $invoiceStatus[1], "in") . " | " . $invoiceStatus[2]);
}
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$blockPage->setLimitsNumber(1);

$block1 = new phpCollab\Block();

$block1->form = "saP";
$block1->openForm("../invoicing/listinvoices.php?client=$clientId&typeInvoices=$typeInvoices#" . $block1->form . "Anchor", null, $csrfHandler);

if ($typeInvoices == "open") {
    $status = "0";
} else if ($typeInvoices == "sent") {
    $status = "1";
} else if ($typeInvoices == "paid") {
    $status = "2";
}
$block1->heading($strings["invoices"] . " : " . $invoiceStatus[$status]);

$block1->openPaletteIcon();
if ($session->get("profile") == "0" || $session->get("profile") == "1" || $session->get("profile") == "5") {
    $block1->paletteIcon(1, "remove", $strings["delete"]);
}
$block1->paletteIcon(2, "info", $strings["view"]);
if ($session->get("profile") == "0" || $session->get("profile") == "1" || $session->get("profile") == "5") {
    $block1->paletteIcon(3, "edit", $strings["edit"]);
}
$block1->closePaletteIcon();

$block1->setLimit($blockPage->returnLimit(1));
$block1->setRowsLimit(20);

$block1->sorting("invoices", $sortingUser["invoices"], "inv.id ASC", $sortingFields = [0 => "inv.id", 1 => "pro.name", 2 => "inv.total_inc_tax", 3 => "inv.date_sent", 4 => "inv.published"]);

$projectsTest = $projects->getProjectsByOrganization($clientId, 'pro.id');

$projectsOk = 0;

if (!$projectsTest) {
    $listProjects = "false";
} else {
    $projectsOk = [];
    foreach ($projectsTest as $project) {
        array_push($projectsOk, $project["pro_id"]);
    }
    if ($projectsOk == "") {
        $listProjects = "false";
    }
}

$listInvoices = $invoices->getActiveInvoicesByProjectId($projectsOk, $status, $block1->sortingValue);

if ($listInvoices) {
    $block1->openResults();
    $block1->labels($labels = [0 => $strings["id"], 1 => $strings["project"], 2 => $strings["total_inc_tax"], 3 => $strings["date_invoice"], 4 => $strings["published"]], "true");

    foreach ($listInvoices as $invoice) {
        $idPublish = $invoice["inv_published"];

        $block1->openRow();
        $block1->checkboxRow($invoice["inv_id"]);
        $block1->cellRow($blockPage->buildLink("../invoicing/viewinvoice.php?id=" . $invoice["inv_id"], $invoice["inv_id"], "in"));
        $block1->cellRow($blockPage->buildLink("../projects/viewproject.php?id=" . $invoice["inv_project"], $invoice["inv_pro_name"], "in"));
        $block1->cellRow($invoice["inv_total_inc_tax"] ? $invoice["inv_total_inc_tax"] : "--");
        $block1->cellRow($invoice["inv_date_sent"] ? $invoice["inv_date_sent"] : "--");

        if ($sitePublish == "true") {
            $block1->cellRow($statusPublish[$idPublish]);
        }
        $block1->closeRow();
    }
    $block1->closeResults();

    $block1->limitsFooter("1", $blockPage->getLimitsNumber(), "", "typeProjects=$typeProjects");

} else {
    $block1->noresults();
}
$block1->closeFormResults();

$block1->openPaletteScript();
if ($session->get("profile") == "0" || $session->get("profile") == "1" || $session->get("profile") == "5") {
    $block1->paletteScript(1, "remove", "../invoicing/deleteinvoices.php?", "false,true,false", $strings["delete"]);
}
$block1->paletteScript(2, "info", "../invoicing/viewinvoice.php?", "false,true,false", $strings["view"]);
if ($session->get("profile") == "0" || $session->get("profile") == "1" || $session->get("profile") == "5") {
    $block1->paletteScript(3, "edit", "../invoicing/editinvoice.php?", "false,true,false", $strings["edit"]);
}

$block1->closePaletteScript(count($listInvoices), array_column($listInvoices, 'inv_id'));

include APP_ROOT . '/themes/' . THEME . '/footer.php';
