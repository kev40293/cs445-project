<?php

require_once("PHPUnit.php");
require_once("../app/Bed.php");

class BedFunctionsTest extends PHPUnit_Framework_TestCase {
   protected $bed;
   protected $date1 = "20131010";
   protected $date2 = "20131011";
   protected $rate1 = "25";
   protected $init_avail;
   public function setUp(){
      $this->init_avail = array($this->date1, $this->date2);
      $this->bed = new Bed(1, $this->init_avail, $this->rate1);
   }

   public function testFree() {
      $avail = $this->bed->free_dates();
      $this->assertGreaterThan(0, sizeof($avail));
      $this->assertTrue($this->bed->is_free($this->date1));
   }

   public function testBooking() {
      $numavail = sizeof($this->bed->free_dates());
      $this->bed->book($this->date1);
      $this->assertFalse($this->bed->is_free($this->date1));
      $avail = $this->bed->free_dates();
      $this->assertCount($numavail - 1, $avail);
   }

   public function testFreeBed() {
      $this->bed->book($this->date1);
      $this->bed->free($this->date1);
      $this->assertTrue($this->bed->is_free($this->date1));
   }

   public function testBadBooking() {
      $this->bed->book($this->date1);
      $this->setExpectedException('IllegalBookingException');
      $this->bed->book($this->date1);
   }

   public function testBookRange() {
      $range = BookingDate::date_range($this->date1, $this->date2);
      $this->bed->book_range($this->date1, $this->date2);

      foreach ($range as $date) {
         $this->assertFalse($this->bed->is_free($date));
      }
   }
}

?>
