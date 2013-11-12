<?php
require_once("PHPUnit.php");
require_once("../app/Reservation.php");

class MakeReservationTest extends PHPUnit_Framework_TestCase {
   protected $customer;
   protected $reservation;
   protected $date1 = "20131010";
   protected $rate1 = "25";
   protected $rate2 = "50";
   protected $bed1;
   protected $bed2;

   public function setUp(){
      $this->customer = new Customer();
      $this->reservation = new Reservation(1, $this->customer);
      $this->bed1 = new Bed(1, array($this->date1), $this->rate1);
      $this->bed2 = new Bed(2, array($this->date1), $this->rate2);
   }

   public function testEmptyReservation() {
      $rbeds = $this->reservation->get_bookings();
      $this->assertEmpty($rbeds);
   }

   public function testAddBed() {
      $this->reservation->add_bed($this->bed1, $this->date1);

      $this->assertFalse($this->bed1->is_free($this->date1));
      $rbeds = $this->reservation->get_bookings();
      $this->assertEquals($rbeds[$this->date1][0], $this->bed1);
      $this->assertEquals(sizeof($rbeds), 1);
   }

   public function testAddMoreBeds() {
      $this->reservation->add_bed($this->bed1, $this->date1);
      $this->reservation->add_bed($this->bed2, $this->date1);

      $this->assertFalse($this->bed1->is_free($this->date1));
      $rbeds = $this->reservation->get_bookings();
      $this->assertEquals(2, sizeof($rbeds[$this->date1]));
   }

   public function testAddBookedBed() {
      $this->bed1->book($this->date1);
      $this->setExpectedException('IllegalBookingException');
      $this->reservation->add_bed($this->bed1, $this->date1);
   }

   public function testCancel() {
      $this->reservation->add_bed($this->bed1, $this->date1);
      $this->reservation->add_bed($this->bed2, $this->date1);

      $this->reservation->cancel();
      $this->assertTrue($this->bed1->is_free($this->date1));
      $this->assertTrue($this->bed2->is_free($this->date1));
      $rbeds = $this->reservation->get_bookings();
      $this->assertEquals(0, sizeof($rbeds));
   }

   public function testCalculateCostEmpty () {
      $cost = $this->reservation->calculate_cost();
      $this->assertEquals(0, $cost);
   }

   public function testCalculateCostTwoBeds() {
      $this->reservation->add_bed($this->bed1, $this->date1);
      $this->reservation->add_bed($this->bed2, $this->date1);
      $cost = $this->reservation->calculate_cost();
      $this->assertEquals(75, $cost);
   }

   public function testCalculateCostTwoDays() {
      $this->reservation->add_bed($this->bed1, $this->date1);
      $this->bed1->add_dates(array(20131011));
      $this->reservation->add_bed($this->bed1, "20131011");
      $cost = $this->reservation->calculate_cost();
      $this->assertEquals(50, $cost);
   }

   public function testBookRange() {
      $this->bed1->add_dates(array(20131011));
      $this->reservation->add_bed($this->bed1, $this->date1, 2);
      $cost = $this->reservation->calculate_cost();
      $this->assertEquals(50, $cost);
      $this->assertCount(2, $this->reservation->get_bookings());
   }

}

?>
