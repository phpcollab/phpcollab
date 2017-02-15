<?php

$checkSession = "true";
include_once '../includes/library.php';

$id = isset($_GET["id"]) ? $_GET["id"] : null;
$invoiceitem = isset($_GET["invoiceitem"]) ? $_GET["invoiceitem"] : null;

if (empty($id)) {
    header("Location:../general/permissiondenied.php");
}

$invoices = new \phpCollab\Invoices\Invoices();
$projects = new \phpCollab\Projects\Projects();
$organizations = new \phpCollab\Organizations\Organizations();
$services = new \phpCollab\Services\Services();

$tableCollab = $GLOBALS["tableCollab"];

$detailInvoiceItem = $invoices->getInvoiceItemById($id);

if ($detailInvoiceItem) {
    $detailInvoice = $invoices->getInvoiceById($detailInvoiceItem["invitem_invoice"]);

    $projectDetail = $projects->getProjectById($detailInvoice["inv_project"]);
}

if ($_GET["action"] == "update") {
    phpCollab\Util::newConnectSql("UPDATE {$tableCollab["invoices_items"]} SET rate_type=:rate_type,rate_value=:rate_value,amount_ex_tax=:amount_ex_tax WHERE id = :id", ["rate_type" => $_POST["rate_type"], "rate_value" => $_POST["rate_value"], "amount_ex_tax" => $_POST["amount_ex_tax"], "id" => $_POST["invoiceitem"]]);
    phpCollab\Util::headerFunction("../invoicing/viewinvoice.php?msg=update&id=$id");
}

//set value in form
$worked_hours = $detailInvoiceItem["invitem_worked_hours"];
$amount_ex_tax = $detailInvoiceItem["invitem_amount_ex_tax"];
$rate_type = $detailInvoiceItem["invitem_rate_type"];
$rate_value = $detailInvoiceItem["invitem_rate_value"];

include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../clients/listclients.php?", $strings["clients"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../clients/viewclient.php?id=" . $projectDetail["pro_org_id"], $projectDetail["pro_org_name"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../invoicing/listinvoices.php?client=" . $projectDetail["pro_organization"], $strings["invoices"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../invoicing/viewinvoice.php?id=" . $detailInvoice["inv_id"], $detailInvoice["inv_id"], "in"));

$blockPage->itemBreadcrumbs($detailInvoiceItem["invitem_title"]);
$blockPage->itemBreadcrumbs($strings["edit_invoiceitem"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox(phpCollab\Util::returnGlobal("msgLabel"));
}

$block1 = new phpCollab\Block();

if (!empty($invoiceitem)) {
    $block1->form = "invoice";
    $block1->openForm("../invoicing/editinvoiceitem.php?invoiceitem=$invoiceitem&id=" . $detailInvoice["inv_id"] . "&action=update&#" . $block1->form . "Anchor");
}

if (!empty($error)) {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

if (!empty($invoiceitem)) {
    $block1->heading($strings["edit_invoiceitem"] . " : " . $detailInvoiceItem["invitem_title"]);
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
if ($detailInvoiceItem["invitem_rate_type"] == "a") {
    $checkeda = "checked";
} else if ($detailInvoiceItem["invitem_rate_type"] == "b") {
    $checkedb = "checked";
} else if ($detailInvoiceItem["invitem_rate_type"] == "c") {
    $checkedc = "checked";
} else if (is_numeric($detailInvoiceItem["invitem_rate_type"])) {
    $checked{$detailInvoiceItem["invitem_rate_type"]} = "checked";
}

$detailClient = $organizations->getOrganizationById($projectDetail["pro_organization"]);

$listServices = $services->getAllServices('serv.name ASC');

if ($listServices) {
    $servicesCount = 0;
    foreach ($listServices as $service) {
        $j = $servicesCount + 1;
        $selectService .= '<input type="radio" name="rate_type" value="'. $listServices["serv_id"] . '" onclick="rateField(\'' . $listServices["serv_hourly_rate"] . '\');" ' . $checked{$j} . ' id="service' . $listServices["serv_id"] . '"> <label for="service'. $listServices["serv_id"] . '">' . $rateType["3"] . ' [' . $listServices["serv_name"] . ']</label><br/>';
        $servicesCount++;
    }
}

$block1->contentRow($strings["worked_hours"], "<input type=\"hidden\" name=\"worked_hours\"value=\"$worked_hours\">$worked_hours");
$block1->contentRow($strings["rate_type"], "<input type=\"radio\" name=\"rate_type\" value=\"a\" $checkeda id=\"custom\"> <label for=\"custom\">" . $rateType["0"] . "</label><br/><input type=\"radio\" name=\"rate_type\" value=\"b\" onclick=\"rateField('" . $projectDetail["pro_hourly_rate"] . "');\" $checkedb id=\"project\"> <label for=\"project\">" . $rateType["1"] . "</label><br/><input type=\"radio\" name=\"rate_type\" value=\"c\" onclick=\"rateField('" . $detailClient["org_hourly_rate"] . "');\" $checkedc id=\"organization\"> <label for=\"organization\">" . $rateType["2"] . "</label><br/>$selectService");
$block1->contentRow($strings["rate_value"], "<input type=\"text\" name=\"rate_value\" size=\"20\" value=\"$rate_value\" onchange=\"document.invoiceForm.rate_type[0].checked=true\">");
$block1->contentRow($strings["amount_ex_tax"], "<input type=\"text\" name=\"amount_ex_tax\" size=\"20\" value=\"$amount_ex_tax\" readonly>");

echo "<tr class=\"odd\"><td valign=\"top\" class=\"leftvalue\">&nbsp;</td><td><input type=\"SUBMIT\" value=\"" . $strings["save"] . "\"></td></tr>";

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/themes/' . THEME . '/footer.php';
