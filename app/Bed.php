<?php
require_once("Hostel.php");
class Bed {
   protected $hostel;
   protected $bed_number;
   protected $room_number;
   protected $number_beds;
   protected $rate;
   protected $availablity = true;

   public function __construct($hostel_id, $room_num) {
      $hostel = $hostel_id;
      $room_number = $room_num;
   }

   public function book_bed() {
      $availability = false;
   }

   public function free_bed() {
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
