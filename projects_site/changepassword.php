<?php
/*
** Application name: phpCollab
** Last Edit page: 05/11/2004
** Path by root: ../project_site/changepassword.php
** Authors: Ceam / Fullo
**
** =============================================================================
**
**               phpCollab - Project Managment 
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: changepassword.php
**
** DESC: 
**
** HISTORY:
**  23/03/2004  -   added new document info
**  24/03/2004  -   fixed session problem
**  24/03/2004  -   xhtml code
**  05/11/2004  -   fixed bug 837027 
** -----------------------------------------------------------------------------
** TO-DO:
** 
**
** =============================================================================
*/


$checkSession = "true";
include("../includes/library.php");

if ($enable_cvs == "true") {
    include("../includes/cvslib.php");
}

if ($action == "update") {
    $r = substr($opw, 0, 2); 
    $opw = crypt($opw, $r);
    
    if ($opw != $passwordSession) {
        $error = $strings["old_password_error"];
    } else {
        
        if ($npw != $pwa || $npw == "") 
        {
            $error = $strings["new_password_error"];
        } else {
            $cnpw = Util::getPassword($npw);

            if ($htaccessAuth == "true") {
                $Htpasswd = new Htpasswd;
                $tmpquery = "WHERE tea.member = '$idSession'";
                $listProjects = new request();
                $listProjects->openTeams($tmpquery);
                $comptListProjects = count($listProjects->tea_id);

                if ($comptListProjects != "0") 
                {
                    
                    for ($i=0;$i<$comptListProjects;$i++) 
                    {
                        $Htpasswd->initialize("files/".$listProjects->tea_pro_id[$i]."/.htpasswd");
                        $Htpasswd->changePass($loginSession,$cnpw);
                    }
                }
            }

            $tmpquery = "UPDATE ".$tableCollab["members"]." SET password='$cnpw' WHERE id = '$idSession'";
            Util::connectSql("$tmpquery");

            //if CVS repository enabled
            if ($enable_cvs == "true") 
            {
                $query = "WHERE tea.member = '$idSession'";
                $cvsMembers = new request();
                $cvsMembers->openTeams($query);

            //change the password in every repository
                for ($i=0;$i<(count($cvsMembers->tea_id));$i++) {
                    cvs_change_password($cvsMembers->tea_mem_login[$i], $cnpw, $cvsMembers->tea_pro_id[$i]);
                }
            }

            $r = substr($npw, 0, 2); 
            $npw = crypt($npw, $r);
            $passwordSession = $npw;

            $_SESSION['passwordSession'] = $passwordSession;

            Util::headerFunction("changepassword.php?msg=update&".session_name()."=".session_id());
            exit;
        }
    }
}

$tmpquery = "WHERE mem.id = '$idSession'";
$userDetail = new request();
$userDetail->openMembers($tmpquery);
$comptUserDetail = count($userDetail->mem_id);

if ($comptUserDetail == "0") {
    Util::headerFunction("userlist.php?msg=blankUser&".session_name()."=".session_id());
    exit;
}

$titlePage = $strings["change_password"];
include ("include_header.php");

if ($msg != "") {
    include('../includes/messages.php');
    $blockPage = new block();
    $blockPage->messagebox($msgLabel);
}

echo "  <form accept-charset='UNKNOWN' method='POST' action='../projects_site/changepassword.php?".session_name()."=".session_id()."&action=update' name='changepassword' enctype='application/x-www-form-urlencoded'>
            <table cellspacing='0' width='90%' border='0' cellpadding='3'>
            <tr>
                <th colspan='2'>".$strings["change_password"]."</th>
            </tr>
            <tr>
                <th>*&nbsp;".$strings["old_password"]." :</th>
                <td><input style='width: 150px;' type='password' name='opw' value=''></td>
            </tr>
            <tr>
                <th>*&nbsp;".$strings["new_password"]." :</th>
                <td><input style='width: 150px;' type='password' name='npw' value=''></td>
            </tr>
            <tr>
                <th>*&nbsp;".$strings["confirm_password"]." :</th>
                <td><input style='width: 150px;' type='password' name='pwa' value=''></td>
            </tr>
            <tr>
                <th>&nbsp;</th>
                <td colspan='2'><input name='submit' type='submit' value='".$strings["save"]."'><br/><br/>$error</td>
            </tr>
            </table>
        </form>
     ";

include ("include_footer.php");
?>