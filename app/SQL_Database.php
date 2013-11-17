<?php

class SQL_Database {
   private $dbcon;
   public function __construct(){
      $this->dbconn = mysqli_connect("localhost", "cs445", "", "cs445");
      if (mysqli_connect_errno()) {
         printf("Connect failed: %s", mysqli_connect_error());
         exit(1);
      }
   }
   public function init(){
      $this->dbconn->query("DROP table if exists Reservations;");
      $this->dbconn->query("DROP table if exists Availability;");
      $this->dbconn->query("DROP table if exists Hostels;");
      $this->dbconn->query("DROP table if exists Customer;");
      $this->dbconn->commit();
      $this->create_tables();
   }
   public function open(){}

   public function add_customer($fname, $lname, $email, $cc_info){
      $stmt = $this->dbconn->prepare("Insert into Customer ".
         "(first_name, last_name, email,
         cc_number, expiration_date, security_code, phone) " .
         "VALUES (?, ? ,?, ?, ?, ?, ?);");

      foreach (array("cc_number", "expiration_date", "security_code", "phone") as $a){
         if (! isset($cc_info[$a])) {
            $cc_info[$a] = null;
         }
      }
      $stmt->bind_param("sssssis", $fname, $lname, $email,
         $cc_info['cc_number'], $cc_info['expiration_date'],
         $cc_info['security_code'], $cc_info['phone']);
      $stmt->execute();
      $stmt->close();
      return mysqli_insert_id($this->dbconn);

   }
   public function update_customer($cust, $options){
      $binds = array();
      $args[0] = implode("",array_fill(0, sizeof($options) ,"s")) . "i";
      foreach ($options as $col => &$val){
         $binds[] = "$col=? ";
         $args[] = &$val;
      }
      $args[] = &$cust;
      $stmt = $this->dbconn->prepare("update Customer set ".
          implode(",", $binds) . " where id = ?");
      call_user_func_array(array($stmt, 'bind_param'), $args);
      $stmt->execute();
      $stmt->close();

   }
   public function get_customer_info($cust_id){
      $stmt = $this->dbconn->prepare(
         "Select first_name, last_name, email, ".
         "cc_number, expiration_date, security_code, phone ".
         "from Customer where id=?;");
      $stmt->bind_param("i", $cust_id);
      $stmt->execute();
      $res = array();
      $stmt->bind_result(
         $res['first_name'], $res['last_name'], $res['email'], 
         $res['cc_number'], $res['expiration_date'],
         $res['security_code'], $res['phone']);
      $stmt->fetch();
      return $res;
   }

   public function add_availability($hostel, $d, $rn, $num, $price){
      $stmt = $this->dbconn->prepare(
         "INSERT INTO Availability ".
         "(av_date, room_number, beds, price, hostel) " .
         "VALUES (?, ? ,?, ?, ?);");
      $format_date = DateTime::createFromFormat('Ymd', $d, new DateTimeZone('UTC'));
      $stmt->bind_param("siiis", $format_date->format("Y-m-d"), $rn, $num, $price, $hostel);
      $stmt->execute();
      $stmt->close();
      return mysqli_insert_id($this->dbconn);
   }
   public function get_available_space($avail_id){
      $stmt = $this->dbconn->prepare("select beds from Availability where id=?;");
      $stmt->bind_param("i",$avail_id);
      $stmt->execute();
      $stmt->bind_result($beds);
      mysqli_stmt_fetch($stmt);
      $stmt->close();
      return $beds;
   }
   public function search_availability($param){
      $conditions = array();
      $bind_arg = "";
      $args = array();
      if ($param['start_date'] != null){
         $conditions[] = "(av_date >= ? and av_date <= ?)";
         $bind_arg .= "ss";
         $args[] = &$param['start_date'];
         $args[] = &$param['end_date'];
      }
      if (! isset($param['num'])) {
         $param['num'] = 0;
      }
      $conditions[] = "beds >= ?";
      $args[] =  &$param['num'];
      $bind_arg .= "i";


      if (isset($param['city']) and $param['city'] != null){
         $conditions[] = "(city = ?)";
         $bind_arg .= "s";
         $args[] = &$param['city'];
      }

      $cond_str = "where " . implode (" and ", $conditions);
      $stmt = $this->dbconn->prepare("select id, room_number, av_date, beds, price, hostel " .
         "from Availability join Hostels $cond_str;");
      array_unshift($args, $bind_arg);
      call_user_func_array(array($stmt, 'bind_param'),$args);
      $stmt->execute();
      $result = array();
      $res = array();
      $stmt->bind_result(
         $res['id'], $res['room'], $res['date'], $res['bed'], $res['price'], $res['hostel']
      );
      while (mysqli_stmt_fetch($stmt) != null) {
         $res_copy = array();
         foreach ($res as $key => $val){
            $res_copy[$key] = $val;
         }
         $res_copy['date'] = date_format(date_create_from_format('Y-m-d', $res_copy['date'], new DateTimeZone('UTC')), 'Ymd');
         $result[$res_copy['id']] = $res_copy;
      }
      $stmt->close();
      return $result;
   }

   public function make_reservation($cust_id, $avail, $num, $rid=0){
      $stmt2 = $this->dbconn->prepare("update Availability ".
         "set beds = beds - ? where id = ?");
      $stmt2->bind_param("ii", $num, $avail);
      $stmt2->execute();
      $stmt2->close();

      $stmt = $this->dbconn->prepare("insert into Reservations " .
            "(avail_id, customer_id, quantity) " .
            "VALUES (?, ?, ?);");
      $stmt->bind_param("iii", $avail, $cust_id, $num);
      if ($rid > 0) {
         $stmt = $this->dbconn->prepare("insert into Reservations " .
            "(id, avail_id, customer_id, quantity) " .
            "VALUES (?, ?, ?, ?);");
         $stmt->bind_param("iiii", $rid, $avail, $cust_id, $num);
      }
      $stmt->execute();
      $stmt->close();
      $res_id = mysqli_insert_id($this->dbconn);

      return $res_id;
   }
   public function delete_reservation($cust_id, $resv_id){
      $count_stmt = $this->dbconn->prepare("select quantity, avail_id from Reservations ".
         "where id = ? and customer_id = ?");
      $count_stmt->bind_param("ii", $resv_id, $cust_id);
      $count_stmt->execute();
      $count_stmt->bind_result($qty, $avail);
      $count_stmt->fetch();
      $count_stmt->close();

      $del_stmt = $this->dbconn->prepare("delete from Reservations " .
         "where id = ?");
      $del_stmt->bind_param("i", $resv_id);
      $del_stmt->execute();
      $del_stmt->close();

      $update_stmt = $this->dbconn->prepare("update Availability ".
         "set beds = beds + ? where id = ?");
      $update_stmt->bind_param("ii", $qty, $avail);
      $update_stmt->execute();
      $update_stmt->close();
   }
   public function get_reservation($res_id){
      $stmt = $this->dbconn->prepare(
         "select first_name, last_name, quantity, " .
         "Availability.id, room_number, av_date, beds, price, hostel from " .
         "Reservations left join Availability on " .
         " Availability.id = Reservations.avail_id " .
         "join Hostels join Customer " .
         "where Reservations.id = ?");
      $stmt->bind_param('i', $res_id);
      $stmt->execute();

      $result = array("id" => $res_id, "price" => 0 );
      $stmt->bind_result(
         $result['first_name'], $result['last_name'], $quantity,
         $avail['id'], $avail['room'], $avail['date'], $avail['bed'], $avail['price'],
         $avail['hostel']);
      while ($stmt->fetch() != null) {
         $av['qty'] = $quantity;
         foreach ($avail as $key => $val) {
            $av[$key] = $val;
         }
         $av['date'] =
            date_format(date_create_from_format('Y-m-d', $av['date'], new DateTimeZone('UTC')), 'Ymd');
         $result['bookings'][] = $av;
         $result['price'] += $av['price'] * $av['qty'];
      }
      return (isset($result['bookings']) ? $result : null);
   }

   public function get_hostels($param){}
   public function add_hostel($name, $address, $contact, $restrict){
      $stmt = $this->dbconn->prepare(
         "insert into Hostels " .
         "(name, phone, email, facebook, web, check_in_time, check_out_time, ".
         "smoking, alcohol, street, city, state, postal_code, country)" .
         "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);");
      $smoke = ($restrict->smoking == "Y" ? 1 : 0);
      $alc = ($restrict->alcohol == "Y" ? 1 : 0);
      $stmt->bind_param("sssssssiisssss",
         $name,
         $contact->phone, $contact->email, $contact->facebook, $contact->web,
         $restrict->check_in_time, $restrict->check_out_time,
         $smoke, $alc,
         $address->street, $address->city, $address->state,
         $address->postal_code, $address->country);

      $stmt->execute();
      $stmt->close();
      return new Hostel($name, $address, $contact, $restrict);
   }

   public function get_revenue(){
      $res = $this->dbconn->query(
         "select sum(price * quantity) as revenue from " .
         "Reservations join Availability ".
         "where Reservations.avail_id = Availability.id;");
      $row = mysqli_fetch_row($res);
      return $row[0];
   }
   public function get_occupancy(){
      $res = $this->dbconn->query(
         "select (occupied / total) from ".
         "(select sum(quantity) as occupied, sum(ifnull(quantity,0) + beds) as total ".
         "from Availability left outer join Reservations on ".
         "Reservations.avail_id = Availability.id) as sums;"
      );
      $row = mysqli_fetch_row($res);
      return $row[0];
   }

   private function create_tables() {
$customer_table = "
create table Customer (
   id integer primary key auto_increment,
   first_name varchar(100) not null,
   last_name varchar(100) not null,
   email varchar(100) not null,
   cc_number char (16),
   expiration_date date,
   security_code integer,
   phone char(20)
);";

$hostel_table = "
create table Hostels (
   name varchar(200) primary key,
   phone varchar(20),
   email varchar(100),
   facebook varchar(100),
   web varchar(100),
   check_in_time varchar(100),
   check_out_time varchar(100),
   smoking boolean,
   alcohol boolean,
   street varchar(100),
   city varchar(100),
   state varchar(100),
   postal_code varchar(10),
   country varchar(10)
);";

$availability_table = "
create table Availability (
   id integer primary key auto_increment,
   av_date date not null,
   room_number integer not null,
   beds integer not null,
   price integer not null,
   hostel varchar(200) references Hostels(name),
   check (beds >= 0 and price > 0)
);";

$reservation_table = "
create table Reservations(
   id integer auto_increment,
   avail_id integer not null,
   customer_id integer not null,
   quantity integer not null,
   primary key (id, avail_id, customer_id),
   foreign key (customer_id) references Customer(id),
   foreign key (avail_id) references Availability(id),
   check (quantity > 0)
);";
      $this->dbconn->query($customer_table);
      $this->dbconn->query($hostel_table);
      $this->dbconn->query($availability_table);
      $this->dbconn->query($reservation_table);
   }
}
