<?php
require_once("Hostel.php");
class Bed {
   protected $hostel;
   protected $bed_number;
   protected $room_number;
   protected $number_beds;
   protected $rate;
   protected $availablity = true;

   public function book_bed() {
      $availability = false;
   }

   public function free_bed() {
      $availability = true;
   }

   public function is_free() {
      return $availability;
   }

}
?>
