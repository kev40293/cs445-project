<?php

require_once ("../app/Database.php");
require_once ("PHPUnit.php");
class MemDBTest extends PHPUnit_Framework_TestCase {
   protected $db;
   protected $avail1;
   protected $hostel1;

   protected function setUp() {
      $this->db = new MemoryDatabase("test_data.xml");
      $address = array("city" => "Chicago");
      $this->hostel1 = new Hostel("Hostel 21", $address, "", "");
      $this->avail1 = new Availability(1, "20131111", 2, 25, $this->hostel1);
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
   }

   public function testUpdateAvail() {
      $this->db->add_availability($this->avail1);
      $avail2 = new Availability(1, "20131111", 3, 25, $this->hostel1);
      $this->db->update_availability($avail2);
      $search = array("start_date" => "20131111",
                      "end_date" => "20131111",
                      "num" => 1,
                      "city" => null);
      $results = $this->db->search_availability($search);
      foreach ($results as $r) {
         if ($r->equals($this->avail1))
            $this->assertEquals(3, $r->free_space());
      }
   }

}

?>
