<?php

$checkSession = "true";
include_once '../includes/library.php';

$invoiceItemId = $request->query->get('id', null);

$msgLabel = $GLOBALS["msgLabel"];
$strings = $GLOBALS["strings"];
$invoiceStatus = $GLOBALS["invoiceStatus"];

if (!$invoiceItemId) {
    header("Location:../general/permissiondenied.php");
}

$invoices = $container->getInvoicesLoader();
$projects = $container->getProjectsLoader();

$detailInvoice = $invoices->getInvoiceById($invoiceItemId);

$projectDetail = $projects->getProjectById($detailInvoice["inv_project"]);

$listInvoicesItems = $invoices->getActiveInvoiceItemsByInvoiceId($invoiceItemId);

$comptListInvoicesItems = count($listInvoicesItems);

if ($projectDetail["pro_owner"] != $session->get("id")) {
    header("Location:../general/permissiondenied.php");

}

$action = $request->query->get('action', null);

/**
 * Update invoice
 */
if ($request->isMethod('post')) {
    try {
        if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
            if ($request->request->get('action') == "update") {
                $pub = !empty($request->request->get('pub')) ? $request->request->get('pub') : 0;
                $st = !empty($request->request->get('st')) ? $request->request->get('st') : 0;

                if ($pub == "") {
                    $pub = "1";
                }
                if ($st == "1") {
                    $datesent = $GLOBALS["date"];
                }

                $invoices->updateInvoice(
                    $invoiceItemId,
                    $request->request->get('header_note'),
                    $request->request->get('footer_note'),
                    $pub,
                    $st,
                    $request->request->get('dd'),
                    $request->request->get('datesent'),
                    $request->request->get('total_ex_tax'),
                    $request->request->get('total_inc_tax'),
                    $request->request->get('tax_rate'),
                    $request->request->get('tax_amount')
                );

                foreach ($listInvoicesItems as $item) {
                    $invoices->editInvoiceItems($item['invitem_id'], $item["invitem_title"], $item["invitem_position"],
                        $item["invitem_amount_ex_tax"]);
                }
                phpCollab\Util::headerFunction("../invoicing/viewinvoice.php?msg=update&id=$invoiceItemId");
            }
        }
    } catch (Exception $e) {
        $logger->critical('CSRF Token Error', [
            'edit bookmark' => $request->request->get("id"),
            '$_SERVER["REMOTE_ADDR"]' => $_SERVER['REMOTE_ADDR'],
            '$_SERVER["HTTP_X_FORWARDED_FOR"]' => $_SERVER['HTTP_X_FORWARDED_FOR']
        ]);
        $msg = 'permissiondenied';
    }
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
$blockPage->itemBreadcrumbs($blockPage->buildLink("../clients/viewclient.php?id=" . $projectDetail["pro_org_id"],
    $projectDetail["pro_org_name"], "in"));
$blockPage->itemBreadcrumbs($blockPage->buildLink("../invoicing/listinvoices.php?client=" . $projectDetail["pro_organization"],
    $strings["invoices"], "in"));

if ($invoiceItemId != "") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../invoicing/viewinvoice.php?id=" . $detailInvoice["inv_id"],
        $detailInvoice["inv_id"], "in"));
    $blockPage->itemBreadcrumbs($strings["edit_invoice"]);
}
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();

if ($invoiceItemId != "") {
    $block1->form = "invoice";
    $block1->openForm("../invoicing/editinvoice.php?id=$invoiceItemId&#" . $block1->form . "Anchor", null,
        $csrfHandler);
}

if (isset($error) && !empty($error)) {
    $block1->headingError($strings["errors"]);
    $block1->contentError($error);
}

if ($invoiceItemId != "") {
    $block1->heading($strings["edit_invoice"] . " : " . $detailInvoice["inv_id"]);
}

$block1->openContent();
$block1->contentTitle($strings["details"]);

$block1->contentRow($strings["header_note"],
    '<textarea rows="10" style="width: 400px; height: 100px;" name="header_note" cols="47">' . $header_note . '</textarea>');
$block1->contentRow($strings["footer_note"],
    '<textarea rows="10" style="width: 400px; height: 100px;" name="footer_note" cols="47">' . $footer_note . '</textarea>');

$listInvoicesItems = $invoices->getActiveInvoiceItemsByInvoiceId($invoiceItemId);

