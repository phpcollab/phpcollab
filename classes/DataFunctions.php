<?php

/**
 * Data functions that will be used through the app
 * These functions are global and should be safer
 */
class DataFunctions
{
    /**
     * Scrubs data .. makes it safe for writting into settings.php or database
     * @return array $returnData Scrubbed Data
     * @param array $data Data from Post or GET (key - val paris)
     */
    public function scrubData($data)
    {
        /**
         * Used to remove characters, if they aren't in this list, they will be removed
         */
        $regEx = "/[^a-zA-Z0-9 .@:\/_]*/";

        $retData = array();
        foreach ($data as $key => $val) {
            $dVal = preg_replace($regEx, '', $val);
            $retData[$key] = $dVal;
        }

        return $retData;
    }
}
