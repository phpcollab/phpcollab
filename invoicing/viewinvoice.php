<?php

$checkSession = "true";
include_once '../includes/library.php';
$setTitle .= " : View Invoices";

$invoices = $container->getInvoicesLoader();
$projects = $container->getProjectsLoader();
$id = $request->query->get("id", 0);
$action = $request->query->get("action");
$addToSite = $request->query->get("addToSite", false);
$removeToSite = $request->query->get("removeToSite", false);
$strings = $GLOBALS["strings"];

if ($action == "publish") {

    try {
        if ($addToSite == "true") {
            $invoices->togglePublish($id, true);
            $msg = "addToSite";
        }

        if ($removeToSite == "true") {
            $invoices->togglePublish($id, false);
            $msg = "removeToSite";
        }
    } catch (Exception $exception) {
        $error = $strings["error_publishing_invoice"];
        error_log($strings["error_publishing_invoice"] . ': ' . $exception->getMessage());
    }
}

$detailInvoice = $invoices->getInvoiceById($id);
$projectDetail = $projects->getProjectById($detailInvoice["inv_project"]);

if ($projectDetail["pro_owner"] != $session->get("id")) {
    header("Location:../general/permissiondenied.php");
}

$setTitle .= " : " . $strings["view_invoice"];

include APP_ROOT . '/views/layout/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../clients/listclients.php?", $strings["clients"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../clients/viewclient.php?id=" . $projectDetail["pro_org_id"],
    $projectDetail["pro_org_name"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../invoicing/listinvoices.php?client=" . $projectDetail["pro_organization"],
    $strings["invoices"], "in"));
$blockPage->itemBreadcrumbs($detailInvoice["inv_id"]);
$blockPage->closeBreadcrumbs();


if (!empty($error)) {
    $blockPage->headingError($strings["errors"]);
    $blockPage->contentError($error);
}

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($GLOBALS["msgLabel"]);
}

$block1 = new phpCollab\Block();

$block1->form = "invoiceSheet";
$block1->openForm("../invoicing/viewinvoice.php?id=$id&#" . $block1->form . "Anchor", null, $csrfHandler);

$block1->headingToggle($strings["invoice"] . " : " . $detailInvoice["inv_id"]);

$block1->openPaletteIcon();
$block1->paletteIcon(0, "remove", $strings["delete"]);
$block1->paletteIcon(2, "export", $strings["export"]);
if ($sitePublish == "true") {
    $block1->paletteIcon(3, "add_projectsite", $strings["add_project_site"]);
    $block1->paletteIcon(4, "remove_projectsite", $strings["remove_project_site"]);
}
$block1->paletteIcon(5, "edit", $strings["edit"]);
$block1->closePaletteIcon();

$block1->openContent();
$block1->contentTitle($strings["info"]);

$block1->contentRow($strings["project"],
    $blockPage->buildLink("../projects/viewproject.php?id=" . $projectDetail["pro_id"], $projectDetail["pro_name"],
        "in"));

$block1->contentRow($strings["organization"],
    $blockPage->buildLink("../clients/viewclient.php?id=" . $projectDetail["pro_org_id"],
        $projectDetail["pro_org_name"], "in"));

$block1->contentRow($strings["header_note"], nl2br($detailInvoice["inv_header_note"]));
$block1->contentRow($strings["footer_note"], nl2br($detailInvoice["inv_footer_note"]));
$block1->contentRow($strings["date_invoice"], $detailInvoice["inv_date_sent"]);
$block1->contentRow($strings["due_date"], $detailInvoice["inv_due_date"]);
$block1->contentRow($strings["status"], $GLOBALS["invoiceStatus"][$detailInvoice["inv_status"]]);
$block1->contentRow($strings["total_ex_tax"], $detailInvoice["inv_total_ex_tax"]);
$block1->contentRow($strings["tax_rate"], $detailInvoice["inv_tax_rate"]);
$block1->contentRow($strings["tax_amount"], $detailInvoice["inv_tax_amount"]);
$block1->contentRow($strings["total_inc_tax"], $detailInvoice["inv_total_inc"]);
if ($sitePublish == "true") {
    $block1->contentRow($strings["published"], $GLOBALS["statusPublish"][$detailInvoice["inv_published"]]);
}

