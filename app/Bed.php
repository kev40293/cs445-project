<?php
require_once("Hostel.php");
class Bed {
   protected $room_number;
   protected $rate;
   protected $availabilities;

   public function __construct($room_num, $dates) {
      $this->room_number = $room_num;
      foreach ($dates as $date) {
         $this->availabilities[$date] = true;
      }
   }

   public function book($date) {
      $this->availabilities[$date] = false;
   }

   public function free($date) {
      $this->availabilities[$date] = true;
   }

   public function free_dates() {
      $free = array();
      foreach ($this->availabilities as $date => $status) {
         if ($status) {
            $free[] = $date;
         }
      }
      return $free;
   }

   public function is_free($date) {
      return $this->availabilities[$date];
   }

}
?>
