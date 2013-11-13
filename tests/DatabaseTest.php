<?php

require_once ("../app/Database.php");
require_once ("PHPUnit.php");
require_once ("DefaultsFactory.php");

class DatabaseTest extends PHPUnit_Framework_TestCase {
   protected $db;
   protected $avail1;
   protected $hostel1;

   protected function setUp() {
      #$this->db = new MemoryDatabase("test_data.xml");
      $this->db = init_database();
      $this->db->init();
      $this->hostel1 = $this->db->add_hostel("Hostel 21", default_address(),
         default_contact(), default_restrictions());
   }

   public function testHostelAdded() {
      $this->assertFalse($this->hostel1 == null);
   }

   public function testSearchEmpty() {
      $ret = $this->db->search_availability(null);
      $this->assertEmpty($ret);
      $ret = $this->db->search_reservation(null);
      $this->assertEmpty($ret);
   }

   public function testAddAvailability() {
      $avail = $this->db->add_availability("Hostel 21", "20131111", 1, 4, 25);
      $this->assertFalse($avail == null);
      $this->assertTrue($avail->free_space() == 4);
   }

   public function testSearchAvail() {
      $this->db->add_availability("Hostel 21", "20131111", 1, 4, 25);
      $search = array("start_date" => "20131111",
                      "end_date" => "20131111",
                      "num" => 1,
                      "city" => null);
      $results = $this->db->search_availability($search);
      $this->assertCount(1, $results);
   }

   public function testSearchAvailMult() {
      $this->db->add_availability("Hostel 21", "20131111", 1, 4, 25);
      $this->db->add_availability("Hostel 21", "20131111", 2, 2, 25);
      $search = array("start_date" => "20131111",
                      "end_date" => "20131111",
                      "num" => 1,
                      "city" => null);
      $results = $this->db->search_availability($search);
      $this->assertCount(2, $results);
   }

   public function testUpdateAvail() {
      //$this->db->add_availability($this->avail1);
      //$avail2 = new Availability(1, "20131111", 3, 25, $this->hostel1);
      //$this->db->update_availability($avail2);
      //$search = array("start_date" => "20131111",
       //               "end_date" => "20131111",
        //              "num" => 1,
         //             "city" => null);
      //$results = $this->db->search_availability($search);
      //foreach ($results as $r) {
       //  if ($r->equals($this->avail1))
        //    $this->assertEquals(3, $r->free_space());
      //}
   }

}

?>
