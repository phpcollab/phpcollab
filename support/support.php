<?php
#Application name: PhpCollab
#Status page: 0

$checkSession = "true";
include_once '../includes/library.php';

$teams = $container->getTeams();
$projects = $container->getProjectsLoader();
$support = $container->getSupportLoader();

if ($supportType == "team") {
    $teamMember = "false";
    $teamMember = $teams->isTeamMember($request->query->get('project'), $session->get("id"));
}

if ($enableHelpSupport != "true") {
    phpCollab\Util::headerFunction('../general/permissiondenied.php');
}

if ($supportType == "admin") {
    if ($session->get("profile") != "0") {
        phpCollab\Util::headerFunction('../general/permissiondenied.php');
    }
}

if ($supportType == "team") {
    $requestProject = $projects->getProjectById($request->query->get('project'));

}

include APP_ROOT . '/themes/' . THEME . '/header.php';

$blockPage = new phpCollab\Block();
$blockPage->openBreadcrumbs();
if ($supportType == "team") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/listprojects.php?", $strings["projects"], "in"));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../projects/viewproject.php?id=" . $requestProject["pro_id"],
        $requestProject["pro_name"], "in"));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../support/listrequests.php?id=" . $requestProject["pro_id"],
        $strings["support_requests"], "in"));
} elseif ($supportType == "admin") {
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/admin.php?", $strings["administration"],
        "in"));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../administration/support.php?", $strings["support_management"],
        "in"));
}

if ($action == "new") {
    $blockPage->itemBreadcrumbs($strings["new_requests"]);
} elseif ($action == "open") {
    $blockPage->itemBreadcrumbs($strings["open_requests"]);
} elseif ($action == "complete") {
    $blockPage->itemBreadcrumbs($strings["closed_requests"]);
}
$blockPage->closeBreadcrumbs();

if ($msg != "") {
    include '../includes/messages.php';
    $blockPage->messageBox($msgLabel);
}

$block1 = new phpCollab\Block();
$block1->form = "srs";
$block1->openForm("../support/support.php?action=" . $request->query->get('action') . "&project=" . $request->query->get('project') . "&#" . $block1->form . "Anchor",
    null, $csrfHandler);

if ($action == "new") {
    $block1->heading($strings["new_requests"]);
} elseif ($action == "open") {
    $block1->heading($strings["open_requests"]);
} elseif ($action == "complete") {
    $block1->heading($strings["closed_requests"]);
}

if ($teamMember == "true" || $session->get("profile") == "0") {
    $block1->openPaletteIcon();
    $block1->paletteIcon(1, "edit", $strings["edit_status"]);
    $block1->paletteIcon(2, "remove", $strings["delete"]);
    $block1->paletteIcon(3, "info", $strings["view"]);
    $block1->closePaletteIcon();
}
$block1->sorting("support_requests", $sortingUser["support_requests"], "sr.id ASC", $sortingFields = array(
    0 => "sr.id",
    1 => "sr.subject",
    2 => "sr.member",
    3 => "sr.project",
    4 => "sr.priority",
    5 => "sr.status",
    6 => "sr.date_open",
    7 => "sr.date_close"
));

if ($supportType == "team") {
    if ($action == "new") {
        $listRequests = $support->getSupportRequestByStatusAndProjectId(0, $project, $block1->sortingValue);
    } elseif ($action == "open") {
        $listRequests = $support->getSupportRequestByStatusAndProjectId(1, $project, $block1->sortingValue);
    } elseif ($action == "complete") {
        $listRequests = $support->getSupportRequestByStatusAndProjectId(2, $project, $block1->sortingValue);
    }
} elseif ($supportType == "admin") {
    if ($request->query->get('action') == "new") {
        $listRequests = $support->getSupportRequestByStatus(0, $block1->sortingValue);
    } elseif ($request->query->get('action') == "open") {
        $listRequests = $support->getSupportRequestByStatus(1, $block1->sortingValue);
    } elseif ($request->query->get('action') == "complete") {
        $listRequests = $support->getSupportRequestByStatus(2, $block1->sortingValue);
    }
}

if ($listRequests) {
    $block1->openResults();
    $block1->labels($labels = array(
        0 => $strings["id"],
        1 => $strings["subject"],
        2 => $strings["owner"],
        3 => $strings["project"],
        4 => $strings["priority"],
        5 => $strings["status"],
        6 => $strings["date_open"],
        7 => $strings["date_close"]
    ), "false");

    foreach ($listRequests as $listItem) {
        $block1->openRow();
        $block1->checkboxRow($listItem["sr_id"]);
        $block1->cellRow($listItem["sr_id"]);
        $block1->cellRow($blockPage->buildLink("../support/viewrequest.php?id=" . $listItem["sr_id"],
            $listItem["sr_subject"], "in"));
        $block1->cellRow($listItem["sr_mem_name"]);
        $block1->cellRow($listItem["sr_pro_name"]);
        $block1->cellRow($priority[$listItem["sr_priority"]]);
        $block1->cellRow($requestStatus[$listItem["sr_status"]]);
        $block1->cellRow($listItem["sr_date_open"]);
        $block1->cellRow($listItem["sr_date_close"]);
        $block1->closeRow();
    }
    $block1->closeResults();
} else {
    $block1->noresults();
}
$block1->closeFormResults();
if ($teamMember == "true" || $session->get("profile") == "0") {
    $block1->openPaletteScript();
    $block1->paletteScript(1, "edit", "../support/addpost.php?action=status", "false,true,false",
        $strings["edit_status"]);
    $block1->paletteScript(2, "remove",
        "../support/deleterequests.php?sendto=" . $request->query->get('action') . "&action=deleteR", "false,true,true",
        $strings["delete"]);
    $block1->paletteScript(3, "info", "../support/viewrequest.php?", "false,true,false", $strings["view"]);
    $block1->closePaletteScript(count($listRequests), array_column($listRequests, 'sr_id'));
}

include APP_ROOT . '/themes/' . THEME . '/footer.php';
