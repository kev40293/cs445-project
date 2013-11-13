<?php

interface DatabaseInterface {
   public function add_customer($cust);
   public function update_customer($cust);

   public function add_availability($avail);
   public function update_availability($avail);
   public function search_availability($param);

   public function add_reservation($resv);
   public function update_reservation($resv);
   public function search_reservation($param);

   public function get_hostel_policy($hostel_name);
}

require_once("Hostel.php");
require_once("Reservation.php");

class MemoryDatabase implements DatabaseInterface {
   private $hostels = array();
   private $availabilities = array();
   private $reservations = array();
   private $customers = array();

   public function add_customer($cust){}
   public function update_customer($cust){
      foreach ($this->customers as &$c) {
         if ($c->equals($cust)) {
            $c = $cust;
         }
      }
   }

   public function add_availability($avail){
      $this->availabilities[] = $avail;
   }
   public function update_availability($avail){
      foreach ($this->availabilities as &$av) {
         if ($av->equals($avail)) {
            $av = $avail;
         }
      }
   }
   public function search_availability($params){
      $matches = array();
      foreach ($this->availabilities as $avail) {
         if ($avail->matches($params)) {
            $matches[] = $avail;
         }
      }
      return $matches;
   }

   public function add_reservation($resv){
      $this->reservations[] = $resv;
   }
   public function update_reservation($resv){
      foreach ($this->reservation as &$res) {
         if ($res->equals($resv)) {
            $res = $resv;
         }
      }
   }
   public function search_reservation($params){
      $matches = array();
      foreach ($this->reservations as $resv) {
         if ($this->match_search_availability($params, $resv)) {
            $matches[] = $resv;
         }
      }
      return $matches;
   }
   private function match_search_reservation($params, $resv) {
      return true;
   }

   public function get_hostel_policy($hostel_name){}
}

?>
