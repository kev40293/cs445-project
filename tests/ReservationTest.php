<?php
require_once("PHPUnit.php");
require_once("../app/Reservation.php");

class MakeReservationTest extends PHPUnit_Framework_TestCase {
   protected $customer;
   protected $reservation;
   protected $date1 = "20131010";
   protected $bed1;
   protected $bed2;

   public function setUp(){
      $this->customer = new Customer();
      $this->reservation = new Reservation($this->customer);
      $this->bed1 = new Bed(1, array($this->date1));
      $this->bed2 = new Bed(2, array($this->date1));
   }

   public function testEmptyReservation() {
      $rbeds = $this->reservation->get_beds();
      $this->assertEmpty($rbeds);
   }

   public function testAddBed() {
      $this->reservation->add_bed($this->bed1, $this->date1);

      $this->assertFalse($this->bed1->is_free($this->date1));
      $rbeds = $this->reservation->get_beds();
      $this->assertEquals($rbeds[$this->date1][0], $this->bed1);
      $this->assertEquals(sizeof($rbeds), 1);
   }

   public function testAddMoreBeds() {
      $this->reservation->add_bed($this->bed1, $this->date1);
      $this->reservation->add_bed($this->bed2, $this->date1);

      $this->assertFalse($this->bed1->is_free($this->date1));
      $rbeds = $this->reservation->get_beds();
      print sizeof($rbeds);
      $this->assertEquals(2, sizeof($rbeds[$this->date1]));
   }

   public function testAddBookedBed() {
      $this->bed1->book($this->date1);
      $this->setExpectedException('InvalidArgumentException');
      $this->reservation->add_bed($this->bed1, $this->date1);
   }

   public function testCancel() {
      $this->reservation->add_bed($this->bed1, $this->date1);
      $this->reservation->add_bed($this->bed2, $this->date1);

      $this->reservation->cancel();
      $this->assertTrue($this->bed1->is_free($this->date1));
      $this->assertTrue($this->bed2->is_free($this->date1));
      $rbeds = $this->reservation->get_beds();
      $this->assertEquals(0, sizeof($rbeds));
   }

}

?>
