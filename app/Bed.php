<?php
require_once("Hostel.php");
class Bed {
   protected $room_number;
   protected $rate;
   protected $isAvailable = true;

   public function __construct($room_num) {
      $this->room_number = $room_num;
   }

   public function book() {
      $this->isAvailable = false;
   }

   public function free() {
      $this->isAvailable = true;
   }

   public function is_free() {
      return $this->isAvailable;
   }

}
?>
