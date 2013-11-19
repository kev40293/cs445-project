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
         $db->pay_for_availability($availability_id, $qty);
         return $db->make_reservation($this->customer_id, $availability_id, $qty, $bid);
      }
      return -1;
   }

   public function cancel_reservation($reservation_id) {
      $db = open_database();
      $res_info = $db->get_reservation($reservation_id);
      foreach ($res_info['bookings'] as $hostel => $resv){
         // Cancelation policy handled here
         // To determine if money should be subtracted
         $rest = $db->get_hostel_restrictions($resv['hostel']);
         if ($this->is_after_deadline($resv, $rest)){
            $db->refund_availability($resv['id'], $resv['qty'],
               $rest['cancellation_penalty']/100);
         }
         else{
            $db->refund_availability($resv['id'], $resv['qty']);
         }
      }
      $db->delete_reservation($this->customer_id, $reservation_id);
   }

   private function is_after_deadline($resv, $rest){
      if ($rest['cancellation_deadline'] == 0)
         return false;
      $current_time = new DateTime("now", new DateTimeZone('UTC'));
      $format = $rest['check_in_time'] == "" ? "Ymd" : "YmdH:i";
      $checkin_time = DateTime::createFromFormat($format,
         $resv['date'] . $rest['check_in_time'],
         new DateTimeZone('UTC'));
      $deadline = $checkin_time->sub(
         DateInterval::createFromDateString($rest['cancellation_deadline'] . ' hours'));
      return $current_time > $deadline;
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
