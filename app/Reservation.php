<?php
require_once("Bed.php");
require_once("Customer.php");
class Reservation {
   protected $beds;
   protected $customer;

   public function __construct($cust) {
      $this->customer = $cust;
   }

   public function add_bed($bed, $date) {
      if ($bed->is_free($date)){
         $bed->book($date);
         $this->beds[$date][] = $bed;
      }
      else
         throw new InvalidArgumentException("Bed not free");
   }

   public function get_beds() {
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
}
?>
