<?php
#Application name: PhpCollab
#Status page: 0

echo <<<HTML
<footer id="footer">
    &copy {$siteTitle}
</footer>
HTML;


if ($footerDev == "true") {
    $parse_end = phpCollab\Util::getMicroTime();
    $parse = $parse_end - $parse_start;
    $parse = round($parse, 3);
    echo <<<DEBUG_INFO
        {$parse} seconds - databaseType {$databaseType} - select requests {$comptRequest}
DEBUG_INFO;
}

if ($debug === true && is_object($debugbarRenderer)) {
    echo $debugbarRenderer->render();
}
echo <<<HTML
    </body>
</html>
HTML;
