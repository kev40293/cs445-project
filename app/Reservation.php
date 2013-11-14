<?php
require_once("Customer.php");
require_once("Date.php");
require_once("Availability.php");

class Reservation {
   private $res_id;
   private $availability;
   private $customer;
   private $date;
   private $hostel;
   private $room;
   private $quantity;

   public function __construct() {
      $this->bookings = array();
   }

   public function add_availability($avail, $qty) {
      $this->bookings[] = array($avail, $qty, false);
   }


   public function bed_list() {
      // Create a list of beds from the avail/num
      $blist = array();
      foreach ($this->bookings as $record) {
         if ($record[2])
            $blist[$record[0]->get_date()][] = array($record[0]->get_room(), $record[1], $record[0]->get_hostel());
      }
      return $blist;
   }

   public function book($cust_id) {
      $this->customer = $cust_id;
      foreach ($this->bookings as &$b) {
         if (!$b[2]){
            $b[0]->reserve($b[1]);
            $b[2] = true;
         }
      }
      $db = open_database();
      $this->res_id = $db->record_reservation($cust_id, $this);
      return $this->res_id;
   }

   public function cancel() {
      foreach ($this->bookings as &$b) {
         if ($b[2]) {
            $b[0]->add_bed($b[1]);
            $b[2] = false;
         }
      }
      $db = open_database();
      $db->delete_reservation($this->res_id);
   }

   public function get_cost() {
      $cost = 0;
      foreach ($this->bookings as $b) {
         $cost += $b[0]->get_price() * $b[1];
      }
      return $cost;
   }

}
?>
