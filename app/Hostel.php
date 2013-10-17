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

   // we should replace rooms and just use the database schema
   public function get_available_rooms() {
      $free_beds = array();
      foreach ($this->beds as $bed) {
         if ($bed->is_free()){
            array_push($free_beds, $bed);
            //$free_beds[] = $bed;
         }
      }
      return $free_beds;;
   }

   public function search() {
   }

}

?>
