<html>
<head>
<title>Search Results</title>
</head>
<body>
<?php include("banner.php"); ?>

<?php
//var_dump($_POST);
require("../app/Customer.php");
$res_id = 0;
$customer = new Customer($_POST["customer_id"]);
echo "<ul>";
foreach ($_POST as $key => $postdata) {
   if (is_numeric($key) and $postdata == "Y"){
      $res_id = $customer->make_reservation($key, $_POST["qty"][$key], $res_id);
      printf("<li>Booked id %d with %d beds</li>", $key, $_POST["qty"][$key]);
   }
}
echo "</ul>";

$res_info = $customer->get_reservation_info($res_id);
function print_reservation($reservation){
   print "<h1>Booking ID: " . $reservation["id"] . "</h1>";
   printf("<h3>Name: %s %s</h3>", $reservation["first_name"], $reservation["last_name"]);
   foreach ($reservation["hostel"] as $hostel => $dates) {
      print "$hostel\n";
      foreach ($dates as $date => $qty) {
         $pdate = DateTime::createFromFormat('Ymd',$date, new DateTimeZone('UTC'));
         printf("<h4>Date: %s", $pdate->format("m/d/Y"));
         printf("      Number Beds: %d </h4>", $qty);
      }
   }
   //var_dump($reservation);
}

print_reservation($res_info);
?>
</body>
</html>

