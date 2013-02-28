<?php
/*
** Application name: phpCollab
** Last Edit page: 2003-10-23 
** Path by root: ../browsecvs/browsecvs.class.php
** Authors: Ceam / TY / Fullo 
** =============================================================================
**
**               phpCollab - Project Managment 
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: browsecvs.class.php
**
** DESC: Library: configuration of browsecvs, the web interface for CVS repositories.
**
** HISTORY:
** 	2003-10-23	-	added new document info
** -----------------------------------------------------------------------------
** TO-DO:
**	check for possible updated library	 
**
** =============================================================================
*/

class browsecvs
{
	function browsecvs()
	{
	}
	function getRevision($file, $rev = "")
	{
		global $conf;
		$f = EscapeShellCmd($file);
		if ($rev != "")
			$rev = EscapeShellCmd("$rev");
		$rlog = $conf['bin']['rlog'];
		$revinfo = `$rlog -r$rev $f 2>&1`;
		$rev2 = $revinfo;
		if (ereg("lines: (.+)", $rev2, $regs) )
		{
			$temp = explode(" ",str_replace("\n"," ",$regs[1]));
			$lines = $temp[0] ." ".$temp[1];
		}
		unset($temp);
		unset($regs);
		if (!ereg("\ndescription:\n([^\n]*\n)?-+\nrevision ([^\n ]*)\n".
			    "date: ([0-9/]+ [0-9:]+); +author: ([^;]*);[^\n]*\n(.*)\n=+\n",
				$revinfo, $regs))
			return NULL;
		return array("rev" => $regs[2],
			    "date" => $regs[3],
			    "auth" => $regs[4],
			    "comment" => $regs[5],
			    "lines" => $lines);
	}
	function revcmp($rev1, $rev2)
	{
		$r1 = split("\.", $rev1);
		$r2 = split("\.", $rev2);
		while (count($r1) > 0 && count($r2) > 0)
		{
			$a = array_shift($r1);
			$b = array_shift($r2);
			if ($a != $b) return $a-$b;
		}
		return (count($r1) > 0)? 1: (count($r2) > 0)? -1 : 0;
	}
	function rrevcmp($rev1, $rev2) { return revcmp($rev1, $rev2) * -1; }
	function doCheckout($rep, $file, $rev = "")
	{
		global $conf;
		$cvsroot = EscapeShellCmd("-d ".$conf['cvsrep'][$rep]);
		$file = EscapeShellCMd($file);
		if ($rev != "")
			$rev = "-r $rev";
		$cmd = $conf['bin']['cvs']." -Q $cvsroot checkout $rev -p $file";
		exec($cmd,$lines);
		highlight_string(implode("\n",$lines)); // todo: binary files?
	}
	function timetoreadable($date, $long = false) {
		global $textutil;
		$secs = time() - strtotime(str_replace("/","-",$date)." UTC");
		if ($long) return $textutil->formattime("%y %m %w %d %h %i %s",$secs);
		return $textutil->formattime("%x",$secs);
	}
	function getRevisionTree($file)
	{
		global $conf;
		// run rlog
		$f = EscapeShellCmd($file);
		$rlog = $conf['bin']['rlog'];
		exec("$rlog $f,v",$log);
		$info = NULL;

		// retrieve head info
		for ($i=0; $i<count($log) && !ereg("^-+$", $log[$i]); $i++)
		{
			if (ereg("^([^:]+): *(.*)$", $log[$i], $regs))
			{
				$key = ereg_replace(" ", "_", strtoupper($regs[1]));
				switch ($regs[1])
				{
				case 'head':
				case 'branch': $info['branch'] = $regs[2]; break;
				case 'symbolic names':
					while (ereg("^\t([^:]*): (.*)$", $log[++$i], $regs) )
					{
						$info['tagsr'][$regs[1]] = $regs[2];
						if (! is_array($info['tagsr'][$regs[2]]))
						$info['tagsr'][$regs[2]] = array();
						array_push($info['tagsr'][$regs[2]], $regs[1]);
					}
					$i--;
					break;
				case 'description':
					while (++$i < count($log) && !ereg("^-+$", $log[$i]))
						$info[$key] .= ereg_replace("\n$", "", $log[$i]);
					$i--;
					break;
				}
			}
		}
		$i++;

		// retrieve log info
		while ($i < count($log))
		{
			// pick revision number
			if (! ereg("^revision *([0-9.]+)$", $log[$i], $regs)) break;
			$rev = $regs[1];
			$i++;

			// pick IDs
			if (ereg("^date: ([0-9/]+ [0-9:]+);.* author: ([^;]+);.*$", $log[$i], $regs) )
			{
				$info['log'][$rev]['date'] = $regs[1];
				$info['log'][$rev]['auth'] = $regs[2];
			}
			if (ereg("lines: (.+)", $log[$i], $regs) )
				$info['log'][$rev]['lines'] = $regs[1];
			$i++;

			// pick branch
			while ($i < count($log) && ereg("^branches: *([0-9.]+)", $log[$i], $regs))
			{
				$info['log'][$rev]['branches'] = array();
				foreach (split(";", $regs[1]) as $branches)
					array_push($info['log'][$rev]['branches'], trim($branches) );
				$i++;
			}

			// pick comment lines
			while ($i < count($log) && !ereg("^(-+|=+)$", $log[$i]) )
				$info['log'][$rev]['comment'] .= $log[$i++] . "\n";
			$i++;
		}

		// sort log entries
		uksort($info['log'], "$this->rrevcmp");
		return $info;
	}
}

$browser = new browsecvs();

?>