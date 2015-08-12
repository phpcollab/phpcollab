<?php
/*
** Application name: phpCollab
** Last Edit page: 04/12/2004
** Path by root: ../invoicing/editinvoice.php
** Authors: Ceam / Fullo 
** =============================================================================
**
**               phpCollab - Project Managment 
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: editinvoice.php
**
** DESC: screen: edit a invoice
**
** HISTORY:
** 	04/12/2004	-	added new document info
**	04/12/2004  -	fixed [ 1077236 ] Calendar bug in Client's Project site
**  25/04/2006  -   replaced JavaScript Calendar functions
** -----------------------------------------------------------------------------
** TO-DO:
** =============================================================================
*/

$checkSession = "true";
include_once('../includes/library.php');

$tmpquery = "WHERE inv.id = '$id'";
$detailInvoice = new request();
$detailInvoice->openInvoices($tmpquery);

$tmpquery = "WHERE pro.id = '".$detailInvoice->inv_project[0]."'";
$projectDetail = new request();
$projectDetail->openProjects($tmpquery);

if ($projectDetail->pro_owner[0] != $idSession) { 
	header("Location:../general/permissiondenied.php?".session_name()."=".session_id()); 
	exit; 
} 

if ($id != "") {

if ($action == "update") {
if ($pub == "") {
	$pub = "1";
}
if ($st == "1") {
	$datesent = $date;
}

	$tmpquery = "UPDATE ".$tableCollab["invoices"]." SET header_note='$header_note',footer_note='$footer_note',published='$pub',status='$st',due_date='$dd',date_sent='$datesent',total_ex_tax='$total_ex_tax',total_inc_tax='$total_inc_tax',tax_rate='$tax_rate',tax_amount='$tax_amount',modified='$dateheure' WHERE id = '$id'";
	Util::connectSql($tmpquery);

for ($i=0;$i<$comptListInvoicesItems;$i++) {
	$tmpquery = "UPDATE ".$tableCollab["invoices_items"]." SET title='".$title[$i]."',position='".$position[$i]."',amount_ex_tax='".${"item".$i}."' WHERE id = '".$itemId[$i]."'";
	Util::connectSql($tmpquery);
}

	Util::headerFunction("../invoicing/viewinvoice.php?msg=update&id=$id&".session_name()."=".session_id());
}

//set value in form
$header_note = $detailInvoice->inv_header_note[0];
$footer_note = $detailInvoice->inv_footer_note[0];
$datesent = $detailInvoice->inv_date_sent[0];
$dd = $detailInvoice->inv_due_date[0];
$total_ex_tax = $detailInvoice->inv_total_ex_tax[0];
$tax_rate = $detailInvoice->inv_tax_rate[0];
$tax_amount = $detailInvoice->inv_tax_amount[0];
$total_inc_tax = $detailInvoice->inv_total_inc_tax[0];
$st = $detailInvoice->inv_status[0];

$pub = $detailInvoice->inv_published[0];
	if ($pub == "0") {
		$checkedPub = "checked";
	}
}
$includeCalendar = true; //Include Javascript files for the pop-up calendar
include('../themes/'.THEME.'/header.php');

$blockPage = new Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../clients/listclients.php?",$strings["clients"],in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../clients/viewclient.php?id=".$projectDetail->pro_org_id[0],$projectDetail->pro_org_name[0],in));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../invoicing/listinvoices.php?client=".$projectDetail->pro_organization[0],$strings["invoices"],in));

if ($id != "") {
	$blockPage->itemBreadcrumbs($blockPage->buildLink("../invoicing/viewinvoice.php?id=".$detailInvoice->inv_id[0],$detailInvoice->inv_id[0],in));
	$blockPage->itemBreadcrumbs($strings["edit_invoice"]);
}
$blockPage->closeBreadcrumbs();

if ($msg != "") {
	include('../includes/messages.php');
	$blockPage->messagebox($msgLabel);
}

$block1 = new Block();

if ($id != "") {
	$block1->form = "invoice";
	$block1->openForm("../invoicing/editinvoice.php?id=$id&action=update&".session_name()."=".session_id()."#".$block1->form."Anchor");
}

if ($error != "") {            
	$block1->headingError($strings["errors"]);
	$block1->contentError($error);
}

if ($id != "") {
	$block1->heading($strings["edit_invoice"]." : ".$detailInvoice->inv_id[0]);
}

$block1->openContent();
$block1->contentTitle($strings["details"]);

$block1->contentRow($strings["header_note"],"<textarea rows=\"10\" style=\"width: 400px; height: 100px;\" name=\"header_note\" cols=\"47\">$header_note</textarea>");
$block1->contentRow($strings["footer_note"],"<textarea rows=\"10\" style=\"width: 400px; height: 100px;\" name=\"footer_note\" cols=\"47\">$footer_note</textarea>");

