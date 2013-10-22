<?php

require_once("PHPUnit.php");
require_once("../app/Bed.php");

class BedFunctionsTest extends PHPUnit_Framework_TestCase {
   protected $bed;
   protected $init_avail = array("10102013");
   public function setUp(){
      $this->bed = new Bed(1, $this->init_avail);
   }

   public function testFree() {
      $avail = $this->bed->free_dates();
      $this->assertGreaterThan(0, sizeof($avail));
      $this->assertTrue($this->bed->is_free("10102013"));
   }

   public function testBooking() {
      $numavail = sizeof($this->bed->free_dates());
      $this->bed->book("10102013");
      $this->assertFalse($this->bed->is_free("10102013"));
      $this->assertEquals($numavail - 1, sizeof($this->bed->free_dates()));
   }

   public function testFreeBed() {
      $this->bed->book("10102013");
      $this->bed->free("10102013");
      $this->assertTrue($this->bed->is_free("10102013"));
   }
}

?>
