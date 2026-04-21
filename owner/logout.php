<?php
require_once __DIR__ . '/admin_common.php';

unset($_SESSION['owner_logged_in'], $_SESSION['owner_user']);
session_regenerate_id(true);
header('Location: login.php');
exit;
