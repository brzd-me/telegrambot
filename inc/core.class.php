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

class PluginTelegrambotCore {

   const BOT_URL = 'https://api.telegram.org/bot';

   static function getMe() {
      return self::sendAPIRequest('getMe', array());
   }

   static function sendMessage($chat_id, $message) {
      $content = array('chat_id' => $chat_id, 'text' => $message);
      return self::sendAPIRequest('sendMessage', $content);
   }

   static function handleGetUpdates() {
      $last_update_id = PluginTelegrambotMessage::getLastUpdateID();
      return self::getUpdates($last_update_id);
   }

   private static function getAPIEndpoint() {
      return self::BOT_URL . PluginTelegrambotConfig::getToken();
   }

   private static function getUpdates($offset=null, $limit=100, $timeout=0) {
      $content = array(
         'offset'    => $offset,
         'limit'     => $limit,
         'timeout'   => $timeout
      );

      return self::sendAPIRequest('getUpdates', $content);
   }

   private static function sendAPIRequest($action, array $content) {
      $url  = self::getAPIEndpoint() . '/' . $action;
      $ch   = curl_init();

      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_HEADER, false);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

      $response = curl_exec($ch);
      curl_close($ch);

      return json_decode($response, true);
   }

}

?>
