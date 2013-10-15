<?php
require_once("Hostel.php");

// Don't need most of the variables,
// most of it should be in SQL
class Room {
   protected $hostel;
   protected $room_number;
   protected $number_beds;
   protected $rate;
   protected $availablity = true;

   public function __construct($hostel_id, $room_num) {
      $hostel = $hostel_id;
      $room_number = $room_num;
   }

   public function book_room() {
      $availability = false;
   }

   public function free_room() {
      $availability = true;
   }

   public function is_free() {
      return $availability;
   }

   public function get_num_beds() {
      return $number_beds;
   }

}
?>
