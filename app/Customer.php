<?php
require_once("Reservation.php");

class Customer {
   protected $customer_id;

   public function __construct($cid = 1){
      $this->customer_id = $cid;
   }

   public function make_reservation($avail, $num_beds) {
      $db = open_database();
      $avail->reserve(2);
      return $rid;
   }

   public function cancel_reservation($reservation_id) {
   }

   public function get_reservations() {
      return null;
   }

}
?>
