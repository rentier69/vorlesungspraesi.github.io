/*create table kurs
(
kursname varchar(50) not null,
kuerzel char(6) primary key
);

create table student (
matnr char(7) primary key,
name varchar(50) not null,
kurs char (6) not null,
foreign key(kurs) references kurs(kuerzel)
);

create table anwender (
name varchar(100) primary key,
passwort varchar(100) not null
);

create table fragebogen (
titel varchar(100) primary key,
name varchar(100) not null,
foreign key(name) references anwender(name)
);

create table frage(
f_id int not null auto_increment primary key,
frage varchar(300) not null,
titel varchar(100) not null,
foreign key(titel) references fragebogen(titel)
);*/

create table 