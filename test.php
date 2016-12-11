<?php

include ("../../inc/includes.php");
include_once('inc/notification.class.php');

$notification  = new PluginTelegrambotNotification();
$result        = $notification->getNotSent();

die(d($result));

?>