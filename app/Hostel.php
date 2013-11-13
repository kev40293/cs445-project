<?php

require_once("Availability.php");

class Hostel {
   protected $name;
   protected $address;
   protected $contact;
   protected $restrictions;
   protected $availabilities;
   protected $beds = array();

   public function __construct($n, $add, $cont, $rest) {
      $this->name = $n;
      $this->address = $add;
      $this->contact = $cont;
      $this->restrictions = $rest;
   }

   public function get_name() {return $this->name; }

   public function get_city() {
      return $this->address["city"];
   }

   public function get_available_beds($date, $enddate = null) {
      if ($enddate == null) {
         $enddate = $date;
      }
      $free_beds = array();
      foreach ($this->beds as $bed) {
         if ($bed->is_free($date, $enddate)){
            array_push($free_beds, $bed);
         }
      }
      return $free_beds;;
   }

   public function get_availabilities($date, $enddate = null, $num = 1) {
      if ($enddate == null) {
         $enddate = $date;
      }
      $results = array();
      $rooms = array();
      foreach (BookingDate::dates_from_range($date, $enddate) as $d) {
         if (isset($this->availabilities[$d])) {
            foreach($this->availabilities[$d] as $av) {
               if ($av->free_space() >= $num)
                  $rooms[$av->room()][] = $av;
            }
         }
         else
            return array();
      }
      return $this->availabilities[$date];
   }

   public function add_availability($date, $room, $nbeds, $price) {
      $this->availabilities[$date] =
         new Availability($room, $date, $nbeds, $price, $this);
   }

   public function equals($host) {
      return $this->name == $host->get_name();
   }
}

?>
