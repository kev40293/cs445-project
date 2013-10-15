<?php
require_once("Room.php");
class Reservation {
   protected $rooms;

   public function cancel() {
      foreach ($rooms as $room) {
         $room.free_room();
      }
   }
}
?>
