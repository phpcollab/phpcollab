<?php

$checkSession = "true";
include_once '../includes/library.php';

$id = isset($_GET["id"]) ? $_GET["id"] : null;

$msgLabel = $GLOBALS["msgLabel"];
$strings = $GLOBALS["strings"];
$tableCollab = $GLOBALS["tableCollab"];
$idSession = $_SESSION["idSession"];
$invoiceStatus = $GLOBALS["invoiceStatus"];

if (!$id) {
    header("Location:../general/permissiondenied.php");
}

$invoices = new \phpCollab\Invoices\Invoices();
$projects = new \phpCollab\Projects\Projects();

$detailInvoice = $invoices->getInvoiceById($id);

$projectDetail = $projects->getProjectById($detailInvoice["inv_project"]);

$listInvoicesItems = $invoices->getActiveInvoiceItemsByInvoiceId($id);

$comptListInvoicesItems = count($listInvoicesItems);

if ($projectDetail["pro_owner"] != $idSession) {
    header("Location:../general/permissiondenied.php");

}

$action = isset($_GET['action']) ? $_GET['action'] : null;

/**
 * Update invoice
 */
if ($action == "update") {
    $pub = isset($_POST['pub']) ? $_POST['action'] : 0;
    $st = isset($_POST['st']) ? $_POST['st'] : 0;

    if ($pub == "") {
        $pub = "1";
    }
    if ($st == "1") {
        $datesent = $GLOBALS["date"];
    }

    $tmpquery = "UPDATE {$tableCollab["invoices"]} SET header_note=:header_note,footer_note=:footer_note,published=:published,status=:status,due_date=:due_date,date_sent=:date_sent,total_ex_tax=:total_ex_tax,total_inc_tax=:total_inc_tax,tax_rate=:tax_rate,tax_amount=:tax_amount,modified=:modified WHERE id = :id";
    phpCollab\Util::newConnectSql(
        $tmpquery,
        [
            "header_note" => $_POST["header_note"],
            "footer_note" => $_POST["footer_note"],
            "published" => $pub,
            "status" => $st,
            "due_date" => $_POST["dd"],
            "date_sent" => $_POST["datesent"],
            "total_ex_tax" => $_POST["total_ex_tax"],
            "total_inc_tax" => $_POST["total_inc_tax"],
            "tax_rate" => $_POST["tax_rate"],
            "tax_amount" => $_POST["tax_amount"],
            "modified" => $dateheure,
            "id" => $id
        ]
    );

    foreach ($listInvoicesItems as $item) {
        $tmpquery = "UPDATE {$tableCollab["invoices_items"]} SET title=:title,position=:position,amount_ex_tax=:amount_ex_tax WHERE id = :id";
        phpCollab\Util::newConnectSql(
            $tmpquery,
            [
                "title" => $item["invitem_title"],
                "position" => $item["invitem_position"],
                "amount_ex_tax" => $item["invitem_amount_ex_tax"],
                "id" => $item["invitem_id"]
            ]
        );
    }
    phpCollab\Util::headerFunction("../invoicing/viewinvoice.php?msg=update&id=$id");
}

/**
 * Edit invoice
 */
//set value in form
$header_note = $detailInvoice["inv_header_note"];
$footer_note = $detailInvoice["inv_footer_note"];
$datesent = $detailInvoice["inv_date_sent"];
$dd = $detailInvoice["inv_due_date"];
$total_ex_tax = $detailInvoice["inv_total_ex_tax"];
$tax_rate = $detailInvoice["inv_tax_rate"];
$tax_amount = $detailInvoice["inv_tax_amount"];
$total_inc_tax = $detailInvoice["inv_total_inc_tax"];
$st = $detailInvoice["inv_status"];

$pub = $detailInvoice["inv_published"];
if ($pub == "0") {
    $checkedPub = "checked";
}

$includeCalendar = true; //Include Javascript files for the pop-up calendar
include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../clients/listclients.php?", $strings["clients"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../clients/viewclient.php?id=" . $projectDetail["pro_org_id"], $projectDetail["pro_org_name"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../invoicing/listinvoices.php?client=" . $projectDetail["pro_organization"], $strings["invoices"], "in"));

if ($id != "") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../invoicing/viewinvoice.php?id=" . $detailInvoice["inv_id"], $detailInvoice["inv_id"], "in"));
    $blockPage->itemBreadcrumbs($strings["edit_invoice"]);
}
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();

if ($id != "") {
    $block1->form = "invoice";
    $block1->openForm("../invoicing/editinvoice.php?id=$id&action=update&#" . $block1->form . "Anchor");
}

