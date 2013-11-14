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
      $this->dom_root->addChild("reservations");
      $this->dom_root->reservations->addAttribute("next_id", "1");
      $this->dom_root->addChild("customer");
      $this->dom_root->customer->addAttribute("next_id", "1");
      $this->dom_root->addAttribute("num_records", "0");
      $this->persist();
   }

   private function persist() {
      $this->dom_root->saveXML($this->xml_file);
   }

   public function add_customer($cust){
      return new Customer();
   }
   public function update_customer($cust){}

   public function add_availability($hostel_name, $date, $room, $qty, $price){
      $curr = $this->dom_root->hostels;
      $id = $this->dom_root["num_records"] + 1;
      $this->dom_root["num_records"] = $id;
      foreach ($curr->hostel as $host) {
         if ($host->name == $hostel_name){
            $avail = $host->availabilities->addChild("availability");
            $avail->addChild("id", $id);
            $avail->addChild("room", $room);
            $avail->addChild("date", $date);
            $avail->addChild("bed", $qty);
            $avail->addChild("price", $price);
            $this->persist();
            return new Availability($room, $date, $qty, $price, $hostel_name);
         }
      }
      return null;
   }
   public function update_availability($hostel_name, $date, $room, $qty, $price){
      $hostel_list = $this->dom_root->hostels;
      foreach ($hostel_list->hostel as $hostel) {
         if ($hostel->name == $hostel_name) {
            foreach ($hostel->availabilities->availability as $avail){
               if ((int)$avail->room[0] == $room and (string)$avail->date[0] == $date) {
                  $avail->bed[0] += $qty;
                  $avail->price[0] = $price;
                  $this->persist();
               }
            }
         }
      }

   }
   public function search_availability($sparam){
      $sdates = BookingDate::dates_from_range($sparam["start_date"], $sparam["end_date"]);
      $dom = $this->dom_root->hostels;
      $results = array();
      foreach ($dom->hostel as $hostel){
         if ($sparam["city"] == null or $sparam["city"] == $hostel->address->city){
            $sub_result = $this->matchAvailability($hostel, $sdates, $sparam['num']);
            $results = $sub_result + $results;
         }
      }
      return $results;
   }

   private function matchAvailability($hostel_xml, $dates, $qty){
      $res = array();
      $hostel_avail = $hostel_xml->availabilities;
      foreach ($hostel_avail->availability as $availability) {
         if (empty($dates) or (in_array($availability->date, $dates) and $availability->bed >= $qty)) {
            $res[(int)$availability->id] = $this->xml_to_avail($availability, $hostel_xml->name);
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

   public function make_reservation($cust_id, $avail_id, $qty){
      $reservs = $this->dom_root->reservations;
      $resv_record = $reservs->addChild("reservation");
      $rid = (int)$reservs["next_id"];
      $reservs["next_id"] = $rid +1;
      $resv_record->addChild("id", $rid);
      $resv_record->addChild("cust". $cust_id);
      $resv_record->addChild("avail", $avail_id);
      $resv_record->addChild("qty", $qty);
      $resv_record->persist();
      return $rid;
   }
   public function delete_reservation($resv_id){
      $resv_dom = $this->dom_root->reservations;
      $index = -1;
      foreach ($resv_dom->reservation as $ind => $reservation) {
         $index++;
         if ($reservation->id == $resv_id) {
            unset ($this->dom_root->reservations->reservation[$index]);
            $this->persist();
            return;
         }
      }
   }
   public function search_reservation($param){
      $resv_list = $this->dom_root->reservations;
      foreach ($resv_list->reservation as $reservation) {
         if ($param["id"] != null and $param["id"] == $reservation->id) {
            return array($this->xml_to_reservation($reservation));
         }
      }
      return array();
   }

   private function xml_to_reservation($resv) {
      return new Reservation();
   }

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

      $this->persist();

      return new Hostel($name, $address, $contact, $restrict);
   }
}
?>
