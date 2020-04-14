<?php
#Application name: PhpCollab
#Status page: 0

use phpCollab\Members\Members;
use phpCollab\Support\Support;

$checkSession = "true";
include '../includes/library.php';

$members = new Members();
$support = new Support();

$userDetail = $members->getMemberById($idSession);

$project = $request->query->get('project');
$priority = $GLOBALS["priority"];

if ($action == "add") {
    $request_priority = $_POST["priority"];
    $subject = $_POST["subject"];
    $message = $_POST["message"];
    $userId = $_POST["userId"];
    $projectId = $_POST["projectId"];

    if (empty($subject) || empty($message)) {
        $errorMessage = "Please enter a subject and message";
    } else {
        $supportRequestId = $support->addSupportRequest($userId, $request_priority, $subject, $message, $projectId);

        if ($notifications == "true") {
            include '../support/noti_newrequest.php';
        }

        phpCollab\Util::headerFunction("suprequestdetail.php?id=$supportRequestId");
    }
}

$bouton[6] = "over";
$titlePage = $strings["support"];
include 'include_header.php';

if (isset($errorMessage) && !empty($errorMessage)) {
    echo <<<ERROR
<div style="margin: 2rem; color: firebrick; font-size: 1rem;">{$errorMessage}</div>
ERROR;
}

echo <<<STARTFORM
<form method="POST" action="../projects_site/addsupport.php?action=add&project={$projectSession}#filedetailsAnchor" name="addsupport">
STARTFORM;

echo <<<TABLE
<table style="width: 90%" class="nonStriped">
    <tr><th colspan="2">{$strings["add_support_request"]}</th></tr>
    <tr>
        <th>{$strings["priority"]} :</th>
        <td><select name="priority">
TABLE;

foreach ($priority as $key => $param) {
    if ($key != 0) {
        echo '<option value="' . $key . '">' . $param . '</option>';
    }
}

echo <<<CLOSETABLE
        </select></td>
    </tr>
    <tr>
        <th>{$strings["subject"]}</th>
        <td><input size="32" value="{$subject}" style="width: 250px" name="subject" maxlength="64" type="text"></td>
    </tr>
    <tr>
        <th>{$strings["message"]}</th>
        <td><textarea rows="3" style="width: 400px; height: 200px;" name="message" cols="43">{$message}</textarea></td>
    </tr>
    <tr>
        <th>&nbsp;</th>
        <td>
            <input type="submit" value="{$strings["submit"]}">
            <input type="hidden" name="userId" value="{$idSession}">
            <input type="hidden" name="projectId" value="{$project}">
        </td>
    </tr>
</table>
</form>
CLOSETABLE;


include("include_footer.php");
