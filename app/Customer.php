<?php

class Customer {
   protected $customer_id;

   public function __construct($cid = 1){
      $this->customer_id = $cid;
   }

   public function get_id() {return $this->customer_id; }

   public function make_reservation($availability_id, $qty=1) {
      $db = open_database();
      $space = $db->get_available_space($availability_id);
      if ($qty <= $space) {
         // Add money to hostel
         return $db->make_reservation($this->customer_id, $availability_id, $qty);
      }
      return -1;
   }

   public function cancel_reservation($reservation_id) {
      // Cancelation policy handled here
      // To determine if money should be subtracted
      $db = open_database();
      $db->delete_reservation($reservation_id);
   }

   public function get_reservation($reservation_id) {
      // Give Back a reservation object
   }

}
?>
