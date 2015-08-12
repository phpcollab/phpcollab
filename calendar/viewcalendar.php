<?php
/*
** Application name: phpCollab
** Last Edit page: 04/12/2004
** Path by root: ../calendar/viewcalendar.php
** Authors: Ceam / Fullo
** =============================================================================
**
**               phpCollab - Project Managment
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: viewcalendar.php
**
** DESC: screen: view main calendar page
**
** HISTORY:
**  2003-10-23  -   added new document info
**  15/09/2004  -   added broadcast support and subtask view
**  10/11/2004  -   fixed http://www.php-collab.org/community/viewtopic.php?t=1697
**  04/12/2004  -   fixed [ 1077236 ] Calendar bug in Client's Project site
**  21/04/2005  -   added css to calendar events
**  25/04/2006  -   replaced JavaScript Calendar functions with new DHTML calendar files.
** -----------------------------------------------------------------------------
** TO-DO:
**  add the iCal format import
**  add vCalendar format import
**  add more view (weekly, daily)
**  check for better calendar engine (PHPiCal ?)
** =============================================================================
*/


$checkSession = "true";
include_once('../includes/library.php');

if ($type == "")
{
    $type = "monthPreview";
}

function _dayOfWeek($timestamp)
{
    return intval(strftime("%w",$timestamp)+1);
}

if ($gmtTimezone != "false")
{
    $zone = 3600 * $_SESSION['timezoneSession'];
    $year = gmdate("Y", time() + $zone);
    $month = gmdate("n", time() + $zone);
    $day = gmdate("j", time() + $zone);
} else {
    $year = date("Y");
    $month = date("n");
    $day = date("j");
}

if (strlen($month) == 1)
{
    $month = "0$month";
}
if (strlen($day) == 1)
{
    $day= "0$day";
}

$dateToday = "$year-$month-$day";

if ($dateCalend != "")
{
    $year = substr("$dateCalend", 0, 4);
    $month = substr("$dateCalend", 5, 2);
    $day = substr("$dateCalend", 8, 2);
}

if ($dateCalend == "")
{
    
    if ($gmtTimezone != "false")
    {
      $zone = 3600 * $_SESSION['timezoneSession'];
      $year = gmdate("Y", time() + $zone);
      $month = gmdate("n", time() + $zone);
      $day = gmdate("d", time() + $zone);
    } else {
      $year = date("Y");
      $month = date("n");
      $day = date("d");
    }

    if (strlen($day) == 1)
    {
        $day = "0$day";
    }

    if (strlen($month) == 1)
    {
        $month = "0$month";
    }

    $dateCalend = "$year-$month-$day";
}

$yearDay = date("Y");
$monthDay = date("n");
$dayDay = date("d");

$dayName = date("w",mktime(0,0,0,$month,$day,$year));
$monthName = date("n",mktime(0,0,0,$month,$day,$year));
$dayName = $dayNameArray[$dayName];
$monthName = $monthNameArray[$monthName];

$daysmonth = date("t",mktime(0,0,0,$month,$day,$year));
$firstday = date("w",mktime(0,0,0,$month,1,$year));
$padmonth = date("m",mktime(0,0,0,$month,$day,$year));
$padday = date("d",mktime(0,0,0,$month,$day,$year));

if ($firstday == 0)
{
    $firstday = 7;
}

echo "<!-- DAB - Type: $type";

if ($type == "calendEdit") {
    if ($action == "update")
    {
        if ($recurring == "")
        {
            $recurring = "0";
        }
        else
        {
            $dateStart_A = substr("$dateStart", 0, 4);
            $dateStart_M = substr("$dateStart", 5, 2);
            $dateStart_J = substr("$dateStart", 8, 2);
            $dayRecurr = _dayOfWeek(mktime(12,12,12,$dateStart_M,$dateStart_J,$dateStart_A));
        }
        $subject = Util::convertData($subject);
        $description = Util::convertData($description);
        $tmpquery = "UPDATE ".$tableCollab["calendar"]." SET subject='$subject',description='$description',location='$location',shortname='$shortname',date_start='$dateStart',date_end='$dateEnd',time_start='$time_start',time_end='$time_end',reminder='$reminder',recurring='$recurring',recur_day='$dayRecurr',broadcast='$broadcast' WHERE id = '$dateEnreg'";
        Util::connectSql("$tmpquery");
        Util::headerFunction("../calendar/viewcalendar.php?dateEnreg=$dateEnreg&dateCalend=$dateCalend&type=calendDetail&msg=update&".session_name()."=".session_id());
    }

    if ($action == "add")
    {
        if($shortname == "")
        {
            $error = $strings["blank_fields"];
        }
        else
        {
            if ($recurring == "")
            {
                $recurring = "0";
            }
            else
            {
                $dateStart_A = substr("$dateStart", 0, 4);
                $dateStart_M = substr("$dateStart", 5, 2);
                $dateStart_J = substr("$dateStart", 8, 2);
                $dayRecurr = _dayOfWeek(mktime(12,12,12,$dateStart_M,$dateStart_J,$dateStart_A));
            }

            $subject = Util::convertData($subject);
            $description = Util::convertData($description);
            $shortname = Util::convertData($shortname);
            $tmpquery = "INSERT INTO ".$tableCollab["calendar"]."(owner,subject,description,location,shortname,date_start,date_end,time_start,time_end,reminder,broadcast,recurring,recur_day) VALUES('$idSession','$subject','$description','$location','$shortname','$dateStart','$dateEnd','$time_start','$time_end','$reminder','$broadcast','$recurring','$dayRecurr')";
            Util::connectSql("$tmpquery");
            $tmpquery = $tableCollab["calendar"];
            Util::getLastId($tmpquery);
            $num = $lastId[0];
            unset($lastId);
            Util::headerFunction("../calendar/viewcalendar.php?dateEnreg=$num&dateCalend=$dateCalend&type=calendDetail&msg=add&".session_name()."=".session_id());
        }
    }
}

