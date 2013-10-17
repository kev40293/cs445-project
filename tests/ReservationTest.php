<?php
require_once("PHPUnit.php");
require_once("../app/Reservation.php");

class MakeReservationTest extends PHPUnit_Framework_TestCase {
   protected $customer;
   protected $reservation;
   public function setUp(){
      $this->customer = new Customer();
      $this->reservation = new Reservation($this->customer);
   }

   public function testEmptyReservation() {
      $rbeds = $this->reservation->get_beds();
      $this->assertEmpty($rbeds);
   }

   public function testAddBed() {
      $bed = new Bed(1);
      $this->reservation->add_bed($bed);

      $this->assertFalse($bed->is_free());
      $rbeds = $this->reservation->get_beds();
      $this->assertEquals($rbeds[0], $bed);
      $this->assertEquals(sizeof($rbeds), 1);
   }

   public function testAddMoreBeds() {
      $bed = new Bed(1);
      $bed2 = new Bed(2);
      $this->reservation->add_bed($bed);
      $this->reservation->add_bed($bed2);

      $this->assertFalse($bed->is_free());
      $rbeds = $this->reservation->get_beds();
      $this->assertGreaterThan(1, sizeof($rbeds));
   }

   public function testAddBookedBed() {
      $bed = new Bed(1);
      $bed->book();
      $this->setExpectedException('InvalidArgumentException');
      $this->reservation->add_bed($bed);
   }

   public function testCancel() {
      $bed = new Bed(1);
      $bed2 = new Bed(2);
      $this->reservation->add_bed($bed);
      $this->reservation->add_bed($bed2);

      $this->reservation->cancel();
      $this->assertTrue($bed->is_free());
      $this->assertTrue($bed2->is_free());
   }

}

class CancelReservationTest extends PHPUnit_Framework_TestCase {

}

?>
