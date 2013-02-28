<?php
#Application name: PhpCollab
#Status page: 0
/* $Id: footer.inc.php,v 1.1 2003/07/02 14:47:06 fullo Exp $ */


// In this file you may add PHP or HTML statements that will be used to define
// the footer for phpMyAdmin pages.
?>

</body>

</html>
<?php

/**
 * Sends bufferized data
 */
if (isset($cfgOBGzip) && $cfgOBGzip
    && isset($ob_mode) && $ob_mode) {
     out_buffer_post($ob_mode);
}
?> 
