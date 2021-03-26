<?php
#Application name: PhpCollab
#Status page: ?
#Path by root: ../users/exportusers.php


// include files

$checkSession = "true";
require_once '../includes/library.php';


// session checking to prevent nonadmins from accessing file. Change or remove to give access to Users.
if ($session->get("profile") != (0 && 2 && 5)) {
    phpCollab\Util::headerFunction('../general/permissiondenied.php');
}

$organizations = $container->getOrganizationsManager();

// PDF setup
$pdf = $container->getExportPDFService();
$pdf->selectFont('../includes/fonts/Helvetica.afm');
$pdf->ezSetMargins(50, 70, 50, 50);

$clientDetail = $organizations->getOrganizationById(1);

$cn = $clientDetail["org_name"];
$add = $clientDetail["org_address1"];
$wp = $clientDetail["org_phone"];
$url = $clientDetail["org_url"];
$email = $clientDetail["org_email"];
$c = $clientDetail["org_comments"];

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

$block1->sorting("users", $sortingUser["users"], "mem.name ASC", $sortingFields = array(
    0 => "mem.name",
    1 => "mem.login",
    2 => "mem.email_work",
    3 => "mem.profil",
    4 => "log.connected"
));

// Get a collection of members, except for Client Users, Administrator, and the demo user
if ($demoMode == "true") {
    $listMembers = $members->getNonClientMembersExcept(1);
} else {
    $listMembers = $members->getNonClientMembersExcept('1,2');
}

foreach ($listMembers as $member) {
    $name = $member["mem_name"];
    $title = $member["mem_title"];
    $email = $member["mem_email_work"];
    $phone = $member["mem_phone_work"];
    $mobile = $member["mem_mobile"];
    $fax = $member["mem_fax"];

// stuff the array with data
    $data[] = array(
        'name' => $name,
        'title' => $title,
        'email' => $email,
        'phone' => $phone,
        'mobile' => $mobile,
        'fax' => $fax
    );
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
$pdf->ezTable($data, array(
    'name' => 'Name',
    'title' => 'Title',
    'email' => 'Email',
    'phone' => 'Phone',
    'mobile' => 'Mobile',
    'fax' => 'Fax'
), '', array('fontSize' => 10, 'maxWidth' => 550));

// output the PDF
$pdf->ezStream();
