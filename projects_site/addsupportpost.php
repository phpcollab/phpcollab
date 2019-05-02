<?php
#Application name: PhpCollab
#Status page: 0

use phpCollab\Members\Members;
use phpCollab\Support\Support;

$checkSession = "true";
include '../includes/library.php';

$support = new Support();
$members = new Members();

$tmpquery = "WHERE sr.id = '$id'";

$requestDetail = $support->getSupportRequestById($id);

if ($requestDetail["sr_project"] != $projectSession || $requestDetail["sr_member"] != $idSession) {
    phpCollab\Util::headerFunction("index.php");
}
if ($_GET["action"] == "add") {
    $message = phpCollab\Util::convertData($_POST["response_message"]);

    if (!empty($message)) {
        $newPostId = $support->addSupportPost($id, $message, $dateheure, $idSession, $requestDetail["sr_project"]);

        if ($notifications == "true") {
            // Gather additional information for the notification
            $postDetail = $support->getSupportPostById($newPostId);
            $requestDetail = $support->getSupportRequestById($postDetail["sp_request_id"]);
            $userDetail = $members->getMemberById($requestDetail["sr_member"]);

            try {
                $support->sendNewPostNotification($requestDetail, $postDetail, $userDetail);
            }
            catch (Exception $e) {
                echo $e->getMessage();
            }
        }
        phpCollab\Util::headerFunction("suprequestdetail.php?id=$id");
    } else {
        $error = "The message can not be blank.  Please enter a message.";
    }
}


$bouton[6] = "over";
$titlePage = $strings["support"];
include 'include_header.php';

if (!empty($error)) {
    echo <<<ERROR
<div style="color: darkred; padding: 1em;">{$error}</div>
ERROR;

}

echo <<<FORM
<form accept-charset="UNKNOWN" 
    method="POST" 
    action="../projects_site/addsupportpost.php?id={$id}&action=add&project={$projectSession}#filedetailsAnchor" 
    name="addsupport" 
    enctype="multipart/form-data">

<table style="width: 90%; margin-bottom: 3em;">
    <tr>
        <th colspan="2" style="text-align: left; padding-bottom: 2em; font-size: 1rem;">{$strings["add_support_response"]}</th>
    </tr>
    <tr>
        <th style="vertical-align: top"><label for="response_message" >{$strings["message"]}</label></th>
        <td><textarea required rows="3" style="width: 400px; height: 200px;" name="response_message" id="response_message" cols="43">{$_POST["response_message"]}</textarea></td>
    </tr>
    <tr>
        <th>&nbsp;</th>
        <td><input type="SUBMIT" value="{$strings["submit"]}"></td>
    </tr>
</table>
    <input type="hidden" name="user" value="{$idSession}">
</form>
FORM;



include("include_footer.php");
