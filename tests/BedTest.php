<?php

require_once("PHPUnit.php");
require_once("../app/Bed.php");

class BedFunctionsTest extends PHPUnit_Framework_TestCase {
   protected $bed;
   public function setUp(){
      $this->bed = new Bed(1);
   }

   public function testFree() {
      $this->assertTrue($this->bed->is_free());
   }

   public function testBooking() {
      $this->bed->book_bed();
      $this->assertFalse($this->bed->is_free());
   }

   public function testFreeBed() {
      $this->bed->book_bed();
      $this->bed->free_bed();
      $this->assertTrue($this->bed->is_free());
   }
}

?>
