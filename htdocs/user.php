<html>
<head>
<title>User</title>
</head>
<body>
<?php include("banner.php"); ?>
<h1>Reservation Lookup</h1>
<form method="GET">
User ID<input type="text" name="user_id">
Reservation ID<input type="text" name="resv_id">
<input type="submit" value="Lookup">
</form>

<?php
require ("../app/Customer.php");

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

if (isset($_POST['user_id'])) {
   if (is_numeric($_GET['user_id']) and is_numeric($_GET['resv_id'])){
      $customer = new Customer($_GET['user_id']);
      $res_info = $customer->cancel_reservation($_GET['resv_id']);
      printf("<h1>Reservation %d canceled</h1>", $_GET['resv_id']);
   }
}
else if (isset($_GET['user_id'])){
   if (is_numeric($_GET['user_id']) and is_numeric($_GET['resv_id'])){
      printf("Retrieving reservation info");
      $customer = new Customer($_GET['user_id']);
      $res_info = $customer->get_reservation_info($_GET['resv_id']);
      if ($res_info == null){
         printf("<h1>Reservation not found</h1>");
      }
      else {
         print_reservation($res_info);
?>
<form method="POST" >
<input type="hidden" name="user_id" value="<?php echo $_GET['user_id']; ?>" >
<input type="hidden" name="user_id" value="<?php echo $_GET['resv_id']; ?>" >
<input type="submit" value="Cancel">
</form>
<?php
      }
      unset($_GET);
   }
}
