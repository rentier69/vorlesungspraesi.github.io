/*
Beispieldaten für Tabellen
*/

 insert into `vl_benutzer`(`benutzername`, `password`, `aktiv`, `datum_registriert`) values 
 ("dozent",MD5('dozent'),TRUE,CURRENT_TIMESTAMP),
 ("student1",MD5('student1'),TRUE,CURRENT_TIMESTAMP),
 ("student2",MD5('student2'),TRUE,CURRENT_TIMESTAMP);

 INSERT INTO `vl_gruppe`(`Gruppe_Kuerzel`, `Gruppenname`) VALUES 
 ('DOZ_ALL','Alle Dozenten'),
 ('STUD_ALL','Alle Studenten'),
 ('WWI117', 'Wirtschaftsinformatik 1 2017');

 insert into `vl_benutzer_gruppe_map`(`benutzer_id`, `gruppe_id`) values 
 (1,1),
 (2,2),
 (3,2),
 (2,3),
 (3,3);

 insert into `vl_vorlesung`(`vorlesung_name`, `benutzer_id`) values 
 ('Logik und Algebra', 1);

 insert into `vl_vorlesung_gruppe_map`(`vorlesung_id`, `gruppe_id`) values 
 (1,3);

 insert into `vl_vorlesung_frage_typ` (`frage_typ_titel`, `frage_typ_beschreibung`) values 
 ('Freitext', 'Als Antwort kann ein beliebiger Text mit max. 255 Zeichen eingegeben werden.'),
 ('Single Choice', 'Aus gegebenen Optionen kann eine Antwort gewählt werden.'),
 ('Multiple Choice', 'Aus gegebenen Optionen können mehrere Antworten gewählt werden.');