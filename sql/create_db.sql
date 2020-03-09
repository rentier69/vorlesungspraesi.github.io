/*drop database `vl`;
create database vl;
use vl;*/
set sql_mode = "no_auto_value_on_zero";

create table if not exists vl_benutzer
(
   benutzer_id int not null auto_increment primary key,
   benutzername varchar(50) not null,
   password varchar(50) not null,
   aktiv boolean not null default true,
   datum_registriert timestamp not null default current_timestamp,
   datum_letzterlogin timestamp not null default '0000-00-00 00:00:00'
);

create table if not exists vl_gruppe
(
   gruppe_id int not null auto_increment primary key,
   gruppe_kuerzel varchar(15) not null,
   gruppenname varchar(50) not null
);

create table if not exists vl_benutzer_gruppe_map 
(
   benutzer_id int not null references vl_benutzer(benutzer_id),
   gruppe_id int not null references vl_gruppe(gruppe_id),
   primary key (benutzer_id, gruppe_id),
   foreign key(benutzer_id) references vl_benutzer(benutzer_id),
   foreign key(gruppe_id) references vl_gruppe(gruppe_id)
);

create table if not exists vl_vorlesung 
(
   vorlesung_id int not null auto_increment primary key,
   benutzer_id int not null references vl_benutzer(benutzer_id),
   vorlesung_name varchar(50) not null
);

create table if not exists vl_vorlesung_gruppe_map 
(
   vorlesung_id int not null references vl_vorlesung(vorlesung_id),
   gruppe_id int not null references vl_gruppe(gruppe_id),
   primary key (vorlesung_id, gruppe_id),
   foreign key(vorlesung_id) references vl_vorlesung(vorlesung_id),
   foreign key(gruppe_id) references vl_gruppe(gruppe_id)
);

create table if not exists vl_vorlesung_frage_typ
(
   frage_typ_id int not null auto_increment primary key,
   frage_typ_titel varchar(50) not null,
   frage_typ_beschreibung varchar(100) not null
);

create table if not exists vl_vorlesung_frage 
(
   frage_id int not null auto_increment,
   vorlesung_id int not null references vl_vorlesung(vorlesung_id),
   frage_titel varchar(50) not null,
   frage_typ_id int not null references vl_vorlesung_frage_typ(frage_typ_id),
   fragenummer int,
   primary key(frage_id, vorlesung_id),
   foreign key(vorlesung_id) references vl_vorlesung(vorlesung_id),
   foreign key(frage_typ_id) references vl_vorlesung_frage_typ(frage_typ_id)
);

create table if not exists vl_vorlesung_frage_antwortmoeglichkeiten 
(
   frage_id int not null  references vl_vorlesung_frage(frage_id),
   antwort varchar(255) not null,
   primary key(frage_id, antwort),
   foreign key (frage_id) references vl_vorlesung_frage(frage_id)
);

create table if not exists vl_vorlesung_frage_antworten 
(
   frage_id int not null references vl_vorlesung_frage(frage_id),
   benutzer_id int not null references vl_benutzer(benutzer_id),
   antwort varchar(255) not null,
   primary key (frage_id, benutzer_id),
   foreign key(benutzer_id) references vl_benutzer(benutzer_id),
   foreign key(frage_id) references vl_vorlesung_frage(frage_id)
);

create table if not exists vl_vorlesung_bewertung 
(
   benutzer_id int not null references vl_benutzer(benutzer_id),
   vorlesung_id int not null references vl_vorlesung(vorlesung_id),
   bewertung_zeitstempel timestamp not null,
   bewertung_rating int not null,
   bewertung_kommentar varchar(255),
   primary key(benutzer_id, vorlesung_id, bewertung_zeitstempel),
   foreign key(benutzer_id) references vl_benutzer(benutzer_id),
   foreign key(vorlesung_id) references vl_vorlesung(vorlesung_id)
);

create table if not exists vl_chat 
(
   benutzer_id int not null,
   nachricht_zeitstempel timestamp not null,
   vorlesung_id int not null references vl_vorlesung(vorlesung_id),
   nachricht varchar(255) not null,
   primary key(benutzer_id, nachricht_zeitstempel, vorlesung_id),
   foreign key(vorlesung_id) references vl_vorlesung(vorlesung_id)
);