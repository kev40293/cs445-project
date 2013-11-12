<?php

interface DatabaseInterface {
   public function search_beds($parameters);
   public function search_reservations($parameters);
   public function record_reservation($resv);
   public function delete_reservation($resv);
   public function add_customer($cust);
}

require_once("Bed.php");
require_once("Hostel.php");
require_once("Reservation.php");

class MemoryDatabase implements DatabaseInterface {
   protected $hostels;
   protected $reservations;
   protected $customer;

   // fix the constructor to read from the XML file
   public function __construct($xml_file) {
      $this->reservations = array();
      $this->customers = array();
      $this->hostels = array();
   }

   public function add_hostel_from_xml($xml_file) {
      // Add availabilited from the xml file
      $new_hostel = simplexml_load_file($xml_file);
      $hostel = new Hostel();
      foreach ($new_hostel->availability as $avail){
         $hostel->add_availability((string)$avail->date,
            (int) $avail->room, (int) $avail->bed, (int) $avail->price);
      }
   }

   // City, start date, end date/numdays
   public function search_beds($args) {
      $hst = array();
      $results = array();
      $hst[] = get_hostel_by_city($args["city"]);
      return get_available_beds_in_hostels($hst,
         $args['start_date'], $args['end_date']);
   }

   public function get_availablities_in_hostels($hostel, $start, $end) {
      $results = array();
      foreach ($hst as $host) {
         $res = $host->get_available_beds($args["start_date"], $args["end_date"]);
         array_merge($results, $res);
      }
      return $results;
   }

   private function get_hostel_by_city($city = null) {
      $hst = array();
      foreach ($this->hostels as $host) {
         if ($host->getAddress()->city == $city) {
            $hst[] = $host;
         }
      }
      return $hst;
   }

   public function search_reservations($parameters){
      return $this->reservations;
   }

   public function record_reservation($resv){
      $this->reservations[] = $resv;
   }
   public function delete_reservation($resv) {
      $new_arr = array();
      foreach ($this->reservation as $r) {
         if ($r != $resv)
            $new_arr[] = $r;
      }
      $this->reservations = $new_arr;
   }
   public function add_customer($cust){
      $this->customers[] = $cust;
   }
}

?>
