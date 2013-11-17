create table Customer (
   id integer primary key auto_increment,
   first_name varchar(100) not null,
   last_name varchar(100) not null,
   email varchar(100) not null,
   cc_number char (16),
   expiration_date date,
   security_code integer,
   phone char(20)
);

create table Hostels (
   id integer primary key auto_increment,
   name varchar(200) not null unique,
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
);

create table Availability (
   id integer primary key auto_increment,
   av_date date not null,
   room_number integer not null,
   beds integer not null,
   price integer not null,
   hostel_id integer not null,
   foreign key (hostel_id) references Hostels(id),
   check (beds >= 0 and price > 0)
);


create table Reservations(
   id integer auto_increment,
   avail_id integer not null,
   customer_id integer not null,
   quantity integer not null,
   primary key (id, avail_id, customer_id),
   foreign key (customer_id) references Customer(id),
   foreign key (avail_id) references Availability(id),
   check (quantity > 0)
);
