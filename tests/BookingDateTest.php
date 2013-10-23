<?php

require_once("PHPUnit.php");
require_once("../app/Date.php");

class DateTest extends PHPUnit_Framework_TestCase {

   public function testDateRangeLength() {
      $array = BookingDate::date_range('20131010', 3);
      $this->assertCount(3, $array);
   }

   public function testDateRangeData() {
      $array = BookingDate::date_range('20131031', 3);
      $this->assertCount(3, $array);
      $this->assertEquals($array[1], '20131101');
   }

   public function testExceptionOnBadRange() {
      $this->setExpectedException('InvalidArgumentException');
      $array = BookingDate::date_range('20131111', -4);
   }

}
