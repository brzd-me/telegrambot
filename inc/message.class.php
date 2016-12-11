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

class PluginTelegrambotMessage extends CommonDBTM {

   static function getLastUpdateID() {
      global $DB;
      $table = self::getTable();

      $query            = "SELECT MAX(`update_id`) AS `update_id` FROM `$table`";
      $result           = $DB->query($query);
      $last_update_id   = 1 + (int) $DB->result($result, 0, 'update_id');

      return $last_update_id;
   }

   static function processUpdate($data=array()) {
      PluginTelegrambotUser::insertUser(
         $data['user_id'],
         $data['first_name'],
         $data['last_name'],
         $data['username']
      );

      self::insertMessage(
         $data['update_id'],
         $data['message_id'],
         $data['user_id'],
         date('Y-m-d H:i:s', $data['date']),
         $data['text']
      );
   }

   private static function insertMessage($update_id, $message_id, $user_id, $date, $text) {
      global $DB;
      $table = self::getTable();

      $query = "INSERT INTO `$table` (`update_id`, `message_id`, `user_id`, `date`, `text`)
               VALUES('$update_id', '$message_id', '$user_id', '$date', '$text')";

      $DB->query($query);
   }

}

?>