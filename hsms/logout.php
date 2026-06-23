<?php
session_start();
session_unset();
session_destroy();
header("Location: /hsms/index.php");
exit;
?>
