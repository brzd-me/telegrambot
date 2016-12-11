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

class PluginTelegrambotNotification extends CommonDBTM {
   
   function sendNotification($data=array()) {
      $data['chat_id'] = PluginTelegrambotUser::getChatID($data['user_id']);

      if(isset($data['chat_id'])) {
         $data = $this->prepareForAdd($data);
         $this->addNotification($data);
      }
   }

   function getNotSent() {
      global $DB;
      $table = $this->getTable();

      $query = "SELECT `id`, `chat_id`, `message` FROM `$table` WHERE NOT `is_deleted`";
      return $DB->query($query);
   }

   function updateDeleteStatus($notification_id) {
      global $DB;
      $table = $this->getTable();

      $query   = "UPDATE `$table` SET `is_deleted` = 1 WHERE `id` = $notification_id";
      $result  = $DB->query($query);
   }

   private function prepareForAdd($data=array()) {
      global $DB;

      // Drop existing notification for the same event, item and chat_id
      $this->dropExistingNotification(
         $data['chat_id'],
         $data['item_type'],
         $data['item_id'],
         $data['template_id']
      );

      return $data;
   }

   private function addNotification($data=array()) {
      global $DB;
      $table = $this->getTable();

      $chat_id       = $data['chat_id'];
      $item_type     = $data['item_type'];
      $item_id       = $data['item_id'];
      $template_id   = $data['template_id'];
      $message       = $data['message'];
      $create_time   = $data['date_mod'];

      if($chat_id) {
         $query = "INSERT INTO `$table`
         (`chat_id`, `item_type`, `item_id`, `template_id`, `message`, `create_time`)
         VALUES('$chat_id', '$item_type', '$item_id', '$template_id', '$message', '$create_time')";

         $DB->query($query);
      }
   }

   private function dropExistingNotification($chat_id, $item_type, $item_id, $template_id) {
      global $DB;
      $table = $this->getTable();

      $query = "DELETE FROM `$table` WHERE
               NOT `is_deleted`
               AND `chat_id` = '$chat_id'
               AND `item_type` = '$item_type'
               AND `item_id` = '$item_id'
               AND `template_id` = '$template_id'";

      $DB->query($query);
   }

}

?>