if ($type == "calendEdit")
{
    if ($dateEnreg == "" && $id != "")
    {
        $dateEnreg = $id;
    }

    if ($id != "")
    {
        $tmpquery = "WHERE cal.owner = '$idSession' AND cal.id = '$dateEnreg'";
        $detailCalendar = new Request();
        $detailCalendar->openCalendar($tmpquery);
        $comptDetailCalendar = count($detailCalendar->cal_id);

        if ($comptDetailCalendar == "0")
        {
            Util::headerFunction("../calendar/viewcalendar.php?".session_name()."=".session_id());
        }
    }
}

if ($type == "calendDetail")
{
    if ($dateEnreg == "" && $id != "")
    {
        $dateEnreg = $id;
    }

    $tmpquery = "WHERE (cal.owner = '$idSession' AND cal.id = '$dateEnreg') OR (cal.broadcast = '1' AND cal.id = '$dateEnreg')";  //changed to $idSession
    $detailCalendar = new Request();
    $detailCalendar->openCalendar($tmpquery);
    $comptDetailCalendar = count($detailCalendar->cal_id);

    if ($comptDetailCalendar == "0")
    {
        Util::headerFunction("../calendar/viewcalendar.php?".session_name()."=".session_id());
    }
}

if ($type == "calendEdit")
{
    $bodyCommand = "onLoad=\"document.calendForm.shortname.focus();\"";
}

/** Do the title calcs here.. we __HAVE__ to do it before the include **/
switch ($type) {
    case 'monthPreview':
        $setTitle .= " : View Calendar ($monthName $year)";
        break;
    case 'dayList':
        $setTitle .= " : View Calendar ($dateCalend)";
        break;
    case 'calendEdit':
        if ($id == "") 
            $setTitle .= " : Add Calendar Entry ($dateCalend)";
        if ($id != "") 
            $setTitle .= " : Edit Calendar Entry ($dateCalend - " . $detailCalendar->cal_shortname[0] . ")";
        break;
    case 'calendDetail':
        $setTitle .= " : Calendar Entry (" . $detailCalendar->cal_shortname[0] . ")";
        break;
}
$includeCalendar = true; //Include Javascript files for the pop-up calendar 
include('../themes/'.THEME.'/header.php');

