create table Customer (
   id integer primary key
);

create table Hostels (
   id integer primary key,
   location varchar(255) not null
);

create table Reservations(
   id integer primary key,
   customer_id integer not null,
   hostel_id integer not null,
   foreign key (customer_id) references Customer(id),
   foreign key (hostel_id) references Hostels(id)
);

create table Rooms (
   room_number integer not null,
   hostel_id integer not null,
   number_beds integer not null,
   foreign key (hostel_id) references Hostels(id),
   primary key (room_number, hostel_id)
);

create table Admin (
   id integer primary key,
   username char(16) not null unique,
   password char(255) not null
);
