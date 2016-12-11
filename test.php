<?php

include ("../../inc/includes.php");
include_once('inc/core.class.php');

$messages   = PluginTelegrambotCore::handleGetUpdates();
$count      = count($messages['result']);

echo $count;

?>