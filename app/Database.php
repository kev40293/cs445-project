<?php

interface DatabaseInterface {
   public function init();
   public function open();

   public function add_customer($fname, $lname, $email, $cc_info);
   public function update_customer($cust, $options);
   public function get_customer_info($cust_id);

   public function add_availability($hostel, $rn, $d, $num, $price);
   public function get_available_space($avail_id);
   public function search_availability($param);

   public function make_reservation($cust_id, $avail, $num);
   public function delete_reservation($cust_id, $resv_id);
   public function get_reservation($res_id);

   public function get_hostels($param);
   public function add_hostel($name, $address, $contact, $restrict);

   public function get_revenue();
   public function get_occupancy();
}

require_once("Hostel.php");

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
