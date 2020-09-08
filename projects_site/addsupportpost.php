<?php
#Application name: PhpCollab
#Status page: 0

use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

$checkSession = "true";
include '../includes/library.php';

$support = $container->getSupportLoader();

$tmpquery = "WHERE sr.id = '$id'";

$requestDetail = $support->getSupportRequestById($id);

if ($requestDetail["sr_project"] != $session->get("project") || $requestDetail["sr_member"] != $session->get("id")) {
    phpCollab\Util::headerFunction("index.php");
}

if ($request->isMethod('post')) {
    try {
        if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
            if ($request->query->get('action') == "add") {
                $message = phpCollab\Util::convertData($request->request->get('response_message'));

                if (!empty($message)) {
                    $newPostId = $support->addSupportPost($id, $message, $dateheure, $session->get("id"),
                        $requestDetail["sr_project"]);

                    if ($notifications == "true") {
                        // Gather additional information for the notification
                        $postDetail = $support->getSupportPostById($newPostId);
                        $requestDetail = $support->getSupportRequestById($postDetail["sp_request_id"]);
                        $userDetail = $members->getMemberById($requestDetail["sr_member"]);

                        try {
                            $support->sendNewPostNotification($requestDetail, $postDetail, $userDetail);
                        } catch (Exception $e) {
                            $logger->error('Project Site (add support post)', ['Exception message', $e->getMessage()]);
                            $error = $strings["action_not_allowed"];
                        }
                    }
                    phpCollab\Util::headerFunction("suprequestdetail.php?id=$id");
                } else {
                    $error = "The message can not be blank.  Please enter a message.";
                }
            }
        }
    } catch (InvalidCsrfTokenException $csrfTokenException) {
        $logger->critical('CSRF Token Error', [
            'Project Site: Add support post',
            '$_SERVER["REMOTE_ADDR"]' => $_SERVER['REMOTE_ADDR'],
            '$_SERVER["HTTP_X_FORWARDED_FOR"]' => $_SERVER['HTTP_X_FORWARDED_FOR']
        ]);
    } catch (Exception $e) {
        $logger->critical('Exception', ['Error' => $e->getMessage()]);
        $msg = 'permissiondenied';
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
    action="../projects_site/addsupportpost.php?id={$id}&action=add&project={$session->get("project")}#filedetailsAnchor" 
    name="addsupport" 
    enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="{$csrfHandler->getToken()}" />
<table style="width: 90%; margin-bottom: 3em;">
    <tr>
        <th colspan="2" style="text-align: left; padding-bottom: 2em; font-size: 1rem;">{$strings["add_support_response"]}</th>
    </tr>
    <tr>
        <th style="vertical-align: top"><label for="response_message" >{$strings["message"]}</label></th>
        <td><textarea required rows="3" style="width: 400px; height: 200px;" name="response_message" id="response_message" cols="43">{$request->request->get('response_message')}</textarea></td>
    </tr>
    <tr>
        <th>&nbsp;</th>
        <td><input type="SUBMIT" value="{$strings["submit"]}"></td>
    </tr>
</table>
    <input type="hidden" name="user" value="{$session->get("id")}">
</form>
FORM;


include("include_footer.php");
