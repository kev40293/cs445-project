<?php
require_once("Bed.php");
require_once("Customer.php");
class Reservation {
   protected $beds;
   protected $customer;

   public function __construct($cust) {
      $this->customer = $cust;
   }

   public function add_bed($bed) {
      if ($bed->is_free()){
         $bed->book();
         $this->beds[] = $bed;
      }
      else
         throw new InvalidArgumentException("Bed not free");
   }

   public function get_beds() {
      return $this->beds;
   }

   public function cancel() {
      foreach ($this->beds as $bed) {
         $bed->free();
      }
   }
}
?>
