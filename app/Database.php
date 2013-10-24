<?php

interface DatabaseInterface {
   public function search_beds($parameters);
   public function search_reservations($parameters);
   public function record_reservation($resv);
   public function delete_reservation($resv);
   public function add_customer($cust);
}

require_once("Bed.php");

class MemoryDatabase implements DatabaseInterface {
   protected $beds;
   protected $reservations;
   protected $customer;
   public function __construct($init_beds) {
      $this->beds = $init_beds;
      $this->reservations = array();
      $this->customers = array();
   }
   public function search_beds($parameters) {
      return null;
   }

   public function search_reservations($parameters){
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