if ($type == "calendEdit")
{
    if ($id != "")
    {
        $subject = $detailCalendar->cal_subject[0];
        $description = $detailCalendar->cal_description[0];
        $location = $detailCalendar->cal_location[0];
        $shortname = $detailCalendar->cal_shortname[0];
        $date_start = $detailCalendar->cal_date_start[0];
        $date_end = $detailCalendar->cal_date_end[0];
        $time_start = $detailCalendar->cal_time_start[0];
        $time_end = $detailCalendar->cal_time_end[0];
        $reminder = $detailCalendar->cal_reminder[0];
        $broadcast = $detailCalendar->cal_broadcast[0];
        $recurring = $detailCalendar->cal_recurring[0];

        if ($reminder == "0")
        {
            $checked1_b = "checked"; //false
        } else {
            $checked2_b = "checked"; //true
        }

        if ($broadcast == "0")
        {
            $checked3_b = "checked"; //false
        }
        else
        {
            $checked4_b = "checked"; //true
        }
        if ($recurring == "1")
        {
            $checked2_a = "checked"; //true
        }
    }
    else
    {
        $checked2_b = "checked"; //true
        $checked3_b = "checked";
        $checked2_a = "";
    }

    $blockPage = new Block();
    $blockPage->openBreadcrumbs();
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../calendar/viewcalendar.php?type=monthPreview",$strings["calendar"],in));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../calendar/viewcalendar.php?type=monthPreview&dateCalend=$dateCalend","$monthName $year",in));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../calendar/viewcalendar.php?type=dayList&dateCalend=$dateCalend","$dayName $day $monthName $year",in));

    if ($id != "")
    {
        $blockPage->itemBreadcrumbs($blockPage->buildLink("../calendar/viewcalendar.php?type=calendDetail&dateCalend=$dateCalend&dateEnreg=$dateEnreg",$detailCalendar->cal_shortname[0],in));
        $blockPage->itemBreadcrumbs($strings["edit"]);
    }
    else
    {
        $blockPage->itemBreadcrumbs($strings["add"]);
    }
    $blockPage->closeBreadcrumbs();

    if ($msg != "")
    {
        include('../includes/messages.php');
        $blockPage->messagebox($msgLabel);
    }

    $block1 = new Block();

    $block1->form = "calend";

    if ($id != "")
    {
        $block1->openForm("../calendar/viewcalendar.php?".session_name()."=".session_id()."&dateEnreg=$dateEnreg&dateCalend=$dateCalend&type=$type&action=update#".$block1->form."Anchor");
    }
    else
    {
        $block1->openForm("../calendar/viewcalendar.php?".session_name()."=".session_id()."&dateEnreg=$dateEnreg&dateCalend=$dateCalend&type=$type&action=add#".$block1->form."Anchor");
    }

    if ($error != "")
    {
        $block1->headingError($strings["errors"]);
        $block1->contentError($error);
    }

    if ($id != "")
    {
        $block1->heading($strings["edit"].": ".$detailCalendar->cal_shortname[0]);
    }
    else
    {
        $block1->heading($strings["add"].":");
    }

    $block1->openContent();
    $block1->contentTitle($strings["details"]);

    echo "
        <tr class='odd'>
            <td valign='top' class='leftvalue'>* ".$strings["shortname"].$block1->printHelp("calendar_shortname")." :</td>
            <td><input size='24' style='width: 250px;' maxlength='128' type='text' name='shortname' value='$shortname'></td>
        </tr>
        <tr class='odd'>
            <td valign='top' class='leftvalue'>".$strings["subject"]." :</td>
            <td><input size='24' style='width: 250px;' maxlength='128' type='text' name='subject' value='$subject'></td>
        </tr>
        <tr class='odd'>
            <td valign='top' class='leftvalue'>".$strings["description"]." :</td>
            <td><textarea style='width: 400px; height: 50px;' name='description' cols='35' rows='2'>$description</textarea></td>
        </tr>
        <tr class='odd'>
            <td valign='top' class='leftvalue'>".$strings["location"]." :</td>
            <td><input size='24' style='width: 250px;' maxlength='128' type='text' name='location' value='$location'></td>
        </tr>";

    if ($date_start == "")
    {
        $date_start = $dateCalend;
    }
    if ($date_end == "")
    {
        $date_end = $dateCalend;
    }

    $block1->contentRow($strings["date_start"],"<input type='text' name='dateStart' id='dateStart' size='20' value='$date_start'><input type='button' value=' ... ' id=\"trigDateStart\">");
	echo "
	<script type='text/javascript'>
	    Calendar.setup({
        	inputField     :    'dateStart',
        	button         :    'trigDateStart',
        	$calendar_common_settings
	    });
	</script>
	";
    $block1->contentRow($strings["date_end"],"<input type='text' name='dateEnd' id='dateEnd' size='20' value='$date_end'><input type='button' value=' ... ' id=\"trigDateEnd\">");
	echo "
	<script type='text/javascript'>
	    Calendar.setup({
        	inputField     :    'dateEnd',
	        button         :    'trigDateEnd',
        	$calendar_common_settings
    	});
	</script>
	";
    echo "      </td>
        </tr>
        <tr class='odd'>
            <td valign='top' class='leftvalue'>".$strings["time_start"]." :</td>
            <td><input size='24' style='width: 250px;' maxlength='128' type='text' name='time_start' value='$time_start'></td>
        </tr>
        <tr class='odd'>
            <td valign='top' class='leftvalue'>".$strings["time_end"]." :</td>
            <td><input size='24' style='width: 250px;' maxlength='128' type='text' name='time_end' value='$time_end'></td>
        </tr>
        <tr class='odd'>
            <td valign='top' class='leftvalue'>".$strings["calendar_reminder"]." :</td>
            <td><input type='radio' name='reminder' value='0' $checked1_b> ".$strings["no"]."&nbsp;<input type='radio' name='reminder' value='1' $checked2_b> ".$strings["yes"]."</td>
        </tr>
        <tr class='odd'>
            <td valign='top' class='leftvalue'>".$strings["calendar_broadcast"]." :</td>
            <td><input type='radio' name='broadcast' value='0' $checked3_b> ".$strings["no"]."&nbsp;<input type='radio' name='broadcast' value='1' $checked4_b> ".$strings["yes"]."</td>
        </tr>
        <tr class='odd'>
            <td valign='top' class='leftvalue'>".$strings["calendar_recurring"]." :</td>
            <td><input type='checkbox' name='recurring' value='1' $checked2_a></td>
        </tr>
        <tr class='odd'>
            <td valign='top' class='leftvalue'>&nbsp;</td>
            <td><input type='SUBMIT' value='".$strings["save"]."'></td>
        </tr>";

    $block1->closeContent();
    $block1->closeForm();
}

