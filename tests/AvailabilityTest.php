<?php

require_once("../app/Availability.php");

class AvailibilityTest extends PHPunit_Framework_TestCase {
   protected $avail;
   protected $hostel;

   public function setUp() {
      $address = array("city" => "Chicago");
      $this->hostel = new Hostel("H21", $address,"","");
      $this->avail = new Availability(1, "20131010", 1, 25, $this->hostel);
      $this->sparam = array("start_date" => "20131010",
         "end_date" => "20131010",
         "city" => null,
         "num" => 0);
   }

   public function testReserve() {
      $this->avail->reserve();
      $this->assertEquals(0, $this->avail->free_space());
   }

   public function addSpace() {
      $this->avail->add_bed();
      $this->assertEquals(2, $this->avail->free_space());
      $this->avail->add_bed(2);
      $this->assertEquals(4, $this->avail->free_space());
   }

   public function testReserveMultiple() {
      $this->avail->add_bed();
      $this->avail->reserve(2);
      $this->assertEquals(0, $this->avail->free_space());
   }

   public function testReserveFailed() {
      $this->setExpectedException('NoMoreSpaceException');
      $this->avail->reserve(2);
   }

   public function testEqual() {
      $avail2 = new Availability(1, "20131010", 1, 25, $this->hostel);
      $this->assertTrue($this->avail->equals($avail2));
   }
   public function testEqualDifferentQuantity() {
      $avail2 = new Availability(1, "20131010", 4, 25, $this->hostel);
      $this->assertTrue($this->avail->equals($avail2));
   }
   public function testEqualDifferntPrice() {
      $avail2 = new Availability(1, "20131010", 1, 45, $this->hostel);
      $this->assertTrue($this->avail->equals($avail2));
   }

   public function testNotEqualDate() {
      $avail2 = new Availability(1, "20131011", 1, 25, $this->hostel);
      $this->assertFalse($this->avail->equals($avail2));
   }
   public function testNotEqualRoom() {
      $avail2 = new Availability(2, "20131010", 1, 25, $this->hostel);
      $this->assertFalse($this->avail->equals($avail2));
   }
   public function testNotEqualHostel() {
      $hostel2 = new Hostel("H22", "","","");
      $avail2 = new Availability(1, "20131010", 1, 25, $hostel2);
      $this->assertFalse($this->avail->equals($avail2));
   }

   public function testMatchDateRange() {
      $this->sparam["start_date"] = "20131002";
      $this->sparam["end_date"] = "20131022";
      $this->assertTrue($this->avail->matches($this->sparam));
   }
   public function testMatchCity() {
      $this->sparam["city"] = "Chicago";
      $this->assertTrue($this->avail->matches($this->sparam));
   }
   public function testMatchQty() {
      $this->sparam["num"] = 1;
      $this->assertTrue($this->avail->matches($this->sparam));
   }
   public function testFailQty() {
      $this->sparam["num"] = 90;
      $this->assertFalse($this->avail->matches($this->sparam));
   }

   public function testMatchDateQty() {
      $this->assertTrue($this->avail->matches($this->sparam));
   }

}

?>