if (isset($error) && !empty($error)) {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

if ($id != "") {
    $block1->heading($strings["edit_invoice"] . " : " . $detailInvoice["inv_id"]);
}

$block1->openContent();
$block1->contentTitle($strings["details"]);

$block1->contentRow($strings["header_note"], '<textarea rows="10" style="width: 400px; height: 100px;" name="header_note" cols="47">' . $header_note . '</textarea>');
$block1->contentRow($strings["footer_note"], '<textarea rows="10" style="width: 400px; height: 100px;" name="footer_note" cols="47">' . $footer_note . '</textarea>');

$listInvoicesItems = $invoices->getActiveInvoiceItemsByInvoiceId($id);

$selectStatus = '<select name="st">';

$comptSta = count($invoiceStatus);
if ($detailInvoice["inv_status"] == "0") {
    $begin = "0";
} else {
    $begin = "1";
}

foreach ($listInvoicesItems as $item) {
    if ($listInvoicesItems["invitem_completed"] == "0") {
        $comptSta = "1";
        $notCompleted = $strings["note_invoice_items_notcompleted"];
        break;
    }
}

for ($i = $begin; $i < $comptSta; $i++) {
    if ($detailInvoice["inv_status"] == $i) {
        $selectStatus .= '<option value="' . $i . '" selected>' . $invoiceStatus[$i] . '</option>';
    } else {
        $selectStatus .= '<option value="' . $i . '">' . $invoiceStatus[$i] . '</option>';
    }
}

$selectStatus .= '</select>' . $notCompleted;

$block1->contentRow($strings["status"], $selectStatus);

if ($dd == "") {
    $dd = "--";
}

$block1->contentRow($strings["due_date"], "<input type='text' name='dd' id='due_date' size='20' value='$dd'><input type='button' value=' ... ' id='trigDueDate''>");
echo "
<script type='text/javascript'>
    Calendar.setup({
        inputField     :    'due_date',
        button         :    'trigDueDate',
        $calendar_common_settings
    });
</script>
";
$block1->contentRow($strings["published"], "<input size=\"32\" value=\"0\" name=\"pub\" type=\"checkbox\" $checkedPub>");

$block1->contentTitle($strings["calculation"]);

echo '<tr class="odd"><td valign="top" class="leftvalue">' . $strings["items"] . ' :</td><td>';
echo '<table cellpadding="0" cellspacing="0">';

echo '<input type="hidden" name="comptListInvoicesItems" value="' . $comptListInvoicesItems . '">';
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
                var amount_due = subtotal + (subtotal * ratePercent) / 100;

                document.invoiceForm["total_inc_tax"].value = amount_due;
            }

            if (subtotal != 0 && ratePercent != '') {
                var tax_part = (subtotal * ratePercent) / 100;
                document.invoiceForm["tax_amount"].value = tax_part;
            }
        }
    </script>

<?php
if ($listInvoicesItems) {
    echo "<tr><td>" . $strings["position"] . "</td><td>" . $strings["title"] . "</td><td>" . $strings["amount_ex_tax"] . "</td><td>" . $strings["completed"] . "</td></tr>";
    $itemCount = 0;
    foreach ($listInvoicesItems as $item) {
        if ($item["invitem_completed"] == "1") {
            $completeValue = $strings["yes"];
        } else {
            $completeValue = $strings["no"];
        }
        echo <<<TR
        <tr>
            <td width="50"><input type="hidden" name="itemId[{$itemCount}]" size="20" value="{$item["invitem_id"]}"><input type="text" name="position[{$itemCount}]" size="3" value="{$item["invitem_position"]}"></td>
            <td width="200"><input type="text" name="title[{$itemCount}]" size="30" value="{$item["invitem_title"]}"></td>
            <td width="50"><input type="text" name="item{$itemCount}" size="20" value="{$item["invitem_amount_ex_tax"]}" tabindex="{$itemCount}" onblur="calc(this)"></td>
            <td width="50">{$completeValue}</td>
        </tr>
TR;
        $itemCount++;
    }
}

echo "</table>";
echo "</td></tr>";

$block1->contentRow($strings["total_ex_tax"], "<input type=\"text\" name=\"total_ex_tax\" size=\"20\" value=\"$total_ex_tax\">");
$block1->contentRow($strings["tax_rate"], "<input type=\"text\" name=\"tax_rate\" size=\"20\" value=\"$tax_rate\" onblur=\"calc(this)\"> %");
$block1->contentRow($strings["tax_amount"], "<input type=\"text\" name=\"tax_amount\" size=\"20\" value=\"$tax_amount\">");
$block1->contentRow($strings["total_inc_tax"], "<input type=\"text\" name=\"total_inc_tax\" size=\"20\" value=\"$total_inc_tax\">");

echo <<<TR
<tr class="odd">
    <td valign="top" class="leftvalue">&nbsp;</td>
    <td><input type="SUBMIT" value="{$strings["save"]}"></td>
</tr>
TR;

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/themes/' . THEME . '/footer.php';
