<?php
require_once("Hostel.php");
require_once("Database.php");

class Admin {

   public function load_hostel($xml_file) {
      $db = open_database();
      $hostel_xml = simplexml_load_file($xml_file);
      $hostel = $db->add_hostel(
         (string) $hostel_xml->name,
         $hostel_xml->address,
         $hostel_xml->contact,
         $hostel_xml->restrictions);

      foreach ($hostel_xml->availability as $available) {
         $hostel->add_availability(
            $available->date,
            $available->room,
            $available->bed,
            $available->price,
            $hostel
         );
      }

   }

   public function get_revenue() {
      return 0;
   }

   public function get_occupancy() {
      return 0;
   }
}

?>
