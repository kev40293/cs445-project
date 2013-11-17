<?php

require_once ("../app/Database.php");
require_once ("PHPUnit.php");
require_once ("DefaultsFactory.php");

class DatabaseTest extends PHPUnit_Framework_TestCase {
   protected $db;
   protected $avail1;
   protected $hostel1;

   protected function setUp() {
      $this->db = init_database();
      $this->db->init();
      $this->hostel1 = $this->db->add_hostel("Hostel 21", default_address(),
         default_contact(), default_restrictions());
   }

   public function testHostelAdded() {
      $this->assertFalse($this->hostel1 == null);
   }

   public function testSearchEmpty() {
      $ret = $this->db->search_availability(null);
      $this->assertEmpty($ret);
   }

   public function testSearchEmptyAll() {
      $avail = $this->db->add_availability("Hostel 21", "20131111", 1, 4, 25);
      $ret = $this->db->search_availability(null);
      $this->assertCount(1, $ret);
   }

   public function testAddAvailability() {
      $a_id = $this->db->add_availability("Hostel 21", "20131111", 1, 4, 25);
      $this->assertGreaterThan(0, $a_id);
      $space = $this->db->get_available_space($a_id);
      $this->assertEquals(4, $space);
      $this->assertGreaterThan(0, $a_id);
   }

   public function testSearchAvailCity() {
      $this->db->add_availability("Hostel 21", "20131111", 1, 4, 25);
      $search = array("start_date" => "20131111",
                      "end_date" => "20131111",
                      "num" => 1,
                      "city" => "Chicago");
      $results = $this->db->search_availability($search);
      $this->assertCount(1, $results);
   }

   public function testSearchAvailMult() {
      $this->db->add_availability("Hostel 21", "20131111", 1, 4, 25);
      $this->db->add_availability("Hostel 21", "20131111", 2, 2, 25);
      $search = array("start_date" => "20131111",
                      "end_date" => "20131111",
                      "num" => 1,
                      "city" => null);
      $results = $this->db->search_availability($search);
      $this->assertCount(2, $results);
   }

   public function testSearchCity() {
      $this->db->add_availability("Hostel 21", "20131111", 1, 4, 25);
      $this->db->add_availability("Hostel 21", "20131111", 2, 2, 25);
      $search = array("start_date" => "20131111",
                      "end_date" => "20131111",
                      "num" => 1,
                      "city" => "Chicago");
      $results = $this->db->search_availability($search);
      $this->assertCount(2, $results);
   }

   public function testDateFormatAvail() {
      $this->db->add_availability("Hostel 21", "20131112", 1, 4, 25);
      $search = array("start_date" => "20131112",
                      "end_date" => "20131112",
                      "num" => 1,
                      "city" => "Chicago");
      $results = $this->db->search_availability($search);
      $this->assertEquals("20131112", $results[1]['date']);
   }

   /*
   public function testUpdateAvail() {
      $a_id = $this->db->add_availability("Hostel 21", "20131111", 1, 4, 25);
      $this->db->update_available_space($a_id, -2);
      $space = $this->db->get_available_space($a_id);
      $this->assertEquals(2, $space);
      $this->db->update_available_space($a_id, 2);
      $space = $this->db->get_available_space($a_id);
      $this->assertEquals(4, $space);
   }
   */

   public function testSearchReturnsIDindex() {
      $this->db->add_availability("Hostel 21", "20131111", 1, 4, 25);
      $this->db->add_availability("Hostel 21", "20131112", 2, 2, 25);
      $search = array("start_date" => "20131112",
                      "end_date" => "20131112",
                      "num" => 1,
                      "city" => "Chicago");
      $results = $this->db->search_availability($search);
      $this->assertArrayHasKey(2, $results);
   }

   public function testAddCustomer() {
      $cust = $this->db->add_customer("Kevin", "Brandstatter", "nothing@me.com", array());
      $info = $this->db->get_customer_info($cust);
      $this->assertEquals("Kevin", $info["first_name"]);
      $this->assertEquals("Brandstatter", $info["last_name"]);
      $this->assertEquals("nothing@me.com", $info["email"]);
   }

   public function testUpdateCustomer() {
      $cust = $this->db->add_customer("John", "Greene", "nothing@me.com", array());
      $this->db->update_customer($cust,
         array("first_name" => "Kevin", "last_name" => "Brandstatter"));
      $info = $this->db->get_customer_info($cust);
      $this->assertEquals("Kevin", $info["first_name"]);
      $this->assertEquals("Brandstatter", $info["last_name"]);
      $this->assertEquals("nothing@me.com", $info["email"]);
   }

   public function testAddReservation(){
      $cid = $this->db->add_customer("John", "Greene", "nothing@me.com", array());
      $aid = $this->db->add_availability("Hostel 21", "20131111", 1, 4, 25);
      $rid = $this->db->make_reservation($cid, $aid, 1);
      $this->assertEquals(3, $this->db->get_available_space($aid));
      $this->assertGreaterThan(0, $rid);
   }

