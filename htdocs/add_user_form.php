<?php
if (isset($_POST['first_name'])){
   require_once("../app/Admin.php");
   $admin = new Admin();
   $optional = array();
   foreach (array("cc_number", "expiration_date", "security_code", "phone") as $oparg){
      if (isset($_POST[$oparg]))
         $optional[$oparg] = $_POST[$oparg];
   }
   $uid = $admin->create_user(
      $_POST['first_name'],
      $_POST['last_name'],
      $_POST['email'],
      $optional
   );
   $uinfo = $admin->get_user_info($uid);
   function print_user($user){
      printf("<h2>User ID: %s</h2>" , $user["id"]);
      printf("<h3>First Name: %s</h3>" , $user["first_name"]);
      printf("<h3>Last Name: %s</h3>" , $user["last_name"]);
      printf("<h3>Email: %s</h3>" , $user["email"]);
   }
   print_user($uinfo);
}
?>
<form method="POST">
First Name:<input type="text" name="first_name"></br>
Last Name:<input type="text" name="last_name"></br>
Email:<input type="text" name="email"></br>
Optional </br>
Credit Card Number:<input type="text" name="cc_number"></br>
Expiration Date:<input type="date" name="security_code"></br>
Security Code:<input type="text" name="security_code"></br>
Phone:<input type="phone" name="phone"> </br>
<input type="submit" value="Add">
</form>
