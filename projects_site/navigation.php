<?php
#Application name: PhpCollab
#Status page: 0
?>

<!-- Navigation Start -->
<form method="post" name="login"
      action="<?php echo $pathMantis ?>login.php?url=<?php echo "http://{$HTTP_HOST}{$REQUEST_URI}" ?>&id=<?php echo $projectSession ?>&PHPSESSID=<?php echo $PHPSESSID; ?>">
    <input type="hidden" name="username" value="<?php echo $loginSession; ?>">
    <input type="hidden" name="password" value="<?php echo $passwordSession; ?>">
    <!-- Navigation End -->