/*
Beispieldaten für Tabellen
*/

 INSERT INTO `vl_Benutzer`(`Benutzername`, `Password`, `Aktiv`, `Datum_Registriert`) VALUES 
 ("dozent",MD5('dozent'),TRUE,CURRENT_TIMESTAMP),
 ("student1",MD5('student1'),TRUE,CURRENT_TIMESTAMP),
 ("student2",MD5('student2'),TRUE,CURRENT_TIMESTAMP);

 INSERT INTO `vl_Gruppe`(`Gruppe_Kuerzel`, `Gruppenname`) VALUES 
 ('DOZ_ALL','Alle Dozenten'),
 ('STUD_ALL','Alle Studenten'),
 ('WWI117', 'Wirtschaftsinformatik 1 2017');

 INSERT INTO `vl_Benutzer_Gruppe_Map`(`Benutzer_ID`, `Gruppe_ID`) VALUES 
 (1,1),
 (2,2),
 (3,2),
 (2,3),
 (3,3);

 INSERT INTO `vl_Vorlesung`(`Vorlesung_Name`, `Benutzer_ID`) VALUES 
 ('Logik und Algebra', 1);

 INSERT INTO `vl_Vorlesung_Gruppe_Map`(`Vorlesung_ID`, `Gruppe_ID`) VALUES 
 (1,3);

 INSERT INTO `vl_Vorlesung_Frage_Typ` (`Frage_Typ_Titel`, `Frage_Typ_Beschreibung`) VALUES 
 ('Freitext', 'Als Antwort kann ein beliebiger Text mit max. 255 Zeichen eingegeben werden.'),
 ('Single Choice', 'Aus gegebenen Optionen kann eine Antwort gewählt werden.');