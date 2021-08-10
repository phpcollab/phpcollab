<?php
#Application name: PhpCollab
#Status page: 0

use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

$checkSession = "true";
require_once '../includes/library.php';

try {
    $files = $container->getFilesLoader();
} catch (Exception $exception) {
    $logger->error('Exception', ['Error' => $exception->getMessage()]);
}

if ($request->isMethod('post')) {
    try {
        if ($csrfHandler->isValid($request->request->get("csrf_token"))) {
            if ($request->request->get('action') == "update") {
                $commentField = phpCollab\Util::convertData($request->request->get('commentField'));

                try {
                    $files->updateApprovalTracking($session->get("id"), $commentField, $id,
                        $request->request->get('statusField'));
                    $msg = "updateFile";

                    phpCollab\Util::headerFunction("doclists.php?msg=$msg");
                } catch (Exception $e) {
                    $logger->error('Project Site (file approval)', ['Exception message', $e->getMessage()]);
                    $error = $strings["action_not_allowed"];
                }
            }
        }
    } catch (InvalidCsrfTokenException $csrfTokenException) {
        $logger->error('CSRF Token Error', [
            'Project Site: Document approval',
            '$_SERVER["REMOTE_ADDR"]' => $_SERVER['REMOTE_ADDR'],
            '$_SERVER["HTTP_X_FORWARDED_FOR"]' => $_SERVER['HTTP_X_FORWARDED_FOR']
        ]);
    } catch (Exception $e) {
        $logger->critical('Exception', ['Error' => $e->getMessage()]);
        $msg = 'permissiondenied';
    }
}

$fileDetail = $files->getFileById($request->query->get('id'));

if ($fileDetail["fil_published"] == "1" || $fileDetail["fil_project"] != $session->get("project")) {
    phpCollab\Util::headerFunction("index.php");
}

$bouton[4] = "over";
$titlePage = $strings["approval_tracking"];
include 'include_header.php';

echo <<<FORM
<form method="post" action="../projects_site/docitemapproval.php?action=update" name="documentitemapproval">
    <input type="hidden" name="csrf_token" value="{$csrfHandler->getToken()}" />
    <table style="width: 90%" class="nonStriped">
        <tr>
            <th colspan="2">{$strings["approval_tracking"]} :</th>
        </tr>
        <tr>
            <th>{$strings["document"]} :</th>
            <td><a href="clientfiledetail.php?id={$fileDetail["fil_id"]}">{$fileDetail["fil_name"]}</a></td>
        </tr>
        <tr>
            <th>{$strings["status"]} :</th>
            <td><select name="statusField">
FORM;
$comptSta = count($statusFile);

for ($i = 0; $i < $comptSta; $i++) {
    if ($fileDetail["fil_status"] == $i) {
        echo <<<OPTION
                <option value="$i" selected>$statusFile[$i]</option>
OPTION;
    } else {
        echo <<<OPTION
                <option value="$i">$statusFile[$i]</option>
OPTION;
    }
}
echo <<<CLOSE_FORM
                </select></td>
        </tr>
        <tr>
            <th>{$strings["comments"]} :</th>
            <td><textarea rows="3" name="commentField" cols="43">{$fileDetail["fil_comments_approval"]}</textarea></td>
        </tr>
        <tr>
            <th>&nbsp;</th>
            <td><input name="submit" type="submit" value="{$strings["save"]}"></td>
        </tr>
</table>
<input name="id" type="hidden" value="$id">
<input name="action" type="hidden" value="update">
</form>
CLOSE_FORM;

include("include_footer.php");
