<?php
#Application name: PhpCollab
#Status page: 0
echo "<p id='footer'><a href='http://www.phpcollab.com' target='blank' title='phpcollab homepage'>PhpCollab v$version</a>";
if ($notLogged != "true" && $blank != "true") {
echo " - Connected users: $connectedUsers";
}

if ($footerDev == "true") {
	$parse_end = phpCollab\Util::getMicroTime();
	$parse = $parse_end - $parse_start;
	$parse = round($parse,3);
	echo " - $parse secondes - databaseType $databaseType - select requests $comptRequest";
	echo " - <a href='http://validator.w3.org/check/referer' target='w3c'>w3c</a> (in progress)&nbsp;&nbsp;&nbsp;";
}

echo "	</p>\n\n
		</body>
		</html>
	 ";
