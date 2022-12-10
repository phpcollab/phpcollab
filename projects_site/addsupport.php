<?php

use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

$checkSession = "true";
require_once '../includes/library.php';

try {
    $support = $container->getSupportLoader();
    $teams = $container->getTeams();

    $userDetail = $members->getMemberById($session->get("id"));

    $project = $request->query->get('project');
    $priority = $GLOBALS["priority"];

    if ($request->isMethod('post')) {
        try {
            if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
                if ($request->query->get("action") == "add") {
                    $request_priority = $request->request->get('priority');
                    $subject = $request->request->get('subject');
                    $message = $request->request->get('message');
                    $userId = $request->request->get('userId');
                    $projectId = $request->request->get('projectId');

                    if (empty($subject) || empty($message)) {
                        $errorMessage = "Please enter a subject and message";
                    } else {
                        $supportRequestDetail = $support->addSupportRequest($userId, $request_priority, $subject, $message,
                            $projectId);

                        if ($supportRequestDetail) {
                            $supportRequestDetail["requestor_name"] = $session->get("name");
                            $supportRequestDetail["requestor_email"] = $session->get("email");

                            if ($notifications == "true") {
                                if ($supportType == "team") {

                                    $listTeam = $teams->getTeamByProjectId($supportRequestDetail["sr_project"]);

                                    // Is the email different between Team and non-team?
                                    foreach ($listTeam as $teamMember) {
                                        // Check to make sure there is an email address
                                        if (!empty($teamMember["tea_mem_email_work"])) {

                                            $teamMember["mem_name"] = $teamMember["tea_mem_name"];
                                            $teamMember["mem_email_work"] = $teamMember["tea_mem_email_work"];

                                            $support->sendNewRequestNotification(
                                                $supportRequestDetail,
                                                $teamMember,
                                                $strings["support"] . " " . $strings["support_id"] . ": " . $supportRequestDetail["sr_id"],
                                                // If the team member matches the current user, then set "opener" text to message and subject,
                                                // otherwise set it to the team message with the project name
                                                ($session->get("id") == $teamMember["tea_mem_id"] ) ?
                                                    $strings["noti_support_request_new2"] . " " . $supportRequestDetail["sr_subject"]
                                                    :
                                                    $strings["noti_support_team_new2"] . " " . $supportRequestDetail["sr_pro_name"]
                                            );
                                        }
                                    }
                                } else {
                                    $userDetail = $members->getMemberById(1);

                                    if ($userDetail["mem_email_work"] != "") {
                                        $support->sendNewRequestNotification(
                                            $supportRequestDetail,
                                            $userDetail,
                                            $strings["support"] . " " . $strings["support_id"] . ": " . $supportRequestDetail["sr_id"],
                                            $strings["support_request_new"]
                                        );
                                    }
                                }
                            }

                            phpCollab\Util::headerFunction("suprequestdetail.php?id=" . $supportRequestDetail["sr_id"]);
                        }

                        phpCollab\Util::headerFunction("showallsupport.php?project=" . $projectId);
                    }
                }
            }
        } catch (InvalidCsrfTokenException $csrfTokenException) {
            $logger->error('CSRF Token Error', [
                'Project Site: Add support',
                '$_SERVER["REMOTE_ADDR"]' => $request->server->get("REMOTE_ADDR"),
                '$_SERVER["HTTP_X_FORWARDED_FOR"]'=> $request->server->get('HTTP_X_FORWARDED_FOR')
            ]);
        } catch (Exception $exception) {
            $logger->critical('Exception', ['Error' => $exception->getMessage()]);
            $msg = 'permissiondenied';
            throw $exception;
        }
    }

    $setTitle .= " : " . $strings["add_support_request"];

    $bouton[6] = "over";

    $titlePage = $strings["support"];

    include 'include_header.php';

    if (isset($errorMessage) && !empty($errorMessage)) {
        echo <<<ERROR
    <div style="margin: 2rem; color: firebrick; font-size: 1rem;">$errorMessage</div>
ERROR;
    }

    echo <<<HTML
    <form method="POST" action="../projects_site/addsupport.php?action=add&project={$session->get("project")}#filedetailsAnchor" name="addsupport">
        <input type="hidden" name="csrf_token" value="{$csrfHandler->getToken()}" />
        <table style="width: 90%" class="nonStriped">
            <tr><th colspan="2">{$strings["add_support_request"]}</th></tr>
            <tr>
                <th>{$strings["priority"]} :</th>
                <td><select name="priority">
HTML;

    foreach ($priority as $key => $param) {
        if ($key != 0) {
            echo '<option value="' . $key . '">' . $param . '</option>';
        }
    }

    echo <<<HTML
            </select></td>
        </tr>
        <tr>
            <th>{$strings["subject"]}</th>
            <td><input size="32" value="{$escaper->escapeHtml($subject ?? "")}" style="width: 250px" name="subject" maxlength="64" type="text"></td>
        </tr>
        <tr>
            <th>{$strings["message"]}</th>
            <td><textarea rows="3" style="width: 400px; height: 200px;" name="message" cols="43">{$escaper->escapeHtml($message ?? "")}</textarea></td>
        </tr>
        <tr>
            <th>&nbsp;</th>
            <td>
                <input type="submit" value="{$strings["submit"]}">
                <input type="hidden" name="userId" value="{$session->get("id")}">
                <input type="hidden" name="projectId" value="$project">
            </td>
        </tr>
    </table>
    </form>
HTML;


    include("include_footer.php");
} catch (Exception $exception) {
    $logger->error('Exception', ['Error' => $exception->getMessage()]);
}