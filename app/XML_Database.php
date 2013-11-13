<?php

require_once("Database.php");

class XML_Database implements DatabaseInterface {
   private $dom_root;
   private $xml_file;
   public function __construct($xml_file){
      $this->xml_file = $xml_file;
   }

   public function open() {
      $this->dom_root = simplexml_load_file($this->xml_file);
   }

   public function init() {
      $this->dom_root = new SimpleXMLElement("<HostelData></HostelData>");
      $this->dom_root->addChild("hostels");
      $this->dom_root->saveXML($this->xml_file);
   }

   public function add_customer($cust){
      // whatever man
      return new Customer();
   }
   public function update_customer($cust){}

   public function add_availability($hostel_name, $date, $room, $qty, $price){
      $curr = $this->dom_root->hostels;
      foreach ($curr->hostel as $host) {
         if ($host->name == $hostel_name){
            $avail = $host->availabilities->addChild("availability");
            $avail->addChild("room", $room);
            $avail->addChild("date", $date);
            $avail->addChild("bed", $qty);
            $avail->addChild("price", $price);
            $this->dom_root->asXML($this->xml_file);
            return new Availability($room, $date, $qty, $price, $hostel_name);
         }
      }
      return null;
   }
   public function update_availability($hostel, $room, $date, $qty, $price){}
   public function search_availability($sparam){
      $sdates = BookingDate::dates_from_range($sparam["start_date"], $sparam["end_date"]);
      $dom = $this->dom_root->hostels;
      $results = array();
      foreach ($dom->hostel as $hostel){
         if ($sparam["city"] == null or $sparam["city"] == $hostel->name){
            $sub_result = $this->matchAvailability($hostel, $sdates, $sparam['num']);
            $results = array_merge($sub_result, $results);
         }
      }
      return $results;
   }

   private function matchAvailability($hostel_xml, $dates, $qty){
      $res = array();
      $hostel_avail = $hostel_xml->availabilities;
      foreach ($hostel_avail->availability as $availability) {
         if (in_array($availability->date, $dates) and $availability->bed >= $qty) {
            $res[] = $this->xml_to_avail($availability, $hostel_xml->name);
         }
      }
      return $res;
   }
   private function xml_to_avail($avail_xml, $hname) {
      return new Availability((int) $avail_xml->room,
                              (string) $avail_xml->date,
                              (int) $avail_xml->bed,
                              (int) $avail_xml->price,
                              (string) $hname);
   }

   public function record_reservation($resv){}
   public function update_reservation($resv_id, $resv){}
   public function delete_reservation($resv_id){}
   public function search_reservation($param){}

   public function get_hostels($param){}
   public function add_hostel($name, $address, $contact, $restrict){
      $hostel_dom = $this->dom_root->hostels;
      $hostel = $hostel_dom->addChild("hostel");

      $hostel->addChild("name", $name);

      $cont = $hostel->addChild("contact");
      $cont->addChild("phone",  $contact->phone);
      $cont->addChild("email", $contact->email);
      $cont->addChild("facebook", $contact->facebook);
      $cont->addChild("web", $contact->web);

      $restrictions = $hostel->addChild("restrictions");
      $restrictions->addChild("check_in_time", $restrict->check_in_time);
      $restrictions->addChild("check_out_time", $restrict->check_out_time);
      $restrictions->addChild("smoking", $restrict->smoking);
      $restrictions->addChild("alchohol", $restrict->alcohol);

      $add = $hostel->addChild("address");
      $add->addChild("street", $address->street);
      $add->addChild("city", $address->city);
      $add->addChild("state", $address->state);
      $add->addChild("postal_code", $address->postal_code);
      $add->addChild("country", $address->country);

      $availabilities = $hostel->addChild("availabilities");

      $this->dom_root->asXML($this->xml_file);

      return new Hostel($name, $address, $contact, $restrict);
   }
}
?>
