<?php
require_once("PHPUnit.php");
require_once("../app/Customer.php");

class MakeReservationTest extends PHPUnit_Framework_TestCase {
   protected $customer;
   public function setUp(){
      $customer = new Customer();
   }

   public function testSuccessful() {
   }

}

class CancelReservationTest extends PHPUnit_Framework_TestCase {

}

?>
