<?php
#Application name: PhpCollab
#Status page: 0
#Path by root: ../invoices/viewinvoice.php

$checkSession = "true";
include_once '../includes/library.php';

if ($action == "publish") {

    if ($addToSite == "true") {
        $tmpquery1 = "UPDATE " . $tableCollab["invoices"] . " SET published='0' WHERE id = '$id'";
        phpCollab\Util::connectSql("$tmpquery1");
        $msg = "addToSite";
    }

    if ($removeToSite == "true") {
        $tmpquery1 = "UPDATE " . $tableCollab["invoices"] . " SET published='1' WHERE id = '$id'";
        phpCollab\Util::connectSql("$tmpquery1");
        $msg = "removeToSite";
    }

}

$tmpquery = "WHERE inv.id = '$id'";
$detailInvoice = new phpCollab\Request();
$detailInvoice->openInvoices($tmpquery);

$tmpquery = "WHERE pro.id = '" . $detailInvoice->inv_project[0] . "'";
$projectDetail = new phpCollab\Request();
$projectDetail->openProjects($tmpquery);

if ($projectDetail->pro_owner[0] != $idSession) {
    header("Location:../general/permissiondenied.php");
}

include '../themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../clients/listclients.php?", $strings["clients"], in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../clients/viewclient.php?id=" . $projectDetail->pro_org_id[0], $projectDetail->pro_org_name[0], in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../invoicing/listinvoices.php?client=" . $projectDetail->pro_organization[0], $strings["invoices"], in));
$blockPage->itemBreadcrumbs($detailInvoice->inv_id[0]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messagebox($msgLabel);
}

$block1 = new phpCollab\Block();

$block1->form = "invoiceSheet";
$block1->openForm("../invoicing/viewinvoice.php?id=$id&#" . $block1->form . "Anchor");

$block1->headingToggle($strings["invoice"] . " : " . $detailInvoice->inv_id[0]);

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

$block1->contentRow($strings["project"], $blockPage->buildLink("../projects/viewproject.php?id=" . $projectDetail->pro_id[0], $projectDetail->pro_name[0], in));

$block1->contentRow($strings["organization"], $blockPage->buildLink("../clients/viewclient.php?id=" . $projectDetail->pro_org_id[0], $projectDetail->pro_org_name[0], in));

$block1->contentRow($strings["header_note"], nl2br($detailInvoice->inv_header_note[0]));
$block1->contentRow($strings["footer_note"], nl2br($detailInvoice->inv_footer_note[0]));
$block1->contentRow($strings["date_invoice"], $detailInvoice->inv_date_sent[0]);
$block1->contentRow($strings["due_date"], $detailInvoice->inv_due_date[0]);
$block1->contentRow($strings["status"], $invoiceStatus[$detailInvoice->inv_status[0]]);
$block1->contentRow($strings["total_ex_tax"], $detailInvoice->inv_total_ex_tax[0]);
$block1->contentRow($strings["tax_rate"], $detailInvoice->inv_tax_rate[0]);
$block1->contentRow($strings["tax_amount"], $detailInvoice->inv_tax_amount[0]);
$block1->contentRow($strings["total_inc_tax"], $detailInvoice->inv_total_inc_tax[0]);
if ($sitePublish == "true") {
    $block1->contentRow($strings["published"], $statusPublish[$detailInvoice->inv_published[0]]);
}

$block1->contentRow($strings["created"], phpCollab\Util::createDate($detailInvoice->inv_created[0], $timezoneSession));
$block1->contentRow($strings["modified"], phpCollab\Util::createDate($detailInvoice->inv_modified[0], $timezoneSession));

$block1->closeContent();
$block1->closeToggle();
$block1->closeForm();

$block1->openPaletteScript();
$block1->paletteScript(0, "remove", "../invoicing/deleteinvoices.php?id=" . $detailInvoice->inv_id[0] . "&client=" . $projectDetail->pro_org_id[0] . "", "true,true,false", $strings["delete"]);
$block1->paletteScript(2, "export", "exportinvoice.php?id=$id", "true,true,false", $strings["export"]);
if ($sitePublish == "true") {
    $block1->paletteScript(3, "add_projectsite", "../invoicing/viewinvoice.php?addToSite=true&id=" . $detailInvoice->inv_id[0] . "&action=publish", "true,true,true", $strings["add_project_site"]);
    $block1->paletteScript(4, "remove_projectsite", "../invoicing/viewinvoice.php?removeToSite=true&id=" . $detailInvoice->inv_id[0] . "&action=publish", "true,true,true", $strings["remove_project_site"]);
}
$block1->paletteScript(5, "edit", "../invoicing/editinvoice.php?id=" . $detailInvoice->inv_id[0], "true,true,false", $strings["edit"]);
$block1->closePaletteScript("", "");

$block2 = new phpCollab\Block();

$block2->form = "invoiceItems";
$block2->openForm("../invoicing/viewinvoice.php?id=$id&#" . $block2->form . "Anchor");

$block2->headingToggle($strings["invoice_items"]);

$block2->openPaletteIcon();
//$block2->paletteIcon(6,"info",$strings["view"]);
$block2->paletteIcon(7, "edit", $strings["edit"]);
$block2->closePaletteIcon();

$tmpquery = "WHERE invitem.invoice = '$id' AND invitem.active = '1' ORDER BY invitem.position ASC";
$listInvoicesItems = new phpCollab\Request();
$listInvoicesItems->openInvoicesItems($tmpquery);
$comptListInvoicesItems = count($listInvoicesItems->invitem_id);

if ($comptListInvoicesItems != "0") {
    $block2->openResults();

    $block2->labels($labels = array(0 => $strings["position"], 1 => $strings["title"], 2 => $strings["rate_type"], 3 => $strings["rate_value"], 4 => $strings["amount_ex_tax"], 5 => $strings["completed"]), "false", $sorting = "false", $sortingOff = array(0 => "0", 1 => "ASC"));

    for ($i = 0; $i < $comptListInvoicesItems; $i++) {
        if ($listInvoicesItems->invitem_rate_type[$i] == "a") {
            $rate_type_value = "0";
        } else if ($listInvoicesItems->invitem_rate_type[$i] == "b") {
            $rate_type_value = "1";
        } else if ($listInvoicesItems->invitem_rate_type[$i] == "c") {
            $rate_type_value = "2";
        } else if (is_numeric($listInvoicesItems->invitem_rate_type[$i])) {
            $rate_type_value = "3";
        }

        $block2->openRow();
        $block2->checkboxRow($listInvoicesItems->invitem_id[$i]);
        $block2->cellRow($listInvoicesItems->invitem_position[$i]);
        $block2->cellRow($blockPage->buildLink("../invoicing/editinvoiceitem.php?invoiceitem=" . $listInvoicesItems->invitem_id[$i], $listInvoicesItems->invitem_title[$i], in));
        $block2->cellRow($rateType[$rate_type_value]);
        $block2->cellRow($listInvoicesItems->invitem_rate_value[$i]);
        $block2->cellRow($listInvoicesItems->invitem_amount_ex_tax[$i]);
        if ($listInvoicesItems->invitem_completed[$i] == "1") {
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
//$block2->paletteScript(6,"info","../invoices/viewsubtask.php?task=$id","false,true,false",$strings["view"]);
$block2->paletteScript(7, "edit", "../invoicing/editinvoiceitem.php?id=$id", "false,true,true", $strings["edit"]);
$block2->closePaletteScript($comptListInvoicesItems, $listInvoicesItems->invitem_id);

include '../themes/' . THEME . '/footer.php';
?>
