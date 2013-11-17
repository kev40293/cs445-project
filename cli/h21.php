<?php

require_once("../app/Admin.php");
require_once("../app/Customer.php");
require_once("../app/Search.php");

#array_pop($argv);
$command = $argv[1];
if ($command == "admin" ) {
   admin($argv);
}
else if ($command == "user") {
   user($argv);
}
else if ($command == "book") {
   book($argv);
}
else if ($command == "search") {
   search($argv);
}

function pair_args($args, $start) {
   $opts = array();
   for($ind = $start; $ind < sizeof($args); $ind+=2 ){ 
      $opts[trim($args[$ind],"-")] = $args[$ind+1];
   }
   return $opts;
}

function search ($argv) {
   $opts = pair_args($argv, 2);
   $search = new Search($opts);
   $results = $search->get_results();
   print_results($results);
}
function admin($args){
   $cmd = $args[2];
   $admin = new Admin();
   if ($cmd == "load") {
      echo "Loading Hostel Data\n";
      $admin->load_hostel($args[3]);
   }
   else if ($cmd == "revenue") {
      printf("Revenue: $%s\n\n", $admin->get_revenue());
   }
   else if ($cmd == "occupancy"){
      printf("Occupancy: %f%%\n", $admin->get_occupancy() *100);
   }
}
function user($args){
   $cmd = $args[2];
   $admin = new Admin();
   $opts = pair_args($args, 3);
   if ($cmd == "add"){
      $admin->create_user($opts['first_name'], $opts['last_name'], $opts["email"], $opts);
   }
   else if ($cmd == "change") {
      $admin->change_user($opts['user_id'], $opts);
   }
   else if ($cmd == "view"){
      $cinfo = $admin->get_user_info($opts["user_id"]);
      print_user($cinfo);
   }
}
function book($args){
   $cmd = $args[2];
   $opts = pair_args($args, 3);
   if (!isset($opts["num_beds"])){
     $opts["num_beds"] = 1;
   }
   if ($cmd == "add"){
      $customer = new Customer($opts["user_id"]);
      if (isset($opts["book_id"]))
         $res_id = $customer->make_reservation($opts["avail_id"], $opts["num_beds"], $opts["book_id"]);
      else
         $res_id = $customer->make_reservation($opts["avail_id"], $opts["num_beds"]);
      if ($res_id < 0){
         print "Unable to make reservation\n";
      }
      else {
         $resv_info = $customer->get_reservation_info($opts["book_id"]);
         print_reservation($resv_info);
      }
   }
   else if ($cmd == "cancel"){
      $customer = new Customer($opts["user_id"]);
      $customer->cancel_reservation($opts["book_id"]);
   }
   else if ($cmd == "view"){
      $customer = new Customer($opts["user_id"]);
      $resv_info = $customer->get_reservation_info($opts["book_id"]);
      print_reservation($resv_info);
   }
}

function print_user($user){
   print "User: " . $user["id"] . "\n";
   print "First Name: " . $user["first_name"] . "\n";
   print "Last Name: " . $user["last_name"] . "\n";
   print "Email: " . $user["email"] . "\n\n";
}
function print_reservation($reservation){
   print "Booking ID: " . $reservation["id"] . "\n";
   printf("Name: %s %s\n", $reservation["first_name"], $reservation["last_name"]);
   foreach ($reservation["hostel"] as $hostel => $dates) {
      print "$hostel\n";
      foreach ($dates as $date => $qty) {
         printf("   Date: %s\n", $date);
         printf("      Number Beds: %d\n", $qty);
      }
   }
   //var_dump($reservation);
}
function print_results($results){
   $hostels = array();
   foreach ($results as $id => $sr) {
      $hostels[$sr["hostel"]][$sr["date"]][$sr["room"]] =
         array($id, $sr["bed"], $sr["price"]);
   }
   foreach($hostels as $hos => $dates){
      print "Hostel: $hos\n";
      foreach ($dates as $date => $rooms) {
         $pdate = DateTime::createFromFormat('Ymd',$date, new DateTimeZone('UTC'));
         $nice_date = $pdate->format('m/d/Y');
         print "   Date: $nice_date\n";
         foreach ($rooms as $room => $listing) {
            print "      Room: $room\n";
            print "         ". $listing[0] . ": " . $listing[1] . " at $" . $listing[2]. "\n";
         }
      }
      print "\n";
   }
}

?>
