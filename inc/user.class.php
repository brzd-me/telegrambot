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

class PluginTelegrambotUser extends CommonDBTM {

   static function getChatID($user_id) {
      global $DB;
      $table = self::getTable();

      if(isset($user_id)) { // regular user
         $query   = "SELECT `T`.`id` AS `chat_id` FROM `$table` `T`
         INNER JOIN `glpi_users` `U` ON(`T`.`username` = `U`.`phone2`)
         WHERE `U`.`id` = $user_id";
      } else { // global admin user
         $query   = "SELECT `U`.`id` AS `chat_id` FROM `$table` `U`
         INNER JOIN `glpi_plugin_telegrambot_configs` `C` ON (`U`.`username` = `C`.`value`)
         WHERE `C`.`name` = 'admin_username'";
      }

      $result  = $DB->query($query);

      return $DB->result($result, 0, 'chat_id');
   }

   static function insertUser($user_id, $first_name, $last_name, $username) {
      global $DB;

      $table   = self::getTable();
      $query   = "SELECT COUNT(*) AS `count` FROM `$table` WHERE `id` = '$user_id'";
      $result  = $result  = $DB->query($query);
      $exists  = $DB->result($result, 0, 'count');

      if(!$exists) {
         $query = "INSERT INTO `$table` (`id`, `first_name`, `last_name`, `username`)
                  VALUES('$user_id', '$first_name', '$last_name', '$username')";

         $DB->query($query);
      }
   }
}

?>