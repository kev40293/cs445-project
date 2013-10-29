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

   public function testDatesFromRangeOne() {
      $result = BookingDate::dates_from_range("20131012", "20131012");
      $this->assertCount(1, $result);
      $this->assertEquals("20131012", "20131012");
   }

   public function testNumDaysOne() {
      $this->assertEquals(1, BookingDate::get_num_days("20131010", "20131010"));
   }

   public function testNumDaysMore() {
      $this->assertEquals(11, BookingDate::get_num_days("20131010", "20131020"));
   }

   public function testNumDaysBorder() {
      $this->assertEquals(2, BookingDate::get_num_days("20131031", "20131101"));
   }

   public function testNumDaysBadRange () {
      $this->assertEquals(0, BookingDate::get_num_days("20131031", "20131001"));
   }

}
