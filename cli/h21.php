<?php

require_once("../app/Admin.php");
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
   var_dump($results);
}
function admin($args){
   $cmd = $args[2];
   $admin = new Admin();
   if ($cmd == "load") {
      echo "Loading Hostel Data\n";
      $admin->load_hostel($args[3]);
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

}
function book(){
   $cmd = array_pop($argv);
}

?>
