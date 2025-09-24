<?php
require_once(__DIR__ . '/models/session.php');

logoutUser();
header('Location: /academia/login.php');
exit;
?>