if ($type == "calendDetail")
{
    $reminder = $detailCalendar->cal_reminder[0];
    $broadcast = $detailCalendar->cal_broadcast[0];
    $recurring = $detailCalendar->cal_recurring[0];

    if ($reminder == "0")
    {
        $reminder = $strings["no"];
    } else {
        $reminder = $strings["yes"];
    }

    if ($broadcast == "0")
    {
        $broadcast = $strings["no"];
    } else {
        $broadcast = $strings["yes"];
    }

    if ($recurring == "0")
    {
        $recurring = $strings["no"];
    } else {
        $recurring = $strings["yes"];
    }

    $blockPage = new Block();
    $blockPage->openBreadcrumbs();
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../calendar/viewcalendar.php?type=monthPreview",$strings["calendar"],in));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../calendar/viewcalendar.php?type=monthPreview&dateCalend=$dateCalend","$monthName $year",in));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../calendar/viewcalendar.php?type=dayList&dateCalend=$dateCalend","$dayName $day $monthName $year",in));
    $blockPage->itemBreadcrumbs($detailCalendar->cal_shortname[0]);
    $blockPage->closeBreadcrumbs();

    if ($msg != "")
    {
        include('../includes/messages.php');
        $blockPage->messagebox($msgLabel);
    }

    $block1 = new Block();

    $block1->form = "calend";
    $block1->openForm("../calendar/viewcalendar.php?".session_name()."=".session_id()."#".$block1->form."Anchor");

    if ($error != "")
    {
        $block1->headingError($strings["errors"]);
        $block1->contentError($error);
    }

    $block1->heading($detailCalendar->cal_shortname[0]);

    $block1->openPaletteIcon();

    //not sure about this...
    if ($detailCalendar->cal_owner[0] == $idSession)
    {
        $block1->paletteIcon(0,"remove",$strings["delete"]);
        $block1->paletteIcon(1,"edit",$strings["edit"]);
    }
    $block1->paletteIcon(2,"export",$strings["export"]);
    $block1->closePaletteIcon();

    $block1->openContent();
    $block1->contentTitle($strings["details"]);


    echo "
        <tr class='odd'>
            <td valign='top' class='leftvalue'>".$strings["subject"]." :</td>
            <td>".$detailCalendar->cal_subject[0]."</td>
        </tr>
        <tr class='odd'>
            <td valign='top' class='leftvalue'>".$strings["description"]." :</td>
            <td>".nl2br($detailCalendar->cal_description[0])."&nbsp;</td>
        </tr>
        <tr class='odd'>
            <td valign='top' class='leftvalue'>".$strings["shortname"].$block1->printHelp("calendar_shortname")." :</td>
            <td>".$detailCalendar->cal_shortname[0]."&nbsp;</td>
        </tr>
        <tr class='odd'>
            <td valign='top' class='leftvalue'>".$strings["location"]." :</td>
            <td>".$detailCalendar->cal_location[0]."&nbsp;</td>
        </tr>
        <tr class='odd'>
            <td valign='top' class='leftvalue'>".$strings["date_start"]." :</td>
            <td>".$detailCalendar->cal_date_start[0]."</td>
        </tr>
        <tr class='odd'>
            <td valign='top' class='leftvalue'>".$strings["date_end"]." :</td>
            <td>".$detailCalendar->cal_date_end[0]."</td>
        </tr>
        <tr class='odd'>
            <td valign='top' class='leftvalue'>".$strings["time_start"]." :</td>
            <td>".$detailCalendar->cal_time_start[0]."</td>
        </tr>
        <tr class='odd'>
            <td valign='top' class='leftvalue'>".$strings["time_end"]." :</td>
            <td>".$detailCalendar->cal_time_end[0]."</td>
        </tr>
        <tr class='odd'>
            <td valign='top' class='leftvalue'>".$strings["calendar_reminder"]." :</td>
            <td>$reminder</td>
        </tr>
        <tr class='odd'>
            <td valign='top' class='leftvalue'>".$strings["calendar_broadcast"]." :</td>
            <td>$broadcast</td>
        </tr>
        <tr class='odd'
        ><td valign='top' class='leftvalue'>".$strings["calendar_recurring"]." :</td>
            <td>$recurring</td>
        </tr>";

    $block1->closeContent();
    $block1->closeForm();

    $block1->openPaletteScript();
    if ($detailCalendar->cal_owner[0] == $idSession)
    {
        $block1->paletteScript(0,"remove","../calendar/deletecalendar.php?id=$dateEnreg","true,true,true",$strings["delete"]);
        $block1->paletteScript(1,"edit","../calendar/viewcalendar.php?id=$dateEnreg&type=calendEdit&dateCalend=$dateCalend","true,true,true",$strings["edit"]);
    }

    $block1->paletteScript(2,"export","../calendar/exportcalendar.php?id=$dateEnreg","true,true,true",$strings["export"]);
    $block1->closePaletteScript("","");
}

$blockPage = new Block();

if ($type == "dayList")
{
    $blockPage->openBreadcrumbs();
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../calendar/viewcalendar.php?type=monthPreview",$strings["calendar"],in));
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../calendar/viewcalendar.php?type=monthPreview&dateCalend=$dateCalend","$monthName $year",in));
    $blockPage->itemBreadcrumbs("$dayName $day $monthName $year");
    $blockPage->closeBreadcrumbs();

    $block1 = new Block();

    $block1->form = "calendList";
    $block1->openForm("../calendar/viewcalendar.php?type=$type&dateCalend=$dateCalend&".session_name()."=".session_id()."#".$block1->form."Anchor");

    $block1->heading("$dayName $day $monthName $year");

    $block1->openPaletteIcon();

    $block1->paletteIcon(0,"add",$strings["add"]);
    $block1->paletteIcon(1,"remove",$strings["delete"]);
    $block1->paletteIcon(2,"info",$strings["view"]);
    $block1->paletteIcon(3,"edit",$strings["edit"]);

    $block1->closePaletteIcon();

    $block1->sorting("calendar",$sortingUser->sor_calendar[0],"cal.date_end DESC",$sortingFields = array(0=>"cal.shortname",1=>"cal.subject",2=>"cal.date_start",3=>"cal.date_end"));

    $dayRecurr = _dayOfWeek(mktime(12,12,12,$month,$day,$year));
    $tmpquery = "WHERE (cal.owner = '$idSession' AND ((cal.date_start <= '$dateCalend' AND cal.date_end >= '$dateCalend' AND cal.recurring = '0') OR ((cal.date_start <= '$dateCalend' AND cal.date_end >= '$dateCalend') AND cal.recurring = '1' AND cal.recur_day = '$dayRecurr'))) OR (cal.broadcast = '1' AND ((cal.date_start <= '$dateCalend' AND cal.date_end >= '$dateCalend' AND cal.recurring = '0') OR ((cal.date_start <= '$dateCalend' AND cal.date_end >= '$dateCalend') AND cal.recurring = '1' AND cal.recur_day = '$dayRecurr'))) ORDER BY cal.shortname";  //changed
    //$tmpquery = "WHERE cal.owner = '$calId' AND cal.date_start <= '$dateCalend' AND cal.date_end >= '$dateCalend' ORDER BY $block1->sortingValue";
    $listCalendar = new Request();
    $listCalendar->openCalendar($tmpquery);
    $comptListCalendar = count($listCalendar->cal_id);

    if ($comptListCalendar != "0")
    {
        $block1->openResults();

        $block1->labels($labels = array(0=>$strings["shortname"],1=>$strings["subject"],2=>$strings["date_start"],3=>$strings["date_end"]),"false");

        for ($i=0;$i<$comptListCalendar;$i++)
        {
            $block1->openRow();
            $block1->checkboxRow($listCalendar->cal_id[$i]);
            $block1->cellRow($blockPage->buildLink("../calendar/viewcalendar.php?$dateEnreg=".$listCalendar->cal_id[$i]."&type=calendDetail&dateCalend=$dateCalend",$listCalendar->cal_shortname[$i],in));
            $block1->cellRow($listCalendar->cal_subject[$i]);
            $block1->cellRow($listCalendar->cal_date_start[$i]);
            $block1->cellRow($listCalendar->cal_date_end[$i]);
            $block1->closeRow();
        }

        $block1->closeResults();
    }
    else
    {
        $block1->noresults();
    }

    $block1->closeFormResults();

    $block1->openPaletteScript();
    $block1->paletteScript(0,"add","../calendar/viewcalendar.php?dateCalend=$dateCalend&type=calendEdit","true,false,false",$strings["add"]);
    $block1->paletteScript(1,"remove","../calendar/deletecalendar.php?","false,true,true",$strings["delete"]);
    $block1->paletteScript(2,"info","../calendar/viewcalendar.php?dateCalend=$dateCalend&type=calendDetail","false,true,false",$strings["view"]);
    $block1->paletteScript(3,"edit","../calendar/viewcalendar.php?dateCalend=$dateCalend&type=calendEdit","false,true,false",$strings["edit"]);
    $block1->closePaletteScript($comptListCalendar,$listCalendar->cal_id);
}

