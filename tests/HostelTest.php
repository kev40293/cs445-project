<?php

require_once("PHPUnit.php");
require_once("../app/Hostel.php");

class Check_AvailabilitiesTest extends PHPUnit_Framework_TestCase {
   protected $hostel;
   protected $date1;
   protected $rate1;
   protected $init_dates;
   protected $bed_one;
   protected $bed_two;

   protected function setUp(){
      $this->date1 = "10102013";
      $this->rate1 = "25";
      $this->init_dates = array($this->date1);
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

}

?>
