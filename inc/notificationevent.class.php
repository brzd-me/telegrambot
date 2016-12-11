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

class PluginTelegrambotNotificationEvent {

   static function raiseEvent($event, $item, $options=array(), $label='') {
      $notification        = new PluginTelegrambotNotification();
      $notificationtarget  = NotificationTarget::getInstance($item, $event, $options);

      if (!$notificationtarget) {
         return false;
      }

      $entity = $notificationtarget->getEntity();

      // For each notification
      foreach(Notification::getNotificationsByEventAndType($event, $item->getType(), $entity) as $data) {
         if($data['mode'] == 'telegram') {
            $targets = getAllDatasFromTable(
               'glpi_notificationtargets',
               'notifications_id = ' . $data['id']
            );

            $notificationtarget->clearAddressesList();

            // For each target
            foreach($targets as $target) {
               // Get all users affected by this notification
               $notificationtarget->getAddressesByTarget($target, $options);

               foreach ($notificationtarget->getTargets() as $user_email => $users_infos) {
                  $ticket_id  = $item->fields['id'];        // temp
                  $username   = $users_infos['username'];   // temp
                  $date_mod   = $item->fields['date_mod'];  // temp

                  $datas['user_id']       = isset($users_infos['users_id']) ? $users_infos['users_id'] : null;
                  $datas['item_type']     = $item->getType();
                  $datas['item_id']       = $item->fields['id'];
                  $datas['template_id']   = $data['notificationtemplates_id'];
                  $datas['date_mod']      = $item->fields['date_mod'];
                  $datas['message']       = "Ticket: $ticket_id\nEvent: $event\nDate: $date_mod\nUser: $username";
                  
                  $notification->sendNotification($datas);
               }
            }
         }
      }
   }

}

?>