<?php

require_once("PHPUnit.php");
require_once("../app/Hostel.php");

class Check_AvailabilitiesTest extends PHPUnit_Framework_TestCase {
   protected $hostel;
   protected $bed_one;
   protected $bed_two;

   protected function setUp(){
      $this->bed_one = new Bed(1);
      $this->bed_two = new Bed(2);

      $this->hostel = new Hostel(array($this->bed_one, $this->bed_two));
   }

   public function testNoAvailabilities() {
      $this->bed_one->book();
      $this->bed_two->book();

      $avail = $this->hostel->get_available_rooms();
      $this->assertEquals(sizeof($avail), 0);

   }

   public function testReturnAvailable() {
      $this->bed_one->book();

      $avail = $this->hostel->get_available_rooms();
      $this->assertEquals(sizeof($avail), 1);
      $this->assertEquals($this->bed_two, $avail[0]);
   }

   public function testMultipleAvailable() {
      $avail = $this->hostel->get_available_rooms();
      $this->assertGreaterThan(1, sizeof($avail));
   }

}

?>
