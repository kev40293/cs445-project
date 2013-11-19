<?php

require_once ("PHPUnit.php");
require_once("../app/Customer.php");
require_once ("DefaultsFactory.php");

class CustomerTest extends PHPUnit_Framework_TestCase {
   private $customer;
   private $avail_id;
   private $avail_id2;

   public function setUp() {
      $db = init_database();
      $this->hostel1 = $db->add_hostel("Hostel 21", default_address(),
         default_contact(), default_restrictions());
      $cust = $db->add_customer("John", "Greene", "nothing@me.com", array());
      $this->avail_id = $db->add_availability("Hostel 21", "20131111", 1, 4, 25);
      $this->avail_id2 = $db->add_availability("Hostel 21", "20131112", 1, 4, 25);
      $this->customer = new Customer($cust);
   }

   public function testMakeReservation() {
      $rid = $this->customer->make_reservation($this->avail_id, 1);
      $this->assertGreaterThan(0, $rid);
      $res = $this->customer->get_reservation_info($rid);
      //var_dump($res);
      $this->assertCount(1, $res["hostel"]);
      $this->assertEquals(1 , $res['hostel']['Hostel 21']['20131111']);
   }
   public function testMultipleReservation() {
      $rid = $this->customer->make_reservation($this->avail_id, 1);
      $rid2 = $this->customer->make_reservation($this->avail_id2, 2);
      $res = $this->customer->get_reservation_info($rid2);
      $this->assertCount(1 , $res['hostel']['Hostel 21']);
      $this->assertEquals(2 , $res['hostel']['Hostel 21']['20131112']);
   }

   public function testMultipleAvailForReservation() {
      $rid = $this->customer->make_reservation($this->avail_id, 1);
      $rid2 = $this->customer->make_reservation($this->avail_id2, 2, $rid);
      $res = $this->customer->get_reservation_info($rid2);
      $this->assertEquals($rid, $rid2);
      $this->assertCount(2 , $res['hostel']['Hostel 21']);
      $this->assertEquals(2 , $res['hostel']['Hostel 21']['20131112']);
   }

   public function testCancelReservation() {
      $rid = $this->customer->make_reservation($this->avail_id, 1);
      $this->customer->cancel_reservation($rid);
      $res_info = $this->customer->get_reservation_info($rid);
      $this->assertEquals(null, $res_info);

   }

   public function testUpdatingRevenue() {
      $rid = $this->customer->make_reservation($this->avail_id, 1);
      $db = open_database();
      $this->assertEquals(25, $db->get_revenue());
      $this->customer->cancel_reservation($rid);
      $db = open_database();
      $this->assertEquals(0, $db->get_revenue());
   }
}

?>
