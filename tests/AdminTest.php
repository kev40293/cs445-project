
<?php

require_once ("PHPUnit.php");
require_once("../app/Admin.php");
require_once ("DefaultsFactory.php");

class AdminTest extends PHPUnit_Framework_TestCase {
   private $admin;
   public function setUp() {
      $db = init_database();
      $this->admin = new Admin();
   }

   public function testAddUesr() {
      $cust = $this->admin->create_user("Kevin", "Brandstatter", "kbrandst@hawk.iit.edu");
      $this->assertGreaterThan(0, $cust);
      $user_info = $this->admin->get_user_info($cust);
      $this->assertEquals("Kevin", $user_info["first_name"]);
   }

   public function testChangeUser() {
      $cust = $this->admin->create_user("Kevin", "Brandstatter", "kbrandst@hawk.iit.edu");
      $this->admin->change_user($cust, array("first_name" => "John"));
      $user_info = $this->admin->get_user_info($cust);
      $this->assertEquals("John", $user_info["first_name"]);
   }

}

?>