$block1->contentRow($strings["created"],
    phpCollab\Util::createDate($detailInvoice["inv_created"], $session->get('timezone')));
$block1->contentRow($strings["modified"],
    phpCollab\Util::createDate($detailInvoice["inv_modified"], $session->get('timezone')));

$block1->closeContent();
$block1->closeToggle();
$block1->closeForm();

$block1->openPaletteScript();
$block1->paletteScript(0, "remove",
    "../invoicing/deleteinvoices.php?id=" . $detailInvoice["inv_id"] . "&client=" . $projectDetail["pro_org_id"] . "",
    "true,true,false", $strings["delete"]);
$block1->paletteScript(2, "export", "exportinvoice.php?id=$id", "true,true,false", $strings["export"]);
if ($sitePublish == "true") {
    $block1->paletteScript(3, "add_projectsite",
        "../invoicing/viewinvoice.php?addToSite=true&id=" . $detailInvoice["inv_id"] . "&action=publish",
        "true,true,true", $strings["add_project_site"]);
    $block1->paletteScript(4, "remove_projectsite",
        "../invoicing/viewinvoice.php?removeToSite=true&id=" . $detailInvoice["inv_id"] . "&action=publish",
        "true,true,true", $strings["remove_project_site"]);
}
$block1->paletteScript(5, "edit", "../invoicing/editinvoice.php?id=" . $detailInvoice["inv_id"], "true,true,false",
    $strings["edit"]);
$block1->closePaletteScript("", []);

$block2 = new phpCollab\Block();

$block2->form = "invoiceItems";
$block2->openForm("../invoicing/viewinvoice.php?id=$id&#" . $block2->form . "Anchor", null, $csrfHandler);

$block2->headingToggle($strings["invoice_items"]);

$block2->openPaletteIcon();
$block2->paletteIcon(7, "edit", $strings["edit"]);
$block2->closePaletteIcon();

$listInvoicesItems = $invoices->getActiveInvoiceItemsByInvoiceId($id);

if ($listInvoicesItems) {
    $block2->openResults();

    $block2->labels($labels = [
        0 => $strings["position"],
        1 => $strings["title"],
        2 => $strings["rate_type"],
        3 => $strings["rate_value"],
        4 => $strings["amount_ex_tax"],
        5 => $strings["completed"]
    ], "false", $sorting = "false", $sortingOff = [0 => "0", 1 => "ASC"]);

    $rate_type_value = null;
    foreach ($listInvoicesItems as $item) {
        if ($item["invitem_rate_type"] == "a") {
            $rate_type_value = "0";
        } else {
            if ($item["invitem_rate_type"] == "b") {
                $rate_type_value = "1";
            } else {
                if ($item["invitem_rate_type"] == "c") {
                    $rate_type_value = "2";
                } else {
                    if (is_numeric($item["invitem_rate_type"])) {
                        $rate_type_value = "3";
                    }
                }
            }
        }

        $block2->openRow();
        $block2->checkboxRow($item["invitem_id"]);
        $block2->cellRow($item["invitem_position"]);
        $block2->cellRow($blockPage->buildLink("../invoicing/editinvoiceitem.php?id=" . $item["invitem_id"],
            $item["invitem_title"], "in"));
        $block2->cellRow($GLOBALS["rateType"][$rate_type_value]);
        $block2->cellRow($item["invitem_rate_value"]);
        $block2->cellRow($item["invitem_amount_ex_tax"]);
        if ($item["invitem_completed"] == "1") {
            $block2->cellRow($strings["yes"]);
        } else {
            $block2->cellRow($strings["no"]);
        }
        $block2->closeRow();
    }
    $block2->closeResults();

} else {
    $block2->noresults();
}
$block2->closeToggle();
$block2->closeFormResults();

$block2->openPaletteScript();
$block2->paletteScript(7, "edit", "../invoicing/editinvoiceitem.php", "false,true,false", $strings["edit"]);
$block2->closePaletteScript(count($listInvoicesItems), array_column($listInvoicesItems, 'invitem_id'));

include APP_ROOT . '/views/layout/footer.php';
