<?php

interface DatabaseInterface {
   public function search_beds($parameters);
   public function search_reservations($parameters);
}

require_once("Bed.php");

class MemoryDatabase implements DatabaseInterface {
   protected $beds;
   protected $reservations;
   public function __construct($init_beds) {
      $this->beds = $init_beds;
   }
   public function search_beds($parameters) {
      return null;
   }
   public function search_reservations($parameters){
   }
}

?>
