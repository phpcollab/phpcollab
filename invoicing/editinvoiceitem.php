<?php

use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

$checkSession = "true";
require_once '../includes/library.php';

$id = $request->query->get("id");

if (empty($id)) {
    header("Location:../general/permissiondenied.php");
}

try {
    $invoices = $container->getInvoicesLoader();
    $projects = $container->getProjectsLoader();
    $organizations = $container->getOrganizationsManager();
    $services = $container->getServicesLoader();
} catch (Exception $exception) {
    $logger->error('Exception', ['Error' => $exception->getMessage()]);
}

$rateType = $GLOBALS["rateType"];

$detailInvoiceItem = $invoices->getInvoiceItemById($id);

if ($detailInvoiceItem) {
    $detailInvoice = $invoices->getInvoiceById($detailInvoiceItem["invitem_invoice"]);

    $projectDetail = $projects->getProjectById($detailInvoice["inv_project"]);
}

if ($request->isMethod('post')) {
    try {
        if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
            if ($request->request->get("action") == "update") {
                try {
                    $success = $invoices->updateItem(
                        $id,
                        $request->request->get("rate_type"),
                        $request->request->get("rate_value"),
                        $request->request->get("amount_ex_tax")
                    );
                    phpCollab\Util::headerFunction("../invoicing/viewinvoice.php?msg=update&id={$detailInvoiceItem["invitem_invoice"]}");
                } catch (Exception $exception) {
                    $error = $strings["error_editing_invoice"];
                    $logger->error($strings["error_editing_invoice"] . ': ' . $exception->getMessage());
                }
            }
        }
    } catch (InvalidCsrfTokenException $csrfTokenException) {
        $logger->error('CSRF Token Error', [
            'Invoicing: Edit invoice item',
            '$_SERVER["REMOTE_ADDR"]' => $request->server->get("REMOTE_ADDR"),
            '$_SERVER["HTTP_X_FORWARDED_FOR"]'=> $request->server->get('HTTP_X_FORWARDED_FOR')
        ]);
    } catch (Exception $e) {
        $logger->critical('Exception', ['Error' => $e->getMessage()]);
        $msg = 'permissiondenied';
    }
}


//set value in form
$worked_hours = $detailInvoiceItem["invitem_worked_hours"];
$amount_ex_tax = $detailInvoiceItem["invitem_amount_ex_tax"];
$rate_type = $detailInvoiceItem["invitem_rate_type"];
$rate_value = $detailInvoiceItem["invitem_rate_value"];

$setTitle .= " " . $strings["edit_invoiceitem"];

include APP_ROOT . '/views/layout/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
$blockPage->itemBreadcrumbs($blockPage->buildLink("../clients/listclients.php?", $strings["clients"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../clients/viewclient.php?id=" . $projectDetail["pro_org_id"],
    $projectDetail["pro_org_name"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../invoicing/listinvoices.php?client=" . $projectDetail["pro_organization"],
    $strings["invoices"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../invoicing/viewinvoice.php?id=" . $detailInvoice["inv_id"],
    $detailInvoice["inv_id"], "in"));

$blockPage->itemBreadcrumbs($detailInvoiceItem["invitem_title"]);
$blockPage->itemBreadcrumbs($strings["edit_invoiceitem"]);
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();

if (!empty($detailInvoiceItem)) {
    $block1->form = "invoice";
    $block1->openForm("../invoicing/editinvoiceitem.php?id=" . $id . "&#" . $block1->form . "Anchor", null,
        $csrfHandler);
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
} else {
    if ($detailInvoiceItem["invitem_rate_type"] == "b") {
        $checkedb = "checked";
    } else {
        if ($detailInvoiceItem["invitem_rate_type"] == "c") {
            $checkedc = "checked";
        } else {
            if (is_numeric($detailInvoiceItem["invitem_rate_type"])) {
                $checked[$detailInvoiceItem["invitem_rate_type"]] = "checked";
            }
        }
    }
}

$detailClient = $organizations->getOrganizationById($projectDetail["pro_organization"]);

$listServices = $services->getAllServices('serv.name ASC');

if ($listServices) {
    $selectService = '';

    foreach ($listServices as $service) {
        // Escape for JavaScript context to prevent XSS
        $safeServHourlyRate = htmlspecialchars($service["serv_hourly_rate"], ENT_QUOTES, 'UTF-8');
        $safeServId = htmlspecialchars($service["serv_id"], ENT_QUOTES, 'UTF-8');
        $safeServName = htmlspecialchars($service["serv_name"], ENT_QUOTES, 'UTF-8');
        $checkedValue = isset($checked[$service["serv_id"]]) ? $checked[$service["serv_id"]] : '';

        $selectService .= <<<RADIOITEM
<label style="display: block; width: fit-content">
    <input
        type="radio"
        name="rate_type"
        value="$safeServId"
        onclick="rateField('$safeServHourlyRate');"
        id="service$safeServId"
        $checkedValue> {$rateType["3"]} [$safeServName]
</label>
RADIOITEM;
    }
}

// Escape for JavaScript context to prevent XSS
$safeProjectHourlyRate = htmlspecialchars($projectDetail["pro_hourly_rate"], ENT_QUOTES, 'UTF-8');
$safeOrgHourlyRate = htmlspecialchars($detailClient["org_hourly_rate"], ENT_QUOTES, 'UTF-8');
$safeWorkedHours = htmlspecialchars($worked_hours, ENT_QUOTES, 'UTF-8');

$block1->contentRow($strings["worked_hours"],
    '<input type="hidden" name="worked_hours" value="' . $safeWorkedHours . '">' . $safeWorkedHours);
$radioButtons = <<<HTML
<label style="display: block; width: fit-content"><input type="radio" name="rate_type" value="a" $checkeda id="custom"> {$rateType["0"]}</label>
<label style="display: block; width: fit-content"><input type="radio" name="rate_type" value="b" onclick="rateField('$safeProjectHourlyRate');" $checkedb id="project"> {$rateType["1"]}</label>
<label style="display: block; width: fit-content"><input type="radio" name="rate_type" value="c" onclick="rateField('$safeOrgHourlyRate');" $checkedc id="organization"> {$rateType["2"]}</label>
$selectService
HTML;

$block1->contentRow($strings["rate_type"], $radioButtons);
$block1->contentRow($strings["rate_value"],
    '<input type="text" name="rate_value" size="20" value="' . $rate_value . '" onchange="document.invoiceForm.rate_type[0].checked=true">');
$block1->contentRow($strings["amount_ex_tax"],
    '<input type="text" name="amount_ex_tax" size="20" value="' . $amount_ex_tax . '" readonly>');

echo <<<HTML
    <tr class="odd">
        <td class="leftvalue">&nbsp;</td>
        <td><button type="submit" name="action" value="update">{$strings["save"]}</button></td>
    </tr>
HTML;

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/views/layout/footer.php';
