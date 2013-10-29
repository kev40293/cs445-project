<?php

interface DatabaseInterface {
   public function search_beds($parameters);
   public function search_reservations($parameters);
   public function record_reservation($resv);
   public function delete_reservation($resv);
   public function add_customer($cust);
}

require_once("Bed.php");
require_once("Hostel.php");
require_once("Reservation.php");

class MemoryDatabase implements DatabaseInterface {
   protected $hostels;
   protected $beds;
   protected $reservations;
   protected $customer;

   // fix the constructor to read from the XML file
   public function __construct($init_beds) {
      $this->beds = $init_beds;
      $this->reservations = array();
      $this->customers = array();
   }
   // City, start date, end date/numdays
   public function search_beds($parameters) {
      $hst = array();
      $results = array();
      if (exists($parameters["city"])) {
         // get appropriate hostel
      }
      else {
         $hst = $this->hostels;
      }
      foreach ($hst as $host) {
         // append available beds
      }
      return $results;
   }

   public function search_reservations($parameters){
      return $this->reservations;
   }

   public function record_reservation($resv){
      $this->reservations[] = $resv;
   }
   public function delete_reservation($resv) {
      $new_arr = array();
      foreach ($this->reservation as $r) {
         if ($r != $resv)
            $new_arr[] = $r;
      }
      $this->reservations = $new_arr;
   }
   public function add_customer($cust){
      $this->customers[] = $cust;
   }
}

?>
