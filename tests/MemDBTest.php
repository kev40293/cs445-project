<?php

require_once ("../app/Database.php");
require_once ("PHPUnit.php");
class MemDBTest extends PHPUnit_Framework_TestCase {
   protected $db;
   protected function setUp() {
      $this->db = new MemoryDatabase(array());
   }

   public function testCreate() {
      $this->assertInstanceOf('MemoryDatabase', $this->db);
   }

}

?>