   public function testAddMultipleToReservation() {
      $cid = $this->db->add_customer("John", "Greene", "nothing@me.com", array());
      $aid = $this->db->add_availability("Hostel 21", "20131111", 1, 4, 25);
      $aid2 = $this->db->add_availability("Hostel 21", "20131112", 2, 4, 35);
      $rid = $this->db->make_reservation($cid, $aid, 1);
      $rid2 = $this->db->make_reservation($cid, $aid2, 2, $rid);
      $this->assertEquals($rid, $rid2);
      $res_info = $this->db->get_reservation($rid);
      $this->assertNotEquals($res_info['bookings'][0]['id'], 
         $res_info['bookings'][1]['id']);
   }

   public function testCancelReservation(){
      $cid = $this->db->add_customer("John", "Greene", "nothing@me.com", array());
      $aid = $this->db->add_availability("Hostel 21", "20131111", 1, 4, 25);
      $rid = $this->db->make_reservation($cid, $aid, 1);
      $this->db->delete_reservation($cid, $rid);
      $this->assertEquals(4, $this->db->get_available_space($aid));
      $this->assertNull($this->db->get_reservation($rid));
   }

   public function testCancelReservation2(){
      $cid = $this->db->add_customer("John", "Greene", "nothing@me.com", array());
      $aid = $this->db->add_availability("Hostel 21", "20131111", 1, 4, 25);
      $aid2 = $this->db->add_availability("Hostel 21", "20131111", 2, 4, 35);
      $rid = $this->db->make_reservation($cid, $aid, 1);
      $rid2 = $this->db->make_reservation($cid, $aid2, 2);
      $this->assertFalse($rid == $rid2);
      $this->db->delete_reservation($cid, $rid2);
      $this->assertEquals(3, $this->db->get_available_space($aid));
      $this->assertEquals(4, $this->db->get_available_space($aid2));
   }

   public function testDontCancelOtherUsersReservation(){
      $cid = $this->db->add_customer("John", "Greene", "nothing@me.com", array());
      $aid = $this->db->add_availability("Hostel 21", "20131111", 1, 4, 25);
      $rid = $this->db->make_reservation($cid, $aid, 1);
      $this->db->delete_reservation($cid+1, $rid);
      $this->assertEquals(3, $this->db->get_available_space($aid));
   }
   public function testGetReservation(){
      $cid = $this->db->add_customer("John", "Greene", "nothing@me.com", array());
      $aid = $this->db->add_availability("Hostel 21", "20131111", 1, 4, 25);
      $rid = $this->db->make_reservation($cid, $aid, 3);
      $res_info = $this->db->get_reservation($rid);
      $this->assertEquals( 75,  $res_info["price"]);
      $this->assertEquals( $rid,  $res_info["id"]);
      $this->assertEquals("Greene", $res_info["last_name"]);
      $this->assertEquals(3, (int)$res_info["bookings"][0]["qty"]);
   }

   public function testGetReservationDateFormat(){
      $cid = $this->db->add_customer("John", "Greene", "nothing@me.com", array());
      $aid = $this->db->add_availability("Hostel 21", "20131112", 1, 4, 25);
      $rid = $this->db->make_reservation($cid, $aid, 3);
      $res_info = $this->db->get_reservation($rid);
      $this->assertEquals("20131112", $res_info['bookings'][0]['date']);
   }

   public function testGetReservationMultiAvail(){
      $cid = $this->db->add_customer("John", "Greene", "nothing@me.com", array());
      $aid = $this->db->add_availability("Hostel 21", "20131111", 1, 4, 25);
      $aid2 = $this->db->add_availability("Hostel 21", "20131111", 2, 4, 35);
      $rid = $this->db->make_reservation($cid, $aid, 3);
      $this->db->make_reservation($cid, $aid2, 1, $rid);
      $res_info = $this->db->get_reservation($rid);
      $this->assertEquals( 110,  $res_info["price"]);
      $this->assertEquals( $rid,  $res_info["id"]);
      $this->assertCount(2, $res_info["bookings"]);
   }

   public function testGetRevenue() {
      $cid = $this->db->add_customer("John", "Greene", "nothing@me.com", array());
      $aid = $this->db->add_availability("Hostel 21", "20131111", 1, 4, 25);
      $aid2 = $this->db->add_availability("Hostel 21", "20131111", 2, 4, 35);
      $this->assertEquals(0, $this->db->get_revenue());
      $rid = $this->db->make_reservation($cid, $aid, 1);
      $rid2 = $this->db->make_reservation($cid, $aid2, 2);
      $this->assertEquals(95, $this->db->get_revenue());
      $this->db->delete_reservation($cid, $rid);
      $this->assertEquals(70, $this->db->get_revenue());
   }
   public function testGetOccupancy() {
      $cid = $this->db->add_customer("John", "Greene", "nothing@me.com", array());
      $aid = $this->db->add_availability("Hostel 21", "20131111", 1, 4, 25);
      $aid2 = $this->db->add_availability("Hostel 21", "20131111", 2, 4, 35);
      $this->assertEquals(0, $this->db->get_occupancy());
      $rid2 = $this->db->make_reservation($cid, $aid2, 2);
      $this->assertEquals(0.25, $this->db->get_occupancy());
      $rid = $this->db->make_reservation($cid, $aid, 4);
      $this->assertEquals(0.75, $this->db->get_occupancy());
   }
}

?>
