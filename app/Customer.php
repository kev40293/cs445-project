<?php
require_once("Database.php");

class Customer {
   protected $customer_id;

   public function __construct($cid = 1){
      $this->customer_id = $cid;
   }

   public function get_id() {return $this->customer_id; }

   public function make_reservation($availability_id, $qty, $bid=0) {
      $db = open_database();
      $space = $db->get_available_space($availability_id);
      if ($qty <= $space) {
         // Add money to hostel
         return $db->make_reservation($this->customer_id, $availability_id, $qty, $bid);
      }
      return -1;
   }

   public function cancel_reservation($reservation_id) {
      // Cancelation policy handled here
      // To determine if money should be subtracted
      $db = open_database();
      $db->delete_reservation($this->customer_id, $reservation_id);
   }

   public function get_reservation_info($reservation_id) {
      $db = open_database();
      $res_info = $db->get_reservation($reservation_id);
      if ($res_info == null)
         return null;
      foreach ($res_info["bookings"] as $record) {
         if (!isset( $res_info["hostel"][$record["hostel"]][$record["date"]]))
            $res_info["hostel"][$record["hostel"]][$record["date"]] = 0;
         $res_info["hostel"][$record["hostel"]][$record["date"]] += $record["qty"];
      }

      // Give Back a reservation object
      return $res_info;
   }

}
?>
