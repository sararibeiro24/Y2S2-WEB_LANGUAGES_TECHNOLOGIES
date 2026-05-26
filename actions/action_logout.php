<?php
declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');

Session::start();
Session::destroy();

header('Location: ../pages/index.php');
exit;
?>
