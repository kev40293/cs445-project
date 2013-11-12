<?php

require_once("PHPUnit.php");
require_once("../app/Hostel.php");

class Check_AvailabilitiesTest extends PHPUnit_Framework_TestCase {
   protected $hostel;
   protected $date1;
   protected $date2;
   protected $rate1;
   protected $init_dates;
   protected $bed_one;
   protected $bed_two;

   protected function setUp(){
      $this->date1 = "20131010";
      $this->date2 = "20131011";
      $this->rate1 = "25";
      $this->init_dates = array($this->date1, $this->date2);
      $this->bed_one = new Bed(1, $this->init_dates, $this->rate1);
      $this->bed_two = new Bed(2, $this->init_dates, $this->rate1);

      $this->hostel = new Hostel(array($this->bed_one, $this->bed_two));
   }

   public function testNoAvailabilities() {
      $this->bed_one->book($this->date1);
      $this->bed_two->book($this->date1);

      $avail = $this->hostel->get_available_beds($this->date1);
      $this->assertEquals(sizeof($avail), 0);

   }

   public function testReturnAvailable() {
      $this->bed_one->book($this->date1);

      $avail = $this->hostel->get_available_beds($this->date1);
      $this->assertEquals(sizeof($avail), 1);
      $this->assertEquals($this->bed_two, $avail[0]);
   }

   public function testMultipleAvailable() {
      $avail = $this->hostel->get_available_beds($this->date1);
      $this->assertGreaterThan(1, sizeof($avail));
   }

   public function testMultipleDates() {
      $avail = $this->hostel->get_available_beds($this->date1, $this->date2);
      $this->assertGreaterThan(1, sizeof($avail));
   }

   public function testMultipleDatesSomeBooked() {
      $avail = $this->hostel->get_available_beds($this->date1, $this->date2);
      $origCount = sizeof($avail);
      $this->bed_two->book($this->date2);
      $avail = $this->hostel->get_available_beds($this->date1, $this->date2);
      $this->assertEquals($origCount -1, sizeof($avail));
   }

   public function testAddAvailability() {
      $this->hostel->add_availability($this->date1, 1, 1, 25);
      $avail = $this->hostel->get_availabilities($this->date1);
      $this->assertEquals(1, sizeof($avail));
   }

   public function testGetAvailabilitiesNone() {
      $this->hostel->add_availability($this->date1, 1, 1, 25);
      $avail = $this->hostel->get_availabilities($this->date2);
      $this->assertEquals(0, sizeof($avail));
   }

   public function testGetAvailabilitiesNotEnoughSpace() {
      $this->hostel->add_availability($this->date1, 1, 1, 25);
      $avail = $this->hostel->get_availabilities($this->date1, $num = 2);
      $this->assertEquals(0, sizeof($avail));
   }

   public function testAddAvailabilityRange() {
      foreach (array($this->date1, $this->date2) as $d) {
         $this->hostel->add_availability($d, 1, 1, 25);
      }
      $avail = $this->hostel->get_availabilities($this->date1, $this->date2);
      $this->assertEquals(1, sizeof($avail));
   }
}

?>
