<?php

require_once("Bed.php");

class Hostel {
   protected $name;
   protected $address;
   protected $contact;
   protected $restrictions;
   protected $availabilities;
   protected $beds = array();

   public function __construct($bed_list) {
      $this->beds = $bed_list;
   }

   public function get_available_beds($date, $enddate = null) {
      if ($enddate == null) {
         $enddate = $date;
      }
      $free_beds = array();
      foreach ($this->beds as $bed) {
         if ($bed->is_free($date, $enddate)){
            array_push($free_beds, $bed);
         }
      }
      return $free_beds;;
   }

   public function search() {
   }

}

?>
