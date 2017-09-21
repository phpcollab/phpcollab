<?php
/**
 * Data functions that will be used through the app
 * These functions are global and shoudl be safer
 * @author David Bates (norman77@users.sourceforge.net) ($Author: norman77 $)
 * @since 05-Nov-2008
 * @version $Revision: 1.3 $
 */


/**
 * Scrubs data .. makes it safe for writting into settings.php or database
 * @return array $returnData Scrubbed Data
 * @param array $data Data from Post or GET (key - val paris)
 */
function scrubData($data)
{
    $regEx = "/[^a-zA-Z0-9 .@:\/_]*/"; // Used to remove characters, if they aren't in this list, they will be removed

    $retData = array();
    foreach ($data as $key => $val) {
        // This is a hack until the data scrubbing can be normalized.
        if ($key == "defaultLanguage") {
            $dVal = $val;
        } else {
            $dVal = preg_replace($regEx, '', $val);
        }

        $retData[$key] = $dVal;
    }
    return $retData;
}
