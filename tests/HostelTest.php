<?php

require_once("PHPUnit.php");
require_once("../app/Hostel.php");
require_once("../app/Database.php");
require_once("DefaultsFactory.php");

class Check_AvailabilitiesTest extends PHPUnit_Framework_TestCase {
   protected $hostel;
   protected $date1;
   protected $date2;
   protected $rate1;
   protected $init_dates;

   protected function setUp(){
      $db = init_database();
      $this->hostel = $db->add_hostel("Hostel 21",
         default_address(), default_contact(), default_restrictions());
   }

   public function testAddAvailability() {
      $this->hostel->add_availability("20131111", 1,3,25);
      $search = search_object();
      $search["start_date"] = "20131111";
      $search["end_date"] = "20131111";
      $db = open_database();
      $res = $db->search_availability($search);
      $this->assertCount(1, $res);
   }
}

?>
