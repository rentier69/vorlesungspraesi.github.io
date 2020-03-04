/*DROP DATABASE `vl`;*/
CREATE DATABASE vl;
USE vl;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

CREATE TABLE IF NOT EXISTS vl_Benutzer
(
   Benutzer_ID INT NOT NULL AUTO_INCREMENT primary key,
   Benutzername VARCHAR(50) NOT NULL,
   Password VARCHAR(50) NOT NULL,
   Aktiv BOOLEAN NOT NULL DEFAULT TRUE,
   Datum_Registriert TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
   Datum_LetzterLogin TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00'
);

CREATE TABLE IF NOT EXISTS vl_Gruppe
(
   Gruppe_ID INT NOT NULL AUTO_INCREMENT primary key,
   Gruppe_Kuerzel VARCHAR(15) NOT NULL,
   Gruppenname VARCHAR(50) NOT NULL
);

CREATE TABLE IF NOT EXISTS vl_Benutzer_Gruppe_Map 
(
   Benutzer_ID INT NOT NULL references vl_Benutzer(Benutzer_ID),
   Gruppe_ID INT NOT NULL references vl_Gruppe(Gruppe_ID),
   primary key (Benutzer_ID, Gruppe_ID)
   /*
   foreign key(Benutzer_ID) references vl_Benutzer(Benutzer_ID),
   foreign key(Gruppe_ID) references vl_Gruppe(Gruppe_ID),
   */
);

CREATE TABLE IF NOT EXISTS vl_Vorlesung 
(
   Vorlesung_ID INT NOT NULL AUTO_INCREMENT primary key,
   Benutzer_ID INT NOT NULL references vl_Benutzer(Benutzer_ID),
   Vorlesung_Name VARCHAR(50) NOT NULL
);

CREATE TABLE IF NOT EXISTS vl_Vorlesung_Gruppe_Map 
(
   Vorlesung_ID INT NOT NULL references vl_Vorlesung(Vorlesung_ID),
   Gruppe_ID INT NOT NULL references vl_Gruppe(Gruppe_ID),
   primary key (Vorlesung_ID, Gruppe_ID)
   /*
   foreign key(Vorlesung_ID) references vl_Vorlesung(Vorlesung_ID),
   foreign key(Gruppe_ID) references vl_Gruppe(Gruppe_ID)
   */
);

CREATE TABLE IF NOT EXISTS vl_Vorlesung_Frage_Typ
(
   Frage_Typ_ID INT NOT NULL AUTO_INCREMENT primary key,
   Frage_Typ_Titel VARCHAR(50) NOT NULL,
   Frage_Typ_Beschreibung VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS vl_Vorlesung_Frage 
(
   Frage_ID INT NOT NULL AUTO_INCREMENT,
   Vorlesung_ID INT NOT NULL references vl_Vorlesung(Vorlesung_ID),
   Frage_Titel VARCHAR(50) NOT NULL,
   Frage_Typ_ID INT NOT NULL references vl_Vorlesung_Frage_Typ(Frage_Typ_ID),
   Fragenummer INT,
   primary key(Frage_ID, Vorlesung_ID)
   /*
   foreign key(Vorlesung_ID) references vl_Vorlesung(Vorlesung_ID),
   foreign key(Frage_Typ_ID) references vl_Vorlesung_Frage_Typ(Frage_Typ_ID)
   */
);

CREATE TABLE IF NOT EXISTS vl_Vorlesung_Frage_AntwortMöglichkeiten 
(
   Frage_ID INT NOT NULL  references vl_Vorlesung_Frage(Frage_ID),
   Antwort VARCHAR(255) NOT NULL,
   primary key(Frage_ID, Antwort)
   /*
   foreign key (Frage_ID) references vl_Vorlesung_Frage(Frage_ID)
   */
);

CREATE TABLE IF NOT EXISTS vl_Vorlesung_Frage_Antworten 
(
   Frage_ID INT NOT NULL references vl_Vorlesung_Frage(Frage_ID),
   Benutzer_ID INT NOT NULL references vl_Benutzer(Benutzer_ID),
   Antwort VARCHAR(255) NOT NULL,
   primary key (Frage_ID, Benutzer_ID)
   /*
   foreign key(Benutzer_ID) references vl_Benutzer(Benutzer_ID),
   foreign key(Frage_ID) references vl_Vorlesung_Frage(Frage_ID)
   */
);

CREATE TABLE IF NOT EXISTS vl_Vorlesung_Bewertung 
(
   Benutzer_ID INT NOT NULL references vl_Benutzer(Benutzer_ID),
   Vorlesung_ID INT NOT NULL references vl_Vorlesung(Vorlesung_ID),
   Bewertung_Zeitstempel TIMESTAMP NOT NULL,
   Bewertung_Rating INT NOT NULL,
   Bewertung_Kommentar VARCHAR(255),
   primary key(Benutzer_ID, Vorlesung_ID, Bewertung_Zeitstempel)
/*
   foreign key(Benutzer_ID) references vl_Benutzer(Benutzer_ID),
   foreign key(Vorlesung_ID) references vl_Vorlesung(Vorlesung_ID)
   */
);

CREATE TABLE IF NOT EXISTS vl_Chat 
(
   Benutzer_ID INT NOT NULL,
   Nachricht_Zeitstempel TIMESTAMP NOT NULL,
   Vorlesung_ID INT NOT NULL references vl_Vorlesung(Vorlesung_ID),
   Nachricht VARCHAR(255) NOT NULL,
   primary key(Benutzer_ID, Nachricht_Zeitstempel, Vorlesung_ID)
   /*foreign key(Vorlesung_ID) references vl_Vorlesung(Vorlesung_ID)*/
);