<?php
$checkSession = "true";
include_once '../includes/library.php';

$invoices = new \phpCollab\Invoices\Invoices();
$teams = new \phpCollab\Teams\Teams();
$organizations = new \phpCollab\Organizations\Organizations();
$projects = new \phpCollab\Projects\Projects();

$typeInvoices = (isset($_GET["typeInvoices"]) && !empty($_GET["typeInvoices"])) ? $_GET["typeInvoices"] : "open";
$client = (isset($_GET["client"]) && !empty($_GET["client"])) ? $_GET["client"] : 0;
$status = (isset($_GET["status"]) && !empty($_GET["status"])) ? $_GET["status"] : 0;
$idSession = (isset($_SESSION["idSession"]) && !empty($_SESSION["idSession"])) ? $_SESSION["idSession"] : 0;

$strings = $GLOBALS["strings"];
$invoiceStatus = $GLOBALS["invoiceStatus"];
$msgLabel = $GLOBALS["msgLabel"];
$statusPublish = $GLOBALS["statusPublish"];



if ($typeInvoices == "") {
    $typeInvoices = "open";
}

$clientDetail = null;

if ($clientsFilter == "true" && $profilSession == "2") {
    $teamMember = "false";

    $memberTest = $teams->getTeamByTeamMemberAndOrgId($idSession, $client);

    $comptMemberTest = count($memberTest["tea_id"]);

    if ($comptMemberTest == "0") {
        phpCollab\Util::headerFunction("../clients/listclients.php?msg=blankClient");
    } else {
        $clientDetail = $organizations->getOrganizationById($client);
    }
} else if ($clientsFilter == "true" && $profilSession == "1") {
    $clientDetail = $organizations->getOrganizationByIdAndOwner($client, $idSession);
} else {
    $clientDetail = $organizations->getOrganizationById($client);
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
    $blockPage->itemBreadcrumbs($invoiceStatus[0] . " | " . $blockPage->buildLink("../invoicing/listinvoices.php?client=$client&typeInvoices=sent", $invoiceStatus[1], "in") . " | " . $blockPage->buildLink("../invoicing/listinvoices.php?client=$client&typeInvoices=paid", $invoiceStatus[2], "in"));
} else if ($typeInvoices == "sent") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../invoicing/listinvoices.php?client=$client&typeInvoices=open", $invoiceStatus[0], "in") . " | " . $invoiceStatus[1] . " | " . $blockPage->buildLink("../invoicing/listinvoices.php?client=$client&typeInvoices=paid", $invoiceStatus[2], "in"));
} else if ($typeInvoices == "paid") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../invoicing/listinvoices.php?client=$client&typeInvoices=open", $invoiceStatus[0], "in") . " | " . $blockPage->buildLink("../invoicing/listinvoices.php?client=$client&typeInvoices=sent", $invoiceStatus[1], "in") . " | " . $invoiceStatus[2]);
}
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$blockPage->setLimitsNumber(1);

$block1 = new phpCollab\Block();

$block1->form = "saP";
$block1->openForm("../invoicing/listinvoices.php?client=$client&typeInvoices=$typeInvoices#" . $block1->form . "Anchor");

if ($typeInvoices == "open") {
    $status = "0";
} else if ($typeInvoices == "sent") {
    $status = "1";
} else if ($typeInvoices == "paid") {
    $status = "2";
}
$block1->heading($strings["invoices"] . " : " . $invoiceStatus[$status]);

$block1->openPaletteIcon();
if ($profilSession == "0" || $profilSession == "1" || $profilSession == "5") {
    $block1->paletteIcon(1, "remove", $strings["delete"]);
}
$block1->paletteIcon(2, "info", $strings["view"]);
if ($profilSession == "0" || $profilSession == "1" || $profilSession == "5") {
    $block1->paletteIcon(3, "edit", $strings["edit"]);
}
$block1->closePaletteIcon();

$block1->setLimit($blockPage->returnLimit(1));
$block1->setRowsLimit(20);

$block1->sorting("invoices", $sortingUser["invoices"], "inv.id ASC", $sortingFields = [0 => "inv.id", 1 => "pro.name", 2 => "inv.total_inc_tax", 3 => "inv.date_sent", 4 => "inv.published"]);

$projectsTest = $projects->getProjectsByOrganization($client, 'pro.id');

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
    } else {

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
if ($profilSession == "0" || $profilSession == "1" || $profilSession == "5") {
    $block1->paletteScript(1, "remove", "../invoicing/deleteinvoices.php?", "false,true,false", $strings["delete"]);
}
$block1->paletteScript(2, "info", "../invoicing/viewinvoice.php?", "false,true,false", $strings["view"]);
if ($profilSession == "0" || $profilSession == "1" || $profilSession == "5") {
    $block1->paletteScript(3, "edit", "../invoicing/editinvoice.php?", "false,true,false", $strings["edit"]);
}

$block1->closePaletteScript(count($listInvoices), array_column($listInvoices, 'inv_id'));

include APP_ROOT . '/themes/' . THEME . '/footer.php';
