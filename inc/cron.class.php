<?php

/*
* @version $Id: HEADER 15930 2016-08-29 10:47:55Z jmd $
-------------------------------------------------------------------------
GLPI - Gestionnaire Libre de Parc Informatique
Copyright (C) 2003-2016 by the INDEPNET Development Team.

http://indepnet.net/   http://glpi-project.org
-------------------------------------------------------------------------

LICENSE

This file is part of GLPI.

GLPI is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

GLPI is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with GLPI. If not, see <http://www.gnu.org/licenses/>.
--------------------------------------------------------------------------
*/

include_once('core.class.php');

class PluginTelegrambotCron {

   static function getTypeName($nb = 0) {
      return 'Telegrambot';
   }

   static function cronInfo($name) {
      switch ($name) {
         case 'MessageListener':
            return array('description' => __('Handles incoming bot messages', 'telegrambot'));

         case 'SendNotification': 
            return array('description' => __('Send Telegram notifications', 'telegrambot'));
      }

      return array();
   }

   static function cronMessageListener($task) {
      $messages   = PluginTelegrambotCore::handleGetUpdates();
      $count      = count($messages['result']);

      if($messages['ok']) {
         foreach ($messages['result'] as $key => $value) {
            $data = array(
               'update_id'  => $value['update_id'],
               'message_id' => $value['message']['message_id'],
               'user_id'    => $value['message']['from']['id'],
               'date'       => $value['message']['date'],
               'text'       => $value['message']['text'],
               'first_name' => $value['message']['from']['first_name'],
               'last_name'  => $value['message']['from']['last_name'],
               'username'   => $value['message']['from']['username']
            );

            PluginTelegrambotMessage::processUpdate($data);
         }
      }

      $task->log("Telegrambot has processed $count new messages");
      return 1;
   }

   static function cronSendNotification($task) {
      $notification  = new PluginTelegrambotNotification();
      $notifications = $notification->getNotSent();

      foreach($notifications as $data) {
         $notification_id  = $data['id'];
         $chat_id          = $data['chat_id'];
         $message          = $data['message'];

         PluginTelegrambotCore::sendMessage($chat_id, $message);
         $notification->updateDeleteStatus($notification_id);
      }

      $task->log("Telegrambot notifications has been sent");
      return 1;
   }

}

?>