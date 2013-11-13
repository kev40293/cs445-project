<?php
require_once("Customer.php");
require_once("Date.php");
require_once("Availability.php");
class Reservation {
   protected $res_id;
   protected $beds = array();
   protected $customer;

   public function __construct($id, $cust) {
      $this->customer = $cust;
      $this->res_id = $id;
   }

   public function add_bed($bed, $start_date, $num_days = 1) {
      $dates = BookingDate::date_range($start_date, $num_days);
      foreach ($dates as $date){
         $bed->book($date);
         $this->beds[$date][] = $bed;
      }
   }

   public function get_bookings() {
      return $this->beds;
   }

   public function cancel() {
      foreach ($this->beds as $date => $bed_list) {
         foreach ($bed_list as $bed) {
            $bed->free($date);
         }
      }
      $this->beds = array();
   }

   public function calculate_cost() {
      $sum = 0;
      foreach ($this->beds as $date => $bed_list) {
         foreach ($bed_list as $bed) {
            $sum += $bed->get_rate();
         }
      }
      return $sum;
   }

}
?>
