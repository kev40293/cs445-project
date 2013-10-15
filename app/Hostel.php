<?php

class Hostel {
   protected $name;
   protected $address;
   protected $contact;
   protected $restrictions;
   protected $availabilities;

   // we should replace rooms and just use the database schema
   public function get_availabilities($db_conn) {
      // here put the database connect logic and query functions
      $result = $db_conn->query("Select * from Rooms");
      return $result;
   }

   public function search() {
   }

}

?>
