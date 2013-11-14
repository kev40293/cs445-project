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

function search ($argv) {
   for($ind = 2; $ind < sizeof($argv); $ind+=2 ){ 
      $opts[trim($argv[$ind],"-")] = $argv[$ind+1];
   }
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
function user($argv){
   $cmd = array_pop($argv);
}
function book(){
   $cmd = array_pop($argv);
}

?>
