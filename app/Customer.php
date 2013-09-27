<?php
require_once("Reservation.php");

class Customer {
   protected $customer_id;

   public function make_reservation($location, $num_rooms, $num_beds) {
   }

   public function cancel_reservation($reservation_id) {
   }

   public function get_reservations() {
      return null;
   }

}
?>
