<?php

interface DatabaseInterface {
   public function init();
   public function open();

   public function add_customer($cust);
   public function update_customer($cust);

   public function add_availability($hostel, $rn, $d, $num, $price);
   public function update_availability($hostel, $room, $date, $num, $price);
   public function search_availability($param);

   public function record_reservation($cust_id, $resv);
   public function delete_reservation($resv_id);
   public function update_reservation($resv_id, $resv);
   public function search_reservation($param);

   public function get_hostels($param);
   public function add_hostel($name, $address, $contact, $restrict);
}

require_once("Hostel.php");
require_once("Reservation.php");

class MemoryDatabase {
   private $hostels = array();
   private $availabilities = array();
   private $reservations = array();
   private $customers = array();

   public function add_customer($cust){
      $this->customers[] = $cust;
      $this->persist();
   }
   public function update_customer($cust){
      foreach ($this->customers as &$c) {
         if ($c->equals($cust)) {
            $c = $cust;
         }
      }
      $this->persist();
   }

   public function add_availability($avail){
      $this->availabilities[] = $avail;
      $this->persist();
   }
   public function update_availability($avail){
      foreach ($this->availabilities as &$av) {
         if ($av->equals($avail)) {
            $av = $avail;
         }
      }
      $this->persist();
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
      $this->persist();
   }
   public function update_reservation($resv){
      foreach ($this->reservation as &$res) {
         if ($res->equals($resv)) {
            $res = $resv;
         }
      }
      $this->persist();
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

   public function add_hostel($new_hostel) {
      $this->hostels[] = $new_hostel;
      $this->persist();
   }

   public function get_hostels($param) {
      return $this->hostels;
   }

   private function persist() {
   }
}

require_once ("XML_Database.php");

function open_database(){
   $db = new XML_Database("db.xml");
   $db->open();
   return $db;
}

function init_database(){
   $db = new XML_Database("db.xml");
   $db->init();
   return $db;
}

?>
