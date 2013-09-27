<?php
require_once("Hostel.php");
class Room {
   protected $hostel;
   protected $room_number;
   protected $number_beds;
   protected $rate;
   protected $availablity = true;

   public function book_room() {
      $availability = false;
   }

   public function free_room() {
      $availability = true;
   }

   public function is_free() {
      return $availability;
   }

}
?>
