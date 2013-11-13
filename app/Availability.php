<?php

require_once("Date.php");
require_once("Exceptions.php");

class Availability {
   protected $room_number;
   protected $rate;
   protected $quantity;
   protected $date;
   protected $hostel;

   public function __construct($rn, $d, $num, $price, $h){
      $this->room_number = $rn;
      $this->date = $d;
      $this->quantity = $num;
      $this->rate = $price;
      $this->hostel = $h;
   }

   public function get_date() { return $this->date; }
   public function get_room() { return $this->room_number; }
   public function get_hostel() { return $this->hostel->get_name(); }

   public function get_price() {
      return $this->rate;
   }

   public function add_bed($num = 1) {
      $this->quantity += $num;
   }

   public function reserve($num = 1) {
      if ($num > $this->quantity) {
         throw new NoMoreSpaceException();
      }
      $this->quantity -= $num;
   }

   public function free_space() {
      return $this->quantity;
   }

   public function equals($avail) {
      return $this->room_number == $avail->room_number and
         $this->hostel->equals($avail->hostel);
   }

   public function matches($sparam) {
      $sdates = BookingDate::dates_from_range($sparam["start_date"], $sparam["end_date"]);
      if ($this->hostel != null)
         if ($this->hostel->get_city() != $sparam["city"])
            return false;
      return in_array($this->date, $sdates) and $this->quantity >= $sparam["num"];
   }

}

?>
