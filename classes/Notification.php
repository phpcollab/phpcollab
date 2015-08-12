<?php 
/*
** Application name: phpCollab
** Last Edit page: 26/01/2004 
** Path by root: ../includes/notification.class.php
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
** FILE: notification.class.php
**
** DESC: Screen: notification class
**
** HISTORY:
** 	26/01/2004	-	added file comment
**	03/08/2005	-	added language file for phpMailer
** -----------------------------------------------------------------------------
** TO-DO:
** 
**
** =============================================================================
*/

define('APP_ROOT', dirname(dirname(__FILE__)));

require( APP_ROOT . "/includes/phpmailer/class.phpmailer.php");

class notification extends phpmailer 
{

	function Notification()
	{
		global $strings,$root,$notificationMethod,$lang;

		if (file_exists( APP_ROOT . "/includes/phpmailer/language/phpmailer.lang-$lang.php"))
		{
			$phpmailer_lang = $lang;
		}
		else {
			$phpmailer_lang = 'en';
		}


		$this->Mailer = $notificationMethod;
		$this->PluginDir = APP_ROOT . "/includes/phpmailer/";
		$this->SetLanguage($phpmailer_lang, APP_ROOT . "/includes/phpmailer/language/");

		if ($this->Mailer == "smtp") 
		{
			$this->Host = SMTPSERVER;
			$this->Username = SMTPLOGIN;
			$this->Password = SMTPPASSWORD;
		}

		$this->footer = "--\n".$strings["noti_foot1"]."\n\n".$strings["noti_foot2"]."\n$root/";
	}

	function getUserinfo($idUser,$type) 
	{
		$tmpquery = "WHERE mem.id = '$idUser'";
		$detailUser = new request();
		$detailUser->openMembers($tmpquery);

		if ($type == "from") 
		{
			$this->From     = $detailUser->mem_email_work[0];
			$this->FromName = $detailUser->mem_name[0];
		}
		
		if ($type == "to") 
		{
			$this->AddAddress($detailUser->mem_email_work[0], $detailUser->mem_name[0]);
		}
	}

}


?>