<?php

require_once ("../app/Database.php");
require_once ("PHPUnit.php");
class MemDBTest extends PHPUnit_Framework_TestCase {
   protected $db;
   protected function setUp() {
      $this->db = new MemoryDatabase("test_data.xml");
   }

   public function testCreate() {
      $this->assertInstanceOf('MemoryDatabase', $this->db);
   }

   public function testAddFromXML() {
      $this->db->add_hostel_from_xml("hostel-20130818.xml");
   }

}

?>
