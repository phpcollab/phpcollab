<?php
include '../includes/library.php';
include '../includes/phplib/template.php';

$tmpquery = "WHERE inv.id = '$id'";
$detailInvoice = new Request();
$detailInvoice->openInvoices($tmpquery);

$tmpquery = "WHERE pro.id = '".$detailInvoice->inv_project[0]."'";
$projectDetail = new Request();
$projectDetail->openProjects($tmpquery);

$tmpquery = "WHERE org.id = '".$projectDetail->pro_organization[0]."'";
$clientDetail = new Request();
$clientDetail->openOrganizations($tmpquery);
$comptClientDetail = count($clientDetail->org_id);

$tmpquery = "WHERE org.id = '1'";
$mycompanyDetail = new Request();
$mycompanyDetail->openOrganizations($tmpquery);
$comptMycompanyDetailDetail = count($mycompanyDetail->org_id);

$tmpquery = "WHERE invitem.invoice = '$id' AND invitem.active = '1' ORDER BY invitem.position ASC";
$listInvoicesItems = new Request();
$listInvoicesItems->openInvoicesItems($tmpquery);
$comptListInvoicesItems = count($listInvoicesItems->invitem_id);

$template = new Template();

$template->set_file('invoice','tpl_invoice.html');

$template->set_var(array(
	'val_CLIENTNAME' => $clientDetail->org_name[0],
	'val_CLIENTADDRESS' => nl2br($clientDetail->org_address1[0]),

	'val_COMPANYNAME' => $mycompanyDetail->org_name[0],
	'val_COMPANYADDRESS' => nl2br($mycompanyDetail->org_address1[0]),

	'str_INVOICE' => $strings["invoice"],
	'val_HEADER' => $detailInvoice->inv_header_note[0],
	'val_FOOTER' => $detailInvoice->inv_footer_note[0],

	'val_TOTALINCTAX' => $detailInvoice->inv_total_inc_tax[0],
	'val_TOTALEXTAX' => $detailInvoice->inv_total_ex_tax[0],
	'val_TAXRATE' => $detailInvoice->inv_tax_rate[0],
	'val_TAXAMOUNT' => $detailInvoice->inv_tax_amount[0],

	'str_TOTALINCTAX' => $strings["total_inc_tax"],
	'str_TOTALEXTAX' => $strings["total_ex_tax"],
	'str_TAXRATE' => $strings["tax_rate"],
	'str_TAXAMOUNT' => $strings["tax_amount"],

	'str_TITLE' => $strings["title"],
	'str_AMOUNTEXTAX' => $strings["amount_ex_tax"],

));

		$template->set_block('invoice', 'items', 'block');

for ($i=0;$i<$comptListInvoicesItems;$i++) {

		$template->set_var(array(
			'val_TITLE' => $listInvoicesItems->invitem_title[$i],
			'val_AMOUNTEXTAX' => $listInvoicesItems->invitem_amount_ex_tax[$i],
		));
		$template->Parse('block', 'items', true); 

}

$dump_buffer = $template->finish($template->parse('invoice', 'invoice'));

$filename = $strings["invoice"].$detailInvoice->inv_id[0];

        $ext       = 'html';
        $mime_type = 'text/html';

    // Send headers
    header('Content-Type: ' . $mime_type);
    // lem9: we need "inline" instead of "attachment" for IE 5.5
    $content_disp = (USR_BROWSER_AGENT == 'IE') ? 'inline' : 'attachment';
    header('Content-Disposition:  ' . $content_disp . '; filename="' . $filename . '.' . $ext . '"');
    header('Pragma: no-cache');
    header('Expires: 0');


echo $dump_buffer;
?>
