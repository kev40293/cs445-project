<?php

require_once("Database.php");

class XML_Database implements DatabaseInterface {
   private $dom_root;
   private $xml_file;
   public function __construct($xml_file){
      $this->xml_file = $xml_file;
   }

   public function open() {
      if (file_exists($this->xml_file)) {
         $this->dom_root = simplexml_load_file($this->xml_file);
      }
      else {
         $this->init();
      }
   }

   public function init() {
      $this->dom_root = new SimpleXMLElement("<HostelData></HostelData>");
      $this->dom_root->addChild("hostels");
      $this->dom_root->addChild("reservations");
      $this->dom_root->reservations->addAttribute("next_id", "1");
      $this->dom_root->addChild("customers");
      $this->dom_root->customers->addAttribute("next_id", "1");
      $this->dom_root->addAttribute("num_records", "0");
      $this->persist();
   }

   private function persist() {
      $this->dom_root->saveXML($this->xml_file);
   }

   public function add_customer($fname, $lname, $email, $cc_info){
      $cust_dom = $this->dom_root->customers;
      $cust_id = (int)$cust_dom["next_id"];
      $cust_dom["next_id"] = $cust_id + 1;
      $cdom = $cust_dom->addChild("customer");
      $cdom->addChild("id", $cust_id);
      $cdom->addChild("first_name", $fname);
      $cdom->addChild("last_name", $lname);
      $cdom->addChild("email", $email);
      $cc = $cdom->addChild("cc_info");
      foreach ($cc_info as $key => $val) {
         $cc->addChild($key, $val);
      }
      $this->persist();
      return $cust_id;
   }
   public function update_customer($cust_id, $options){
      $customer_list = $this->dom_root->customers;
      foreach ($customer_list->customer as $customer){
         if ($customer->id = $cust_id){
            foreach ($customer->children() as $child){
               if (isset($options[$child->getName()])){
                  $customer->{$child->getName()} =  $options[$child->getName()];
               }
            }
         }
      }
      $this->persist();
   }
   private function create_xml_node($name, $value) {
      return new SimpleXMLElement("<$name>$value</$name>");
   }
   public function get_customer_info($cust_id){
      $customer_list = $this->dom_root->customers;
      foreach ($customer_list->customer as $customer){
         if ($customer->id = $cust_id){
            $info = array();
            foreach ($customer->children() as $node){
               $info[$node->getName()] = (string)$node;
            }
            return $info;
         }
      }
   }


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
            return $id;
         }
      }
      return null;
   }
   private function get_availability_by_id($a_id) {
      $hostel_list = $this->dom_root->hostels;
      foreach ($hostel_list->hostel as $hostel) {
         foreach ($hostel->availabilities->availability as $avail){
            if ((int)$avail->id == $a_id) {
               return $avail;
            }
         }
      }
   }
   public function get_available_space($avail_id) {
      $avail = $this->get_availability_by_id($avail_id);
      return (int) $avail->bed;
   }
   public function update_available_space($avail_id, $qty){
      $avail = $this->get_availability_by_id($avail_id);
      $avail->bed[0] = $qty;
      $this->persist();
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
      $resv_record->addChild("cust", $cust_id);
      $resv_record->addChild("avail", $avail_id);
      $resv_record->addChild("qty", $qty);

      $this->avail = $this->get_availability_by_id($avail_id);
      $this->avail->bed -= $qty;
      $this->persist();
      return $rid;
   }
   public function delete_reservation($cust_id, $resv_id){
      $resv_dom = $this->dom_root->reservations;
      $index = -1;
      foreach ($resv_dom->reservation as $ind => $reservation) {
         $index++;
         if ($reservation->id == $resv_id and $reservation->cust == $cust_id) {
            $this->avail = $this->get_availability_by_id($reservation->avail);
            $this->avail->bed += $reservation->qty;
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
