<?php

require_once ("../app/Database.php");
require_once ("PHPUnit.php");
class MemDBTest extends PHPUnit_Framework_TestCase {
   protected $db;
   protected $avail1;
   protected $hostel1;

   protected function setUp() {
      $this->db = new MemoryDatabase("test_data.xml");
      $this->hostel1 = new Hostel("Hostel 21", "", "", "");
      $this->avail1 = new Availability(1, "20131111", 2, 25, null);
   }

   public function testSearchEmpty() {
      $ret = $this->db->search_availability(null);
      $this->assertEmpty($ret);
      $ret = $this->db->search_reservation(null);
      $this->assertEmpty($ret);
   }

   public function testSearchAvail() {
      $this->db->add_availability($this->avail1);
      $search = array("start_date" => "20131111",
                      "end_date" => "20131111",
                      "num" => 1,
                      "city" => null);
      $results = $this->db->search_availability($search);
      $this->assertGreaterThan(0, $results);
      $this->assertContains($this->avail1, $results);
   }

   public function testUpdate() {
   }

}

?>