$selectStatus = '<select name="st">';

$comptSta = count($invoiceStatus);
if ($detailInvoice["inv_status"] == "0") {
    $begin = "0";
} else {
    $begin = "1";
}

$notCompleted = null;

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

$block1->contentRow($strings["due_date"],
    "<input type='text' name='dd' id='due_date' size='20' value='$dd'><input type='button' value=' ... ' id='trigDueDate''>");
echo <<<SCRIPT
<script type='text/javascript'>
    Calendar.setup({
        inputField     :    'due_date',
        button         :    'trigDueDate',
        {$calendar_common_settings}
    })
</script>
SCRIPT;

$block1->contentRow($strings["published"], '<input size="32" value="0" name="pub" type="checkbox"' . $checkedPub . '>');

$block1->contentTitle($strings["calculation"]);

echo <<<HTML
<tr class="odd">
    <td class="leftvalue">{$strings["items"]} :</td>
    <td>
        <table class="calculation">
            <input type="hidden" name="comptListInvoicesItems" value="{$comptListInvoicesItems}">
HTML;
echo <<<SCRIPT
<script type="text/JavaScript">
    function calc(w) {
        var item = [];
        var subtotal = 0;

        for (var i = 0; i < {$comptListInvoicesItems}; i++) {
            item[i] = 1 * document.invoiceForm["item" + i].value;
            if (item[i] === "") {
                item[i] = 0;
            } else {
                subtotal += item[i];
            }
        }
        document.invoiceForm["total_ex_tax"].value = subtotal.toFixed(2);

        var ratePercent = document.invoiceForm["tax_rate"].value;

        if (subtotal !== 0) {
            var amount_due = subtotal + (subtotal * ratePercent) / 100;

            document.invoiceForm["total_inc_tax"].value = amount_due.toFixed(2);
        }

        if (subtotal != 0 && ratePercent != '') {
            var tax_part = (subtotal * ratePercent) / 100;
            document.invoiceForm["tax_amount"].value = tax_part.toFixed(2);
        }
    }
</script>
SCRIPT;

if ($listInvoicesItems) {
    echo <<<HTML
    <tr>
        <td>{$strings["position"]}</td>
        <td>{$strings["title"]}</td>
        <td>{$strings["amount_ex_tax"]}</td>
        <td>{$strings["completed"]}</td>
    </tr>
HTML;

    $itemCount = 0;
    foreach ($listInvoicesItems as $item) {
        if ($item["invitem_completed"] == "1") {
            $completeValue = $strings["yes"];
        } else {
            $completeValue = $strings["no"];
        }
        echo <<<TR
        <tr>
            <td><input type="hidden" name="itemId[{$itemCount}]" size="20" value="{$item["invitem_id"]}"><input type="text" name="position[{$itemCount}]" size="3" value="{$item["invitem_position"]}"></td>
            <td><input type="text" name="title[{$itemCount}]" size="30" value="{$item["invitem_title"]}"></td>
            <td><input type="text" name="item{$itemCount}" size="20" value="{$item["invitem_amount_ex_tax"]}" tabindex="{$itemCount}" onblur="calc(this)"></td>
            <td>{$completeValue}</td>
        </tr>
TR;
        $itemCount++;
    }
}

echo <<<HTML
    </table></td>
</tr>
HTML;


$block1->contentRow($strings["total_ex_tax"],
    '<input type="text" name="total_ex_tax" size="20" value="' . $total_ex_tax . '">');
$block1->contentRow($strings["tax_rate"],
    '<input type="text" name="tax_rate" size="20" value="' . $tax_rate . '" onblur="calc(this)"> %');
$block1->contentRow($strings["tax_amount"],
    '<input type="text" name="tax_amount" size="20" value="' . $tax_amount . '">');
$block1->contentRow($strings["total_inc_tax"],
    '<input type="text" name="total_inc_tax" size="20" value="' . $total_inc_tax . '">');

echo <<<TR
<tr class="odd">
    <td class="leftvalue"><input type="hidden" name="action" value="update"></td>
    <td><input type="submit" value="{$strings["save"]}"></td>
</tr>
TR;

$block1->closeContent();
$block1->closeForm();

include APP_ROOT . '/themes/' . THEME . '/footer.php';
