<?php

class Hostel {
   private $id;
   private $name;
   private $address;
   private $contact;
   private $restrictions;
   private $availabilities;

   public function __construct($n, $add, $cont, $rest) {
      $this->name = $n;
      $this->address = $add;
      $this->contact = $cont;
      $this->restrictions = $rest;
   }

   public function get_name() {return $this->name; }

   public function get_city() {
      return $this->address["city"];
   }

   public function add_availability($date, $room, $nbeds, $price) {
      $db = open_database();
      $db->add_availability($this->name, $date, $room, $nbeds, $price);
   }

   public function equals($host) {
      return $this->name == $host->get_name();
   }

}

?>