$tmpquery = "WHERE invitem.invoice = '$id' AND invitem.active = '1' ORDER BY invitem.position ASC";
$listInvoicesItems = new request();
$listInvoicesItems->openInvoicesItems($tmpquery);
$comptListInvoicesItems = count($listInvoicesItems->invitem_id);

$selectStatus = "<select name=\"st\">";

$comptSta = count($invoiceStatus);
if ($detailInvoice->inv_status[0] == "0") {
	$begin = "0";
} else {
	$begin = "1";
}

for ($i=0;$i<$comptListInvoicesItems;$i++) {
	if ($listInvoicesItems->invitem_completed[$i] == "0") {
		$comptSta = "1";
		$notCompleted = $strings["note_invoice_items_notcompleted"];
		break;
	}
}

for ($i=$begin;$i<$comptSta;$i++) {
	if ($detailInvoice->inv_status[0] == $i) {
		$selectStatus .= "<option value=\"$i\" selected>$invoiceStatus[$i]</option>";
	} else {
		$selectStatus .= "<option value=\"$i\">$invoiceStatus[$i]</option>";
	}
}

$selectStatus .= "</select> $notCompleted";

$block1->contentRow($strings["status"],$selectStatus);

if ($dd == "") {
	$dd = "--";
}

$block1->contentRow($strings["due_date"],"<input type='text' name='dd' id='due_date' size='20' value='$dd'><input type='button' value=' ... ' id='trigDueDate''>");
echo "
<script type='text/javascript'>
    Calendar.setup({
        inputField     :    'due_date',
        button         :    'trigDueDate',
        $calendar_common_settings
    });
</script>
";
$block1->contentRow($strings["published"],"<input size=\"32\" value=\"0\" name=\"pub\" type=\"checkbox\" $checkedPub>");

$block1->contentTitle($strings["calculation"]);

echo "<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">".$strings["items"]." :</td><td>";
echo "<table cellpadding=\"0\" cellspacing=\"0\">";

echo "<input type=\"hidden\" name=\"comptListInvoicesItems\" value=\"$comptListInvoicesItems\">";
?>

<script type="text/JavaScript">
function calc(w) {
var item = [];
var subtotal = 0;

for (var i = 0; i < <?php echo $comptListInvoicesItems; ?>; i++) {
      item[i] = 1 * document.invoiceForm["item" + i].value;
	if (item[i] == "") {
		item[i] = 0;
	} else {
		subtotal += item[i];
	}
}
document.invoiceForm["total_ex_tax"].value = subtotal;

var ratePercent = document.invoiceForm["tax_rate"].value;

if (subtotal != 0) {
var amount_due = subtotal + (subtotal*ratePercent)/100;

document.invoiceForm["total_inc_tax"].value = amount_due;
}

if (subtotal != 0 && ratePercent != '') {
var tax_part = (subtotal*ratePercent)/100;
document.invoiceForm["tax_amount"].value = tax_part;
}
}
</script>

<?php
if ($comptListInvoicesItems != "0") {
echo "<tr><td>".$strings["position"]."</td><td>".$strings["title"]."</td><td>".$strings["amount_ex_tax"]."</td><td>".$strings["completed"]."</td></tr>";
for ($i=0;$i<$comptListInvoicesItems;$i++) {
if ($listInvoicesItems->invitem_completed[$i] == "1") {
	$completeValue = $strings["yes"];
} else {
	$completeValue = $strings["no"];
}
echo "<tr><td><input type=\"hidden\" name=\"itemId[$i]\" size=\"20\" value=\"".$listInvoicesItems->invitem_id[$i]."\"><input type=\"text\" name=\"position[$i]\" size=\"3\" value=\"".$listInvoicesItems->invitem_position[$i]."\"></td><td><input type=\"text\" name=\"title[$i]\" size=\"20\" value=\"".$listInvoicesItems->invitem_title[$i]."\"></td><td><input type=\"text\" name=\"item$i\" size=\"20\" value=\"".$listInvoicesItems->invitem_amount_ex_tax[$i]."\" tabindex=\"$i\" onblur=\"calc(this)\"></td><td>$completeValue</td></tr>";
}
}

echo "</table>";
echo "</td></tr>";

$block1->contentRow($strings["total_ex_tax"],"<input type=\"text\" name=\"total_ex_tax\" size=\"20\" value=\"$total_ex_tax\">");
$block1->contentRow($strings["tax_rate"],"<input type=\"text\" name=\"tax_rate\" size=\"20\" value=\"$tax_rate\" onblur=\"calc(this)\"> %");
$block1->contentRow($strings["tax_amount"],"<input type=\"text\" name=\"tax_amount\" size=\"20\" value=\"$tax_amount\">");
$block1->contentRow($strings["total_inc_tax"],"<input type=\"text\" name=\"total_inc_tax\" size=\"20\" value=\"$total_inc_tax\">");

echo "<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">&nbsp;</td><td><input type=\"SUBMIT\" value=\"".$strings["save"]."\"></td></tr>";

$block1->closeContent();
$block1->closeForm();

include('../themes/'.THEME.'/footer.php');
?>
