<?php

use phpCollab\Invoices\Invoices;
use phpCollab\Organizations\Organizations;
use phpCollab\Projects\Projects;
use phpCollab\Services\Services;

$checkSession = "true";
include_once '../includes/library.php';

$id = $request->query->get("id", null);

if (empty($id)) {
    header("Location:../general/permissiondenied.php");
}

$invoices = new Invoices();
$projects = new Projects();
$organizations = new Organizations();
$services = new Services();

$tableCollab = $GLOBALS["tableCollab"];
$rateType = $GLOBALS["rateType"];

$detailInvoiceItem = $invoices->getInvoiceItemById($id);

if ($detailInvoiceItem) {
    $detailInvoice = $invoices->getInvoiceById($detailInvoiceItem["invitem_invoice"]);

    $projectDetail = $projects->getProjectById($detailInvoice["inv_project"]);
}

if ($request->isMethod('post')) {
    if ($request->query->get("action") == "update") {
        try {
            $success = $invoices->updateItem(
                $id,
                $request->request->get("rate_type"),
                $request->request->get("rate_value"),
                $request->request->get("amount_ex_tax")
            );

//            echo "redirect to: " . "../invoicing/viewinvoice.php?msg=update&id={$detailInvoiceItem["invitem_invoice"]}";
//            die();
            phpCollab\Util::headerFunction("../invoicing/viewinvoice.php?msg=update&id={$detailInvoiceItem["invitem_invoice"]}");
        } catch (Exception $exception) {
            $error = $strings["error_editing_invoice"];
            error_log($strings["error_editing_invoice"] . ': ' . $exception->getMessage());
        }
    }
//        phpCollab\Util::newConnectSql("
//    UPDATE {$tableCollab["invoices_items"]}
//    SET
//        rate_type=:rate_type,
//        rate_value=:rate_value,
//        amount_ex_tax=:amount_ex_tax
//    WHERE id = :id
//    ", [
// "rate_type" => $_POST["rate_type"],
// "rate_value" => $_POST["rate_value"],
// "amount_ex_tax" => $_POST["amount_ex_tax"],
// "id" => $_POST["invoiceitem"]
// ]);
//    }

}


//set value in form
$worked_hours = $detailInvoiceItem["invitem_worked_hours"];
$amount_ex_tax = $detailInvoiceItem["invitem_amount_ex_tax"];
$rate_type = $detailInvoiceItem["invitem_rate_type"];
$rate_value = $detailInvoiceItem["invitem_rate_value"];

$setTitle .= " " . $strings["edit_invoiceitem"];

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

if (!empty($detailInvoiceItem)) {
    $block1->form = "invoice";
    $block1->openForm("../invoicing/editinvoiceitem.php?id=" . $id . "&action=update&#" . $block1->form . "Anchor");
}

if (!empty($error)) {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

if (!empty($detailInvoiceItem)) {
    $block1->heading($strings["edit_invoiceitem"] . " : " . $detailInvoiceItem["invitem_title"]);
}

$block1->openContent();
$block1->contentTitle($strings["calculation"]);

echo <<<SCRIPT
<script type="text/JavaScript">
    function rateField(ref) {
        document.invoiceForm["rate_value"].value = ref;
        document.invoiceForm["amount_ex_tax"].value = document.invoiceForm["rate_value"].value * document.invoiceForm["worked_hours"].value;
    }
</script>
SCRIPT;

$checked = null;

if ($detailInvoiceItem["invitem_rate_type"] == "a") {
    $checkeda = "checked";
} else if ($detailInvoiceItem["invitem_rate_type"] == "b") {
    $checkedb = "checked";
} else if ($detailInvoiceItem["invitem_rate_type"] == "c") {
    $checkedc = "checked";
} else if (is_numeric($detailInvoiceItem["invitem_rate_type"])) {
    $checked[$detailInvoiceItem["invitem_rate_type"]] = "checked";
}

$detailClient = $organizations->getOrganizationById($projectDetail["pro_organization"]);

$listServices = $services->getAllServices('serv.name ASC');

if ($listServices) {
    $servicesCount = 0;
    $selectService = '';
    foreach ($listServices as $service) {
        $j = $servicesCount + 1;
        $selectService .= <<<RADIOITEM
<label style="display: block">
    <input 
        type="radio" 
        name="rate_type" 
        value="{$service["serv_id"]}" 
        onclick="rateField('{$service["serv_hourly_rate"]}');" 
        id="service{$service["serv_id"]}"
        {$checked[$j]}> {$rateType["3"]} [{$service["serv_name"]}]
</label>
RADIOITEM;
        $servicesCount++;
    }
}

$block1->contentRow($strings["worked_hours"], '<input type="hidden" name="worked_hours" value="' . $worked_hours . '">' . $worked_hours);
$radioButtons = <<<HTML
<label style="display: block"><input type="radio" name="rate_type" value="a" {$checkeda} id="custom"> {$rateType["0"]}</label>
<label style="display: block"><input type="radio" name="rate_type" value="b" onclick="rateField('{$projectDetail["pro_hourly_rate"]}');" {$checkedb} id="project"> {$rateType["1"]}</label>
<label style="display: block"><input type="radio" name="rate_type" value="c" onclick="rateField('{$detailClient["org_hourly_rate"]}');" {$checkedc} id="organization"> {$rateType["2"]}</label>
{$selectService}
HTML;

$block1->contentRow($strings["rate_type"], $radioButtons);
$block1->contentRow($strings["rate_value"], '<input type="text" name="rate_value" size="20" value="' . $rate_value . '" onchange="document.invoiceForm.rate_type[0].checked=true">');
$block1->contentRow($strings["amount_ex_tax"], '<input type="text" name="amount_ex_tax" size="20" value="' . $amount_ex_tax . '" readonly>');

echo <<<HTML
    <tr class="odd">
        <td class="leftvalue">&nbsp;</td>
        <td><input type="submit" value="{$strings["save"]}"></td>
    </tr>
HTML;

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/themes/' . THEME . '/footer.php';
