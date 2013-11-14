<?php
require_once("Reservation.php");

class Customer {
   protected $customer_id;

   public function __construct($cid = 1){
      $this->customer_id = $cid;
   }

   public function get_id() {return $this->customer_id; }

   public function make_reservation($availability_id) {
      // Create the reservation

   }

   public function cancel_reservation($reservation_id) {
      // Cancelation policy handled here
   }

   public function get_reservation($reservation_id) {
      // Give Back a reservation object
   }

}
?>
