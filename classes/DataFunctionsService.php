<?php

namespace phpCollab;

/**
 * Data functions that will be used through the app
 * These functions are global and should be safer
 */
class DataFunctionsService
{
    /**
     * Scrubs data .. makes it safe for writting into settings.php or database
     * @param array $data Data from Post or GET (key - val paris)
     * @return array $returnData Scrubbed Data
     */
    public static function scrubData(array $data): array
    {
        /**
         * Used to remove characters, if they aren't in this list, they will be removed
         */
        $regEx = "/[^a-zA-Z0-9 .@:\/_-]*/";

        $retData = array();
        foreach ($data as $key => $val) {
            $dVal = preg_replace($regEx, '', $val);
            $retData[$key] = $dVal;
        }

        return $retData;
    }
}
