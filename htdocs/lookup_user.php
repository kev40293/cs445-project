<?php
   function print_user($user){
      printf("<h2>User ID: %s</h2>" , $user["id"]);
      printf("<h3>First Name: %s</h3>" , $user["first_name"]);
      printf("<h3>Last Name: %s</h3>" , $user["last_name"]);
      printf("<h3>Email: %s</h3>" , $user["email"]);
   }
if (isset($_GET['user_id']) and is_numeric($_GET['user_id'])) {
   require_once("../app/Admin.php");
   $admin = new Admin();
   $uinfo = $admin->get_user_info($_GET['user_id']);
   if ($uinfo == null)
      printf("<h1>User not found</h1>");
   else
      print_user($uinfo);
}
?>

<form method="GET">
User ID: <input type="text" name="user_id"><br/>
<input type="submit" value="Lookup">
</form>
