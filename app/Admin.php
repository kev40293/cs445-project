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

   public function create_user($fname, $lname, $email, $optional=array()){
      $cc_options = array();
      foreach (array("cc_number", "expiration_date", "security_code", "phone") as $oparg){
         if (! isset($optional[$oparg]))
            $optional[$oparg] = null;
         else
            $cc_options = $optional[$oparg];
      }
      $db = open_database();
      $cust = $db->add_customer($fname, $lname, $email, $cc_options);
      return $cust->get_id();
   }

   public function change_user($user_id, $options=array()){
      $db = open_database();
      $db->update_customer($user_id, $options);
   }

   public function get_user_info($cid) {
      $db = open_database();
      return $db->get_customer_info($cid);
   }

   public function get_revenue() {
      return 0;
   }

   public function get_occupancy() {
      return 0;
   }
}

?>
