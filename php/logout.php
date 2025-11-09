<?php
session_start();
session_destroy();
header("Location: ../citizen_login.html");
exit();
?>
