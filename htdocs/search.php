<html>
<head>
<title>Search Results</title>
<script type="text/javascript">
function toggleCombo(check, id){
   el = document.getElementById(id);
   if (check.checked) {
      el.style.display = 'inline';
   }
   else {
      el.style.display = 'none';
   }
}
</script>
   <style type="text/css">
      select {
         display: none;
      }
</style>
</head>
<body>
<?php include("banner.php"); ?>

<h1> Search Results </h1>
<?php

$search_options = array();
if ($_GET['s_year'] != "" and $_GET['s_month'] != "" and $_GET['s_day'] != "") {
   $search_options['start_date'] =
      sprintf("%s%02d%02d", $_GET['s_year'] , $_GET['s_month'] , $_GET['s_day']);
}
if ($_GET['e_year'] != "" and $_GET['e_month'] != "" and $_GET['e_day'] != "") {
   $search_options['end_date'] =
      sprintf("%s%02d%02d", $_GET['e_year'] , $_GET['e_month'] , $_GET['e_day']);
}
if (is_numeric($_GET['beds'])){
   $search_options['num'] = $_GET['beds'];
}
if ($_GET['city'] != "") {
   $search_options['city'] = $_GET['city'];
}

require("../app/Search.php");
$search = new Search($search_options);
$results = $search->get_results();
function print_results($results){
   $hostels = array();
   foreach ($results as $id => $sr) {
      $hostels[$sr["hostel"]][$sr["date"]][$sr["room"]] =
         array($id, $sr["bed"], $sr["price"]);
   }
   foreach($hostels as $hos => $dates){
      print "<ul>Hostel: $hos\n";
      foreach ($dates as $date => $rooms) {
         $pdate = DateTime::createFromFormat('Ymd',$date, new DateTimeZone('UTC'));
         $nice_date = $pdate->format('m/d/Y');
         print "<li><ul>Date: $nice_date\n";
         foreach ($rooms as $room => $listing) {
            print "<li>Room: $room\n";
            print "<br/>". $listing[0] . ": " . $listing[1] . " at $" . $listing[2]. "\n";
?>
   <input type="checkbox" name="<?php echo $listing[0] ?>" value="Y"
      onclick="toggleCombo(this,<?php echo $listing[0]; ?>)">
   <select name="qty[<?php echo $listing[0]; ?>]" id="<?php echo $listing[0]; ?>">
<?php
            foreach (range(0,$listing[1]) as $n) {
               printf('<option value="%d" >%d', $n, $n);
            }
?>
   </select>
<?php
         }
         print "</ul></li>";
      }
      print "</ul>\n";
   }
}
?>
<form method="POST" action="book.php">
<?php print_results($results); ?>
Customer ID: <input type="text" name="customer_id">
<input type="submit" value="Book">
</form>

<?php
var_dump($_POST);
?>
</body>
</html>
