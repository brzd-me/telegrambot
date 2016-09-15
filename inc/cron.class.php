<?php

require_once('telegram.class.php');

class PluginTelegrambotCron extends CommonDBTM {
   static function getTypeName($nb = 0) {
      return 'Telegrambot';
   }

   static function cronInfo($name) {
      switch ($name) {
         case 'MessageListener':
            return array('description' => __('Handles incoming bot messages', 'telegrambot'));
      }
      return array();
   }

   static function cronMessageListener($task) {
      global $DB;

      $query = "SELECT `value` AS token FROM glpi_plugin_telegrambot_configs WHERE `name` = 'token'";
      $result = $DB->query($query);
      $token = $DB->result($result, 0, 'token');

      $telegram   = new PluginTelegrambotCore($token);
      $response   = $telegram->handle_get_updates();
      $count      = count($response);

      $task->log("Telegrambot has processed $count new messages");
      return 1;
   }
}

?>