if ($type == "monthPreview")
{
    $blockPage->openBreadcrumbs();
    $blockPage->itemBreadcrumbs($blockPage->buildLink("../calendar/viewcalendar.php?",$strings["calendar"],in));
    $blockPage->itemBreadcrumbs("$monthName $year");
    $blockPage->closeBreadcrumbs();

    // include('memlist.php');

    $block2 = new Block();

    $block2->heading("$monthName $year");

    echo "<table border='0' cellpadding='0' cellspacing='2' width='100%' class='listing'><tr>";
    for($daynumber = 1; $daynumber < 8; $daynumber++)
    {
        echo "<td width='14%' class='calendDays'>&nbsp;$dayNameArray[$daynumber]</td>";
    }
    echo "</tr>";

    //  Print the calendar
    echo "<tr>";

    $tmpquery = "WHERE tas.assigned_to = '$idSession' ORDER BY tas.name";
    $listTasks = new Request();
    $listTasks->openTasks($tmpquery);
    $comptListTasks = count($listTasks->tas_id);

    $tmpquery = "WHERE subtas.assigned_to = '$idSession' ORDER BY subtas.name"; //Leave as calId
    $listSubtasks = new Request();
    $listSubtasks->openSubtasks($tmpquery);
    $comptListSubtasks = count($listSubtasks->subtas_id);
    $comptListCalendarScan = "0";
    for ($g=0;$g<$comptListTasks;$g++)
    {
        if (substr($listTasks->tas_start_date[$g],0,7) == substr($dateCalend,0,7))
        {
            $gantt = "true";
        }
    }

    //hack to check the td
    $weekremain = ($daysmonth -(7-($firstday - 1)));
    $daysremain = ($weekremain -(floor($weekremain / 7))*7);
    $colsremain = ((7-$daysremain));

    for ($i = 1; $i < $daysmonth + $firstday; $i++)
    {

        $a = $i - $firstday + 1;
        $day = $i - $firstday + 1;

        if (strlen($a) == 1)
        {
            $a = "0$a";
        }

        if (strlen($month) == 1)
        {
            $month = "0$month";
        }

        $dateLink = "$year-$month-$a";
        $todayClass = "";
        $dayRecurr = _dayOfWeek(mktime(12,12,12,$month,$a,$year));

        $tmpquery = "WHERE (cal.owner = '$idSession' AND ((cal.date_start <= '$dateLink' AND cal.date_end >= '$dateLink' AND cal.recurring = '0') OR ((cal.date_start <= '$dateLink' AND cal.date_end <= '$dateLink') AND cal.recurring = '1' AND cal.recur_day = '$dayRecurr'))) OR (cal.broadcast = '1' AND ((cal.date_start <= '$dateLink' AND cal.date_end >= '$dateLink' AND cal.recurring = '0') OR ((cal.date_start <= '$dateLink' AND cal.date_end <= '$dateLink') AND cal.recurring = '1' AND cal.recur_day = '$dayRecurr'))) ORDER BY cal.shortname";
        $listCalendarScan = new Request();
        $listCalendarScan->openCalendar($tmpquery);
        $comptListCalendarScan = count($listCalendarScan->cal_id);

        if (($i < $firstday) || ($a == "00"))
        {
            echo "<td width='14%' class='even'>&nbsp;</td>";
        }
        else
        {
            if ($dateLink == $dateToday)
            {
                $classCell = "old";
            }
            else
            {
                $classCell = "odd";
            }

        echo "<td width='14%' align='left' valign='top' class='$classCell' onmouseover=\"this.style.backgroundColor='".$block2->highlightOn."'\" onmouseout=\"this.style.backgroundColor='".$highlightOff."'\"><div align='right'>".$blockPage->buildLink("../calendar/viewcalendar.php?dateCalend=$dateLink&type=dayList",$day,in)."</div>";

        if ($comptListCalendarScan != "0")
        {
            for ($h=0;$h<$comptListCalendarScan;$h++)
            {
                // echo $blockPage->buildLink("../calendar/viewcalendar.php?dateEnreg=".$listCalendarScan->cal_id[$h]."&type=calendDetail&dateCalend=$dateLink",$listCalendarScan->cal_shortname[$h],in)."<br/>";
                if ($listCalendarScan->cal_broadcast[$h] == "0" && $listCalendarScan->cal_owner[$h] == $idSession)
                {
                    echo "<div align='center' class='calendar-regular-event'><a href='../calendar/viewcalendar.php?dateEnreg=".$listCalendarScan->cal_id[$h]."&type=calendDetail&dateCalend=$dateLink' class='calendar-regular-todo-event'>".$listCalendarScan->cal_shortname[$h]."</a></div>";
                }
                else if ($listCalendarScan->cal_broadcast[$h] != "0" && $listCalendarScan->cal_owner[$h] == $idSession)
                {
                    echo "<div align='center' class='calendar-regular-event'><a href='../calendar/viewcalendar.php?dateEnreg=".$listCalendarScan->cal_id[$h]."&type=calendDetail&dateCalend=$dateLink' class='calendar-regular-todo-event'><b>".$listCalendarScan->cal_shortname[$h]."</b></a></div>";
                }
                else
                {
                    echo "<div align='center' class='calendar-broadcast-event'><a href='../calendar/viewcalendar.php?dateEnreg=".$listCalendarScan->cal_id[$h]."&type=calendDetail&dateCalend=$dateLink' class='calendar-broadcast-todo-event'><b>".$listCalendarScan->cal_shortname[$h]."</b></a></div>";
                }
            }
        }

        if ($comptListTasks != "0")
        {
            for ($h=0;$h<$comptListTasks;$h++)
            {
                $idPriority = $listTasks->tas_priority[$h];

                if ($listTasks->tas_status[$h] == "3" || $listTasks->tas_status[$h] == "2")
                {
                    if ($listTasks->tas_start_date[$h] == $dateLink && $listTasks->tas_start_date[$h] != $listTasks->tas_due_date[$h])
                    {
                        echo "<img src=\"../themes/".THEME."/images/gfx_priority/".$idPriority.".gif\" alt='".$strings["priority"].": ".$priority[$idPriority]."' /> <b>".$strings["task"]."</b>: ";
                        echo "<a href='../tasks/viewtask.php?id=".$listTasks->tas_id[$h]."' class='calendar-results-start-date'>".$listTasks->tas_name[$h]."</a><br /><br />";
                    }

                    if ($listTasks->tas_due_date[$h] == $dateLink && $listTasks->tas_start_date[$h] != $listTasks->tas_due_date[$h])
                    {

                        if ($listTasks->tas_due_date[$h] <= $date && $listTasks->tas_completion[$h] != "10")
                        {
                            echo "<img src=\"../themes/".THEME."/images/gfx_priority/".$idPriority.".gif\" alt='".$strings["priority"].": ".$priority[$idPriority]."' /> <b>".$strings["task"]."</b>: ";
                            echo "<a href='../tasks/viewtask.php?id=".$listTasks->tas_id[$h]."' class='calendar-results-due-date'><b>".$listTasks->tas_name[$h]."</b></a><br /><br />";
                        }
                        else
                        {
                            echo "<img src=\"../themes/".THEME."/images/gfx_priority/".$idPriority.".gif\" alt='".$strings["priority"].": ".$priority[$idPriority]."' /> <b>".$strings["task"]."</b>: ";
                            echo "<a href='../tasks/viewtask.php?id=".$listTasks->tas_id[$h]."' class='calendar-results-due-date'>".$listTasks->tas_name[$h]."</a><br /><br />";
                        }
                    }

                    if ($listTasks->tas_start_date[$h] == $dateLink && $listTasks->tas_due_date[$h] == $dateLink)
                    {

                        if ($listTasks->tas_due_date[$h] <= $date && $listTasks->tas_completion[$h] != "10")
                        {
                            echo "<img src=\"../themes/".THEME."/images/gfx_priority/".$idPriority.".gif\" alt='".$strings["priority"].": ".$priority[$idPriority]."' /> <b>".$strings["task"]."</b>: ";
                            echo "<a href='../tasks/viewtask.php?id=".$listTasks->tas_id[$h]."' class='calendar-results-due-date'><b>".$listTasks->tas_name[$h]."</b></a><br /><br />";
                        }
                        else
                        {
                            echo "<img src=\"../themes/".THEME."/images/gfx_priority/".$idPriority.".gif\" alt='".$strings["priority"].": ".$priority[$idPriority]."' /> <b>".$strings["task"]."</b>: ";
                            echo "<a href='../tasks/viewtask.php?id=".$listTasks->tas_id[$h]."' class='calendar-results-due-date'>".$listTasks->tas_name[$h]."</a><br /><br />";
                        }
                    }

                }
                else
                {

                    if ($listTasks->tas_start_date[$h] == $dateLink && $listTasks->tas_start_date[$h] != $listTasks->tas_due_date[$h])
                    {
                        echo $blockPage->buildLink("../tasks/viewtask.php?id=".$listTasks->tas_id[$h],$listTasks->tas_name[$h],in)." (".$strings["start_date"].")<br/>";
                    }

                    if ($listTasks->tas_due_date[$h] == $dateLink && $listTasks->tas_start_date[$h] != $listTasks->tas_due_date[$h])
                    {
                        if ($listTasks->tas_due_date[$h] <= $date && $listTasks->tas_completion[$h] != "10")
                        {
                            echo $blockPage->buildLink("../tasks/viewtask.php?id=".$listTasks->tas_id[$h],"<b>".$listTasks->tas_name[$h]."</b>",in)." (".$strings["due_date"].")<br/>";
                        }
                        else
                        {
                            echo $blockPage->buildLink("../tasks/viewtask.php?id=".$listTasks->tas_id[$h],$listTasks->tas_name[$h],in)." (".$strings["due_date"].")<br/>";
                        }
                    }

                    if ($listTasks->tas_start_date[$h] == $dateLink && $listTasks->tas_due_date[$h] == $dateLink)
                    {

                        if ($listTasks->tas_due_date[$h] <= $date && $listTasks->tas_completion[$h] != "10")
                        {
                            echo $blockPage->buildLink("../tasks/viewtask.php?id=".$listTasks->tas_id[$h],"<b>".$listTasks->tas_name[$h]."</b>",in)."<br/>";
                        }
                        else
                        {
                            echo $blockPage->buildLink("../tasks/viewtask.php?id=".$listTasks->tas_id[$h],$listTasks->tas_name[$h],in)."<br/>";

                        }
                    }
                }

            }
        }

        if ($comptListSubtasks != "0")
        {

            for ($h=0;$h<$comptListSubtasks;$h++)
            {
                $idPriority = $listSubtasks->subtas_priority[$h];

                if ($listSubtasks->subtas_status[$h] == "3" || $listSubtasks->subtas_status[$h] == "2")
                {
                    if ($listSubtasks->subtas_start_date[$h] == $dateLink && $listSubtasks->subtas_start_date[$h] != $listSubtasks->subtas_due_date[$h])
                    {
                        echo "<img src=\"../themes/".THEME."/images/gfx_priority/".$idPriority.".gif\" alt='".$strings["priority"].": ".$priority[$idPriority]."' /> <b>".$strings["subtask"]."</b>: ";
                        echo "<a href='../subtasks/viewsubtask.php?id=".$listSubtasks->subtas_id[$h]."&task=".$listSubtasks->subtas_task[$h]."' class='calendar-results-start-date'>".$listSubtasks->subtas_name[$h]."</a><br /><br />";
                    }

                    if ($listSubtasks->subtas_due_date[$h] == $dateLink && $listSubtasks->subtas_start_date[$h] != $listSubtasks->subtas_due_date[$h])
                    {
                        echo "<img src=\"../themes/".THEME."/images/gfx_priority/".$idPriority.".gif\" alt='".$strings["priority"].": ".$priority[$idPriority]."' /> <b>".$strings["subtask"]."</b>: ";                        if ($listSubtasks->subtas_due_date[$h] <= $date && $listSubtasks->subtas_completion[$h] != "10") {
                        echo "<a href='../subtasks/viewsubtask.php?id=".$listSubtasks->subtas_id[$h]."&task=".$listSubtasks->subtas_task[$h]."' class='calendar-results-due-date'><b>".$listSubtasks->subtas_name[$h]."</b></a><br /><br />";
                    }
                    else
                    {
                        echo "<a href='../subtasks/viewsubtask.php?id=".$listSubtasks->subtas_id[$h]."&task=".$listSubtasks->subtas_task[$h]."' class='calendar-results-due-date'>".$listSubtasks->subtas_name[$h]."</a><br /><br />";
                    }
                }

                if ($listSubtasks->subtas_start_date[$h] == $dateLink && $listSubtasks->subtas_due_date[$h] == $dateLink)
                {
                    echo "<img src=\"../themes/".THEME."/images/gfx_priority/".$idPriority.".gif\" alt='".$strings["priority"].": ".$priority[$idPriority]."' /> <b>".$strings["subtask"]."</b>: ";

                    if ($listSubtasks->subtas_due_date[$h] <= $date && $listSubtasks->subtas_completion[$h] != "10")
                    {
                        echo "<a href='../subtasks/viewsubtask.php?id=".$listSubtasks->subtas_id[$h]."&task=".$listSubtasks->subtas_task[$h]."' class='calendar-results-due-date'><b>".$listSubtasks->subtas_name[$h]."</b></a><br /><br />";
                    }
                    else
                    {
                        echo "<a href='../subtasks/viewsubtask.php?id=".$listSubtasks->subtas_id[$h]."&task=".$listSubtasks->subtas_task[$h]."' class='calendar-results-due-date'>".$listSubtasks->subtas_name[$h]."</a><br /><br />";
                    }
                }
            }
            else
            {
                if ($listSubtasks->subtas_start_date[$h] == $dateLink && $listSubtasks->subtas_start_date[$h] != $listSubtasks->subtas_due_date[$h])
                {
                    echo "<img src=\"../themes/".THEME."/images/gfx_priority/".$idPriority.".gif\" alt='".$strings["priority"].": ".$priority[$idPriority]."' /> <b>".$strings["subtask"]."</b>: ";
                    echo "<a href='../subtasks/viewsubtask.php?id=".$listSubtasks->subtas_id[$h]."&task=".$listSubtasks->subtas_task[$h]."'>".$listSubtasks->subtas_name[$h]."</a><br /><br />";
                }

                if ($listSubtasks->subtas_due_date[$h] == $dateLink && $listSubtasks->subtas_start_date[$h] != $listSubtasks->subtas_due_date[$h])
                {
                    echo "<img src=\"../themes/".THEME."/images/gfx_priority/".$idPriority.".gif\" alt='".$strings["priority"].": ".$priority[$idPriority]."' /> <b>".$strings["subtask"]."</b>: ";

                    if ($listSubtasks->subtas_due_date[$h] <= $date && $listSubtasks->subtas_completion[$h] != "10")
                    {
                        echo "<a href='../subtasks/viewsubtask.php?id=".$listSubtasks->subtas_id[$h]."&task=".$listSubtasks->subtas_task[$h]."'><b>".$listSubtasks->subtas_name[$h]."</b></a><br /><br />";
                    }
                    else
                    {
                        echo "<a href='../subtasks/viewsubtask.php?id=".$listSubtasks->subtas_id[$h]."&task=".$listSubtasks->subtas_task[$h]."'>".$listSubtasks->subtas_name[$h]."</a><br /><br />";
                    }
                }

                if ($listSubtasks->subtas_start_date[$h] == $dateLink && $listSubtasks->subtas_due_date[$h] == $dateLink)
                {
                    echo "<img src=\"../themes/".THEME."/images/gfx_priority/".$idPriority.".gif\" alt='".$strings["priority"].": ".$priority[$idPriority]."' /> <b>".$strings["subtask"]."</b>: ";

                    if ($listSubtasks->subtas_due_date[$h] <= $date && $listSubtasks->subtas_completion[$h] != "10")
                    {
                        echo "<a href='../subtasks/viewsubtask.php?id=".$listSubtasks->subtas_id[$h]."&task=".$listSubtasks->subtas_task[$h]."'><b>".$listSubtasks->subtas_name[$h]."</b></a><br /><br />";
                    }
                    else
                    {
                        echo "<a href='../subtasks/viewsubtask.php?id=".$listSubtasks->subtas_id[$h]."&task=".$listSubtasks->subtas_task[$h]."'>".$listSubtasks->subtas_name[$h]."</a><br /><br />";
                    }
                }
            }
        }
    }

        if ($comptListTasks == "0" ||  $comptListSubtasks == "0" || $comptListCalendarScan == "0")
        {
            echo "<br />";
        }

    echo "</td>";

    }

    if (($i%7) == "0")
    {
        echo "</tr>\n";
    }
}


if ($colsremain != "7"){
    for ($j=0;$j<$colsremain;$j++){
        echo "<td class='even'>&nbsp;</td>\n";
        }
}

echo "</tr></table>";

    if ($month == 1) {
        $pyear = $year - 1;
        $pmonth = 12;
    } else {
        $pyear = $year;
        $pmonth = $month - 1;
    }

    if ($month == 12) {
        $nyear = $year + 1;
        $nmonth = 1;
    } else {
        $nyear = $year;
        $nmonth = $month + 1;
    }

    $year = date("Y");
    $month = date("n");
    $day = date("j");
    if (strlen($month) == 1) {
        $month = "0$month";
    }
    if (strlen($pmonth) == 1) {
        $pmonth = "0$pmonth";
    }
    if (strlen($nmonth) == 1) {
        $nmonth = "0$nmonth";
    }
    if (strlen($day) == 1) {
        $day= "0$day";
    }

    $datePast = "$pyear-$pmonth-01";
    $dateNext = "$nyear-$nmonth-01";

    $dateToday = "$year-$month-$day";
        echo "<table><tr><td class='calend'> </td></tr></table>";

    echo "  <table cellspacing='0' width='100%' border='0' cellpadding='0'>
            <tr>
                <td nowrap align='right' class='footerCell'>".$blockPage->buildLink("../calendar/viewcalendar.php?dateCalend=$datePast",$strings["previous"],in)." | ".$blockPage->buildLink("../calendar/viewcalendar.php?dateCalend=$dateToday",$strings["today"],in)." | ".$blockPage->buildLink("../calendar/viewcalendar.php?dateCalend=$dateNext",$strings["next"],in)."</td>
            </tr>
            <tr>
                <td height='5' colspan='2'><img width='1' height='5' border='0' src='../themes/".THEME."/spacer.gif' alt=''></td>
            </tr>
            </table>";

    if ($activeJpgraph == "true" && $gantt == "true")
    {
		echo "
			<div id='ganttChart_taskList' class='ganttChart'>
				<img src='graphtasks.php?".session_name()."=".session_id()."&dateCalend=$dateCalend' alt=''><br/>
				<span class='listEvenBold''>".$blockPage->buildLink("http://www.aditus.nu/jpgraph/","JpGraph",powered)."</span>	
			</div>
		";
    }
}

include('../themes/'.THEME.'/footer.php');
?>