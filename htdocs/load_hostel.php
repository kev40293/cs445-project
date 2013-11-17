<?php
if (isset($_FILES['upload'])){
   require_once("../app/Admin.php");
   $admin = new Admin();
   $admin->load_hostel($_FILES['upload']['tmp_name']);
}
?>
<form method="POST" enctype="multipart/form-data">
<input type="file" name="upload">
<input type="submit" value="load">
</form>
