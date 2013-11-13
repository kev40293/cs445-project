<?php
require_once("PHPUnit.php");
require_once("../app/Reservation.php");

class MakeReservationTest extends PHPUnit_Framework_TestCase {
   protected $customer;
   protected $reservation;
   protected $date1 = "20131010";
   protected $rate1 = "25";
   protected $rate2 = "50";
   protected $avail1;

   public function setUp(){
      $this->customer = new Customer();
      $address = array("city" => "Chicago");
      $this->hostel1 = new Hostel("Hostel 21", $address, "", "");
      $this->avail1 = new Availability(1,$this->date1,2,25,$this->hostel1);
      $this->reservation = new Reservation();
   }

   public function testEmptyReservation() {
      $rbeds = $this->reservation->bed_list();
      $this->assertEmpty($rbeds);
   }
   public function testAddWithoutBook() {
      $this->reservation->add_availability($this->avail1, 2);
      $rbeds = $this->reservation->bed_list();
      $this->assertEquals(0, sizeof($rbeds));
   }

   public function testAddAndBook() {
      $this->reservation->add_availability($this->avail1, 2);
      $this->reservation->book($this->customer);
      $rbeds = $this->reservation->bed_list();
      $this->assertEquals(1, sizeof($rbeds));
   }
   public function testCancelReservation() {
      $this->reservation->add_availability($this->avail1, 2);
      $this->reservation->book($this->customer);
      $this->reservation->cancel();
      $rbeds = $this->reservation->bed_list();
      $this->assertEquals(0, sizeof($rbeds));
   }

   public function testCostCalculation() {
      $this->reservation->add_availability($this->avail1, 2);
      $this->reservation->book($this->customer);
      $this->assertEquals(50, $this->reservation->get_cost());
   }

}

?>
