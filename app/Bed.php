<?php
require_once("Hostel.php");
require_once("Exceptions.php");
require_once("Date.php");
class Bed {
   protected $room_number;
   protected $rate;
   protected $availabilities;

   public function __construct($room_num, $dates, $rate) {
      $this->room_number = $room_num;
      $this->add_dates($dates);
      $this->rate = $rate;
   }

   public function add_dates($dates) {
      foreach ($dates as $date) {
         $this->availabilities[$date] = true;
      }
   }

   public function book($date) {
      if ($this->availabilities[$date])
         $this->availabilities[$date] = false;
      else
         throw new IllegalBookingException();
   }

   //public function book_range($sdate, $edate) {
   //   foreach (BookingDate::date_range($sdate, $edate) as $date){
   //      $this->book($date);
   //   }
   //}

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

   public function get_rate() {
      return $this->rate;
   }

}
?>
