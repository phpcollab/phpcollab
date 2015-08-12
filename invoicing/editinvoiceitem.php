<?php
#Application name: PhpCollab
#Status page: 0
#Path by root: ../tasks/edittask.php

$checkSession = "true";
include_once('../includes/library.php');

$tmpquery = "WHERE invitem.id = '$id'";
$detailInvoiceItem = new Request();
$detailInvoiceItem->openInvoicesItems($tmpquery);

$tmpquery = "WHERE inv.id = '".$detailInvoiceItem->invitem_invoice[0]."'";
$detailInvoice = new Request();
$detailInvoice->openInvoices($tmpquery);

$tmpquery = "WHERE pro.id = '".$detailInvoice->inv_project[0]."'";
$projectDetail = new Request();
$projectDetail->openProjects($tmpquery);

if ($action == "update") {
	$tmpquery = "UPDATE ".$tableCollab["invoices_items"]." SET rate_type='$rate_type',rate_value='$rate_value',amount_ex_tax='$amount_ex_tax' WHERE id = '$invoiceitem'";
	Util::connectSql($tmpquery);
	Util::headerFunction("../invoicing/viewinvoice.php?msg=update&id=$id&".session_name()."=".session_id());
}

//set value in form
$worked_hours = $detailInvoiceItem->invitem_worked_hours[0];
$amount_ex_tax = $detailInvoiceItem->invitem_amount_ex_tax[0];
$rate_type = $detailInvoiceItem->invitem_rate_type[0];
$rate_value = $detailInvoiceItem->invitem_rate_value[0];

include('../themes/'.THEME.'/header.php');

$blockPage = new Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../clients/listclients.php?",$strings["clients"],in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../clients/viewclient.php?id=".$projectDetail->pro_org_id[0],$projectDetail->pro_org_name[0],in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../invoicing/listinvoices.php?client=".$projectDetail->pro_organization[0],$strings["invoices"],in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../invoicing/viewinvoice.php?id=".$detailInvoice->inv_id[0],$detailInvoice->inv_id[0],in));
//$blockPage->itemBreadcrumbs($blockPage->buildLink("../invoicing/viewinvoiceitem.php?invoiceitem=".$detailInvoiceItem->invitem_id[0],$detailInvoiceItem->invitem_title[0],in));
$blockPage->itemBreadcrumbs($detailInvoiceItem->invitem_title[0]);
$blockPage->itemBreadcrumbs($strings["edit_invoiceitem"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
	include('../includes/messages.php');
	$blockPage->messagebox($msgLabel);
}

$block1 = new Block();

if ($invoiceitem != "") {
	$block1->form = "invoice";
	$block1->openForm("../invoicing/editinvoiceitem.php?invoiceitem=$invoiceitem&id=".$detailInvoice->inv_id[0]."&action=update&".session_name()."=".session_id()."#".$block1->form."Anchor");
}

if ($error != "") {            
	$block1->headingError($strings["errors"]);
	$block1->contentError($error);
}

if ($invoiceitem != "") {
	$block1->heading($strings["edit_invoiceitem"]." : ".$detailInvoiceItem->invitem_title[0]);
}

$block1->openContent();
$block1->contentTitle($strings["calculation"]);
?>

<script type="text/JavaScript">
function rateField(ref) {
	document.invoiceForm["rate_value"].value = ref;
	document.invoiceForm["amount_ex_tax"].value = document.invoiceForm["rate_value"].value * document.invoiceForm["worked_hours"].value;
}
</script>

<?php
if ($detailInvoiceItem->invitem_rate_type[0] == "a") {
	$checkeda = "checked";
} else if ($detailInvoiceItem->invitem_rate_type[0] == "b") {
	$checkedb = "checked";
} else if ($detailInvoiceItem->invitem_rate_type[0] == "c") {
	$checkedc = "checked";
} else if (is_numeric($detailInvoiceItem->invitem_rate_type[0])) {
	$checked{$detailInvoiceItem->invitem_rate_type[0]} = "checked";
}

$tmpquery = "WHERE org.id = '".$projectDetail->pro_organization[0]."'";
$detailClient = new Request();
$detailClient->openOrganizations($tmpquery);

$tmpquery = "ORDER BY serv.name ASC";
$listServices = new Request();
$listServices->openServices($tmpquery);
$comptListServices = count($listServices->serv_id);

if ($comptListServices!= "0") {
for ($i=0;$i<$comptListServices;$i++) {
	$j = $i + 1;
	$selectService .= "<input type=\"radio\" name=\"rate_type\" value=\"".$listServices->serv_id[$i]."\" onclick=\"rateField('".$listServices->serv_hourly_rate[$i]."');\" ".$checked{$j}." id=\"service".$listServices->serv_id[$i]."\"> <label for=\"service".$listServices->serv_id[$i]."\">".$rateType["3"]." [".$listServices->serv_name[$i]."]</label><br/>";
}
}

$block1->contentRow($strings["worked_hours"],"<input type=\"hidden\" name=\"worked_hours\"value=\"$worked_hours\">$worked_hours");
$block1->contentRow($strings["rate_type"],"<input type=\"radio\" name=\"rate_type\" value=\"a\" $checkeda id=\"custom\"> <label for=\"custom\">".$rateType["0"]."</label><br/><input type=\"radio\" name=\"rate_type\" value=\"b\" onclick=\"rateField('".$projectDetail->pro_hourly_rate[0]."');\" $checkedb id=\"project\"> <label for=\"project\">".$rateType["1"]."</label><br/><input type=\"radio\" name=\"rate_type\" value=\"c\" onclick=\"rateField('".$detailClient->org_hourly_rate[0]."');\" $checkedc id=\"organization\"> <label for=\"organization\">".$rateType["2"]."</label><br/>$selectService");
$block1->contentRow($strings["rate_value"],"<input type=\"text\" name=\"rate_value\" size=\"20\" value=\"$rate_value\" onchange=\"document.invoiceForm.rate_type[0].checked=true\">");
$block1->contentRow($strings["amount_ex_tax"],"<input type=\"text\" name=\"amount_ex_tax\" size=\"20\" value=\"$amount_ex_tax\" readonly>");

echo "<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">&nbsp;</td><td><input type=\"SUBMIT\" value=\"".$strings["save"]."\"></td></tr>";

$block1->closeContent();
$block1->closeForm();
?>

<?php
include('../themes/'.THEME.'/footer.php');
?>
