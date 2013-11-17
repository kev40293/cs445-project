<html>
<head>
<title>Admin Panel</title>
</head>
<body>
<?php include("banner.php"); ?>

<h1>Admin Control Panel</h1>
<?php
require_once("../app/Admin.php");
$admin = new Admin();
$occupancy = $admin->get_occupancy();
$revenue = $admin->get_revenue();
?>

   <h3>Occupancy: <?php echo $occupancy . "%"; ?></h3>
   <h3>Revenue: <?php echo "$" . $revenue; ?></h3>

<?php include("lookup_user.php"); ?>
<?php include("add_user_form.php"); ?>
<?php include("load_hostel.php"); ?>
</body>
</head>
