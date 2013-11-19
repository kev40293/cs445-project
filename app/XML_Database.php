<?php

require_once("Database.php");
require_once("Date.php");

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
      $x = ($this->get_hostel_availability_pair_by_id($a_id));
      return $x[1];
   }
   private function get_hostel_for_availability_id($a_id) {
      $x =($this->get_hostel_availability_pair_by_id($a_id));
      return (string)($x[0]->name);
   }
   private function get_hostel_availability_pair_by_id($a_id) {
      $hostel_list = $this->dom_root->hostels;
      foreach ($hostel_list->hostel as $hostel) {
         foreach ($hostel->availabilities->availability as $avail){
            if ((int)$avail->id == $a_id) {
               return array($hostel , $avail);
            }
         }
      }
      return array(null, 0);
   }

   public function get_available_space($avail_id) {
      $avail = $this->get_availability_by_id($avail_id);
      return (int) $avail->bed;
   }
   private function update_available_space($avail_id, $qty){
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
      return array("id" => (int) $avail_xml->id,
                   "room" => (int) $avail_xml->room,
                   "date" => (string) $avail_xml->date,
                   "bed"  =>(int) $avail_xml->bed,
                   "price" => (int) $avail_xml->price,
                   "hostel" => (string) $hname);
   }

   private function get_reservation_by_id($res_id, $cust_id){
      $reservs = $this->dom_root->reservations;
      if ($res_id != 0) {
         foreach ($reservs->reservation as $resv){
            if ($resv->id == $res_id and $cust_id == $resv->cust)
               return $resv;
         }
      }
      $rid = (int)$reservs["next_id"];
      $reservs["next_id"] = $rid +1;
      $resv_record = $reservs->addChild("reservation");
      $resv_record->addChild("id", $rid);
      $resv_record->addChild("cust", $cust_id);
      return $resv_record;
   }

   public function make_reservation($cust_id, $avail_id, $qty, $rid = 0){
      $resv_record = $this->get_reservation_by_id($rid, $cust_id);
      $av_rec = $resv_record->addChild("avail", $avail_id);
      $av_rec->addAttribute("qty", $qty);

      $this->avail = $this->get_availability_by_id($avail_id);
      $this->avail->bed -= $qty;
      $this->persist();
      return (int) $resv_record->id;
   }
   public function delete_reservation($cust_id, $resv_id){
      $resv_dom = $this->dom_root->reservations;
      $index = -1;
      foreach ($resv_dom->reservation as $ind => $reservation) {
         $index++;
         if ($reservation->id == $resv_id and $reservation->cust == $cust_id) {
            foreach ($reservation->avail as $item){
               $this->avail = $this->get_availability_by_id($item);
               $this->avail->bed += $item["qty"];
            }
            unset ($this->dom_root->reservations->reservation[$index]);
            $this->persist();
            return;
         }
      }
   }
   public function get_reservation($resv_id){
      $resv_list = $this->dom_root->reservations;
      foreach ($resv_list->reservation as $reservation) {
         if ($resv_id == $reservation->id) {
            return $this->xml_to_reservation($reservation);
         }
      }
      return null;
   }

   private function xml_to_reservation($resv) {
      $resv_info["price"] = 0;
      foreach ($resv->avail as $booking){
         $hostel_avail = $this->get_hostel_availability_pair_by_id($booking);
         $avail = $this->xml_to_avail($hostel_avail[1], $hostel_avail[0]->name);
         $avail["qty"] = (int)$booking["qty"];
         $resv_info["bookings"][] = $avail;
         $resv_info["price"] += $booking["qty"] * $avail["price"];
      }
      $cust = $this->get_customer_info($resv->cust);
      $resv_info["first_name"] = $cust["first_name"];
      $resv_info["last_name"] = $cust["last_name"];
      $resv_info["id"] = (int) $resv->id;
      return $resv_info;
   }

   private function get_hostel_by_name($hostel_name){
      $hostel_dom = $this->dom_root->hostels;
      foreach ($hostel_dom->hostel as $hostels) {
         if ($hostels->name == $hostel_name){
            return $hostels;
         }
      }
   }
   public function pay_for_availability($a_id, $qty) {
      $host_avail = $this->get_hostel_availability_pair_by_id($a_id);
      $host_avail[0]->revenue += ((int)$host_avail[1]->price) * $qty;
      $this->persist();
   }

   public function refund_availability($a_id, $qty, $penalty=0){
      $host_avail = $this->get_hostel_availability_pair_by_id($a_id);
      $penalty_cost = $host_avail[1]->price * $qty * $penalty;
      $host_avail[0]->revenue -= ($host_avail[1]->price * $qty - $penalty_cost);
      $this->persist();
   }

   public function get_hostel_restrictions($hostel_name){
      $hostels = $this->get_hostel_by_name($hostel_name);
      if ($hostels == null)
         return array();
      $restrict = $hostels->restrictions;
      return array(
         "check_in_time" => (string)$restrict->check_in_time,
         "check_out_time" =>  (string)$restrict->check_out_time,
         "smoking" =>  (string)$restrict->smoking,
         "alchohol" =>  (string)$restrict->alcohol,
         "cancellation_deadline" =>  (int)$restrict->cancellation_deadline,
         "cancellation_penalty" =>  (string)$restrict->cancellation_penalty);
   }
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
      $restrictions->addChild("cancellation_deadline", $restrict->cancellation_deadline);
      $restrictions->addChild("cancellation_penalty", $restrict->cancellation_penalty);
      $hostel->addChild("revenue", 0);

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

   public function get_revenue() {
      $revenue = 0;
      foreach($this->dom_root->hostels->hostel as $hostel) {
         $revenue += (int) $hostel->revenue;
      }
      return $revenue;
   }

   private function total_available() {
      $sum = 0;
      foreach ($this->dom_root->hostels->hostel as $hostel){
         foreach ($hostel->availabilities->availability as $avail){
            $sum += (int) $avail->bed;
         }
      }
      return $sum;
   }
   private function total_reserved() {
      $sum = 0;
      foreach ($this->dom_root->reservations->reservation as $resv) {
         foreach ($resv->avail as $item){
            $avail = $this->get_availability_by_id((int) $item);
            $sum += (int) $item["qty"];
         }
      }
      return $sum;
   }
   public function get_occupancy() {
      $free = $this->total_available();
      $reserved = $this->total_reserved();
      $occupancy = $reserved / ($free + $reserved);
      return $occupancy;
   }
}
?>
