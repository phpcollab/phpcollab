<?php
#Application name: PhpCollab
#Status page: 2
#Path by root: ../includes/vcard.class.php

/***************************************************************************

PHP vCard class v2.0
(c) Kai Blankenhorn
www.bitfolge.de/en
kaib@bitfolge.de


This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.

***************************************************************************/


function encode($string) {
	return escape(quoted_printable_encode($string));
}

function escape($string) {
	return str_replace(";","\;",$string);
}

// taken from PHP documentation comments
function quoted_printable_encode($input, $line_max = 76) {
	$hex = array('0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F');
	$lines = preg_split("/(?:\r\n|\r|\n)/", $input);
	$eol = "\r\n";
	$linebreak = "=0D=0A";
	$escape = "=";
	$output = "";

	for ($j=0;$j<count($lines);$j++) {
		$line = $lines[$j];
		$linlen = strlen($line);
		$newline = "";
		for($i = 0; $i < $linlen; $i++) {
			$c = substr($line, $i, 1);
			$dec = ord($c);
			if ( ($dec == 32) && ($i == ($linlen - 1)) ) { // convert space at eol only
				$c = "=20"; 
			} elseif ( ($dec == 61) || ($dec < 32 ) || ($dec > 126) ) { // always encode "\t", which is *not* required
				$h2 = floor($dec/16); $h1 = floor($dec%16); 
				$c = $escape.$hex["$h2"].$hex["$h1"]; 
			}
			if ( (strlen($newline) + strlen($c)) >= $line_max ) { // CRLF is not counted
				$output .= $newline.$escape.$eol; // soft line break; " =\r\n" is okay
				$newline = "    ";
			}
			$newline .= $c;
		} // end of for
		$output .= $newline;
		if ($j<count($lines)-1) $output .= $linebreak;
	}
	return trim($output);
}

class vCard {
	var $properties;
	var $filename;
	
	function setPhoneNumber($number, $type="") {
	// type may be PREF | WORK | HOME | VOICE | FAX | MSG | CELL | PAGER | BBS | CAR | MODEM | ISDN | VIDEO or any senseful combination, e.g. "PREF;WORK;VOICE"
		$key = "TEL";
		if ($type!="") $key .= ";".$type;
		$key.= ";ENCODING=QUOTED-PRINTABLE";
		$this->properties[$key] = quoted_printable_encode($number);
	}
	
	// UNTESTED !!!
	function setPhoto($type, $photo) { // $type = "GIF" | "JPEG"
		$this->properties["PHOTO;TYPE=$type;ENCODING=BASE64"] = base64_encode($photo);
	}
	
	function setFormattedName($name) {
		$this->properties["FN"] = quoted_printable_encode($name);
	}
	
	function setName($fullName) {
		$this->properties["N"] = "$fullName";
		$this->filename = "$fullName.vcf";
		if ($this->properties["FN"]=="") $this->setFormattedName(trim("$fullName"));
	}
	
	function setTitle($value) {
		$this->properties["TITLE"] = $value;
	}

	function setOrganization($value) {
		$this->properties["ORG"] = $value;
	}

	function setAddress($postoffice="", $extended="", $street="", $city="", $region="", $zip="", $country="", $type="HOME;POSTAL") {
	// $type may be DOM | INTL | POSTAL | PARCEL | HOME | WORK or any combination of these: e.g. "WORK;PARCEL;POSTAL"
		$key = "ADR";
		if ($type!="") $key.= ";$type";
		$key.= ";ENCODING=QUOTED-PRINTABLE";
		$this->properties[$key] = encode($name).";".encode($extended).";".encode($street).";".encode($city).";".encode($region).";".encode($zip).";".encode($country);
		
		if ($this->properties["LABEL;$type;ENCODING=QUOTED-PRINTABLE"] == "") {
		}
	}
	
	function setLabel($postoffice="", $extended="", $street="", $city="", $region="", $zip="", $country="", $type="HOME;POSTAL") {
		$label = "";
		if ($postoffice!="") $label.= "$postoffice\r\n";
		if ($extended!="") $label.= "$extended\r\n";
		if ($street!="") $label.= "$street\r\n";
		if ($zip!="") $label.= "$zip ";
		if ($city!="") $label.= "$city\r\n";
		if ($region!="") $label.= "$region\r\n";
		if ($country!="") $country.= "$country\r\n";
		
		$this->properties["LABEL;$type;ENCODING=QUOTED-PRINTABLE"] = quoted_printable_encode($label);
	}
	
	function setEmail($address) {
		$this->properties["EMAIL;INTERNET"] = $address;
	}
	
	function setNote($note) {
		$this->properties["NOTE;ENCODING=QUOTED-PRINTABLE"] = quoted_printable_encode($note);
	}
	
	function setURL($url, $type="") {
	// $type may be WORK | HOME
		$key = "URL";
		if ($type!="") $key.= ";$type";
		$this->properties[$key] = $url;
	}
	
	function getVCard() {
		$text = "BEGIN:VCARD\r\n";
		$text.= "VERSION:2.1\r\n";
		foreach($this->properties as $key => $value) {
			$text.= "$key:$value\r\n";
		}
		$text.= "REV:".date("Y-m-d")."T".date("H:i:s")."Z\r\n";
		$text.= "MAILER:PHP vCard class by Kai Blankenhorn\r\n";
		$text.= "END:VCARD\r\n";
		return $text;
	}
	
	function getFileName() {
		return $this->filename;
	}
}

class vCalendar {
	var $properties;
	var $eventname;
	
	function setName($eventName) {
		$this->eventname = "$eventName.ics";
		}
	
	function setSummary($value) {
		$this->properties["SUMMARY"] = $value;
		}

	function setOrganizer($value) {
		$this->properties["ORGANIZER:MAILTO"] = $value;
		}

	function setStartDate($date,$time) {
		$this->properties["DTSTART"] = $date."T".$time;		
		}
	
	function setEndDate($date,$time) {
		$this->properties["DTEND"] = $date."T".$time;		
		}
	
	function setLocation($location) {
		$this->properties["LOCATION;ENCODING=QUOTED-PRINTABLE"] = quoted_printable_encode($location);
		}
	
	function setDescription($description) {
		$this->properties["DESCRIPTION"] = $description;
		}
	
	function setReminder($reminder) {
		if ($reminder == "1"){
			$this->properties["BEGIN"] = "VALARM";
			$this->properties["TRIGGER"] = "PT15M";
			$this->properties["ACTION"] = "DISPLAY";
			$this->properties["END"] = "VALARM";
			}	
		}
	
	function getVCalendar() {
		$text = "BEGIN:VCALENDAR\r\n";
		$text .= "VERSION:1.0\r\n";
		$text .= "METHOD:PUBLISH\r\n";
		$text .= "BEGIN:VEVENT\r\n";
		foreach($this->properties as $key => $value) {
			$text.= "$key:$value\r\n";
			}
		$text .= "TRANSP:1\r\n";
		$text .= "SEQUENCE:0\r\n";
		$text .= "UID:040000008200E00074C5B7101A82E00800000000A03EAED7766FC20100000000000000001000000056B56C3860D17B448DC0B0DB90B2BEB6\r\n";
		$text .= "PRIORITY:3\r\n";
		$text .= "CLASS:PUBLIC\r\n";
		$text.= "REV:".date("Y-m-d")."T".date("H:i:s")."Z\r\n";
		$text .= "END:VEVENT\r\n";
		$text .= "END:VCALENDAR\r\n";
		return $text;
		}
	
	function getCalendarName() {
		return $this->eventname;
		}
}

?>