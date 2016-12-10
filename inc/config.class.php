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

class PluginTelegrambotConfig extends CommonDBTM {

   static function getToken() {
      global $DB;
      $table = self::getTable();

      $query   = "SELECT `value` AS `token` FROM `$table` WHERE `name` = 'token'";
      $result  = $DB->query($query);

      return $DB->result($result, 0, 'token');
   }

   static function setToken($token) {
      global $DB;
      $table = self::getTable();

      $query = "UPDATE `$table` SET `value` = '$token' WHERE `name` = 'token'";
      $DB->query($query);
   }

   static function getUsername() {
      global $DB;
      $table = self::getTable();

      $query   = "SELECT `value` AS `username` FROM `$table` WHERE `name` = 'admin_username'";
      $result  = $DB->query($query);

      return $DB->result($result, 0, 'username');
   }

   static function setUsername($username) {
      global $DB;
      $table = self::getTable();

      $query = "UPDATE `$table` SET `value` = '$username' WHERE `name` = 'admin_username';";
      $DB->query($query);
   }

   function getTabNameForItem(CommonGLPI $item, $withtemplate=0) {
      if (!$withtemplate) {
         if ($item->getType() == 'Config') {
            return __('Telegram');
         }
      }
      
      return '';
   }

   function showForm($token, $username) {
      global $CFG_GLPI;

      $action = $CFG_GLPI['root_doc'] . '/plugins/telegrambot/front/config.form.php';

      echo "<form name='form' action='" . $action . "' method='post'";
      echo "<div class='center' id='tabsbody'>";
      echo "<table class='tab_cadre_fixe'>";
      echo "<tr><th colspan='4'>" . __('Telegram setup') . "</th></tr>";
      echo "<tr><td>" . __('Token:') . "</td>";
      echo "<td>";
      echo "<input name='token' type='text' value='$token' style='width: 400px'/>";
      echo "</td></tr>";
      echo "<tr><td>" . __('Administrator username:') . "</td>";
      echo "<td>";
      echo "<input name='username' type='text' value='$username' style='width: 400px'/>";
      echo "</td></tr>";
      echo "<tr class='tab_bg_2'>";
      echo "<td colspan='4' class='center'>";
      echo "<input type='submit' name='update' class='submit' value=\"" . _sx('button', 'Save') . "\">";
      echo "</td></tr>";
      echo "</table></div>";

      Html::closeForm();
   }

   static function displayTabContentForItem(CommonGLPI $item, $tabnum=1, $withtemplate=0) {
      if ($item->getType() == 'Config') {
         $form = new self();
         $form->showForm(self::getToken(), self::getUsername());
      }
   }

}

?>