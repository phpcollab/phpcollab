<?php
#Application name: PhpCollab
#Status page: 0

echo <<<HTML
<footer id="footer">
    &copy {$siteTitle}
</footer>
HTML;

if ($debug === true && isset($debugbarRenderer) && is_object($debugbarRenderer)) {
    echo $debugbarRenderer->render();
}
echo <<<HTML
    </body>
</html>
HTML;
