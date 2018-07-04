<?php
#Application name: PhpCollab
#Status page: ?
#Path by root: ../users/exportusers.php

// PDF setup
include('../includes/class.ezpdf.php');
$pdf =& new Cezpdf();
$pdf->selectFont('../includes/fonts/Helvetica.afm');
$pdf->ezSetMargins(50, 70, 50, 50);

// include files
$checkSession = "true";
include '../includes/library.php';


// session checking to prevent nonadmins from accessing file. Change or remove to give access to Users.
if ($profilSession != (0 && 2 && 5)) {
    phpCollab\Util::headerFunction('../general/permissiondenied.php');
}

// get company info
$tmpquery = "WHERE org.id = '1'";
$clientDetail = new phpCollab\Request();
$clientDetail->openOrganizations($tmpquery);

$cn = $clientDetail->org_name[0];
$add = $clientDetail->org_address1[0];
$wp = $clientDetail->org_phone[0];
$url = $clientDetail->org_url[0];
$email = $clientDetail->org_email[0];
$c = $clientDetail->org_comments[0];

// print company info at top of page
$pdf->ezText("<b>" . $cn . "</b>", 18, array('justification' => 'center'));
$pdf->ezText($add, 12, array('justification' => 'center'));
$pdf->ezText($wp, 12, array('justification' => 'center'));
$pdf->ezText($url, 12, array('justification' => 'center'));
$pdf->ezText("\n");

// get user info
$blockPage = new phpCollab\Block();

if ($msg != "") {
    if (file_exists("modules/PhpCollab/pnversion.php")) {
        include 'modules/PhpCollab/includes/messages.php';
    } else {
        include '../includes/messages.php';
    }
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();

$block1->sorting("users", $sortingUser["users"], "mem.name ASC", $sortingFields = array(0 => "mem.name", 1 => "mem.login", 2 => "mem.email_work", 3 => "mem.profil", 4 => "log.connected"));

if ($demoMode == "true") {
    $tmpquery = "WHERE mem.id != '1' AND mem.profil != '3' ORDER BY $block1->sortingValue";
} else {
    $tmpquery = "WHERE mem.id != '1' AND mem.profil != '3' AND mem.id != '2' ORDER BY $block1->sortingValue";
}
$listMembers = new phpCollab\Request();
$listMembers->openMembers($tmpquery);
$comptListMembers = count($listMembers->mem_id);


for ($i = 0; $i < $comptListMembers; $i++) {

    $name = $listMembers->mem_name[$i];
    $title = $listMembers->mem_title[$i];
    $email = $listMembers->mem_email_work[$i];
    $phone = $listMembers->mem_phone_work[$i];
    $mobile = $listMembers->mem_mobile[$i];
    $fax = $listMembers->mem_fax[$i];
//$ = $listMembers->[$i];

// stuff the array with data
    $data[] = array('name' => $name, 'title' => $title, 'email' => $email, 'phone' => $phone, 'mobile' => $mobile, 'fax' => $fax);
}

// print the page number 
$pdf->ezStartPageNumbers(526, 34, 6, 'right', '', 1);

// put a line top and bottom on all the pages and company info on the bottom
$all = $pdf->openObject();
$pdf->saveState();
$pdf->setStrokeColor(0, 0, 0, 1);
$pdf->line(20, 40, 578, 40);
$pdf->line(20, 822, 578, 822);
$pdf->addText(50, 34, 6, $cn . " - " . $url);
$pdf->AddText(510, 34, 6, "Page ");
$pdf->restoreState();
$pdf->closeObject();
$pdf->addObject($all, 'all');

// make the table
$pdf->ezTable($data, array('name' => 'Name', 'title' => 'Title', 'email' => 'Email', 'phone' => 'Phone', 'mobile' => 'Mobile', 'fax' => 'Fax'), '', array('fontSize' => 10, 'maxWidth' => 550));

// output the PDF
$pdf->ezStream();
?>