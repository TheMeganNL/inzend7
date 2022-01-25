CREATE DATABASE verenigingsleden;
USE verenigingsleden;

CREATE TABLE postcode(
postcode CHAR(6) NOT NULL,
adres VARCHAR(128) NOT NULL,
woonplaats VARCHAR(128) NOT NULL,
PRIMARY KEY (postcode)
) ENGINE INNODB;

INSERT INTO postcode(postcode,adres,woonplaats) VALUES
('1212LE','Lerkeind','Doorn'),
('3434ZX','Wakkerdam','Doorn'),
('5656RE','Rezolade','Meidoorn');

CREATE TABLE lid (
lidnummer INT UNSIGNED NOT NULL AUTO_INCREMENT,
naam VARCHAR(128) NOT NULL,
voornaam VARCHAR(128) NOT NULL,
postcode CHAR(6) NOT NULL,
huisnummer VARCHAR(20) NOT NULL,
PRIMARY KEY (lidnummer),
FOREIGN KEY (postcode) REFERENCES postcode(postcode)
)ENGINE INNODB;

INSERT INTO lid(naam,voornaam,postcode,huisnummer) VALUES 
('Vollens','Theresa','1212LE','86'),
('Hamoen','Gert','3434ZX','52'),
('Klinkers','Sarah','5656RE','4'),
('Veulens','Thea','1212LE','12'),
('Gollens','Tommy','3434ZX','90'),
('Nadal','Saartje','5656RE','496'),
('Post','Mark','1212LE','8856'),
('Ganzen','Leon','3434ZX','34a'),
('Janssen','Thomas','5656RE','42'),
('Mans','Theo','1212LE','20'),
('Groen','Klaus','3434ZX','152'),
('Daal','Suriel','5656RE','4');

CREATE TABLE email(
emailadres VARCHAR(128) NOT NULL,
lidnummer INT UNSIGNED NOT NULL,
PRIMARY KEY (emailadres),
FOREIGN KEY (lidnummer) REFERENCES lid(lidnummer)
) ENGINE INNODB;

INSERT INTO email(emailadres,lidnummer) VALUES 
('tvollens@gmail.com','1'),
('gerthamoen@hotmail.com','2'),
('sarklink@live.nl','3'),
('theaveul@gmail.com','4'),
('gollenstommy@hotmail.com','5'),
('saarnad@live.nl','6'),
('markpostie@gmail.com','7'),
('lganzen@hotmail.com','8'),
('thoja@live.nl','9'),
('theomans@gmail.com','10'),
('klaugroe@hotmail.com','11'),
('suriedaal@live.nl','12');

CREATE TABLE telefoonnummers(
telefoonnummer VARCHAR(10) NOT NULL,
lidnummer INT UNSIGNED NOT NULL,
PRIMARY KEY (telefoonnummer),
FOREIGN KEY (lidnummer) REFERENCES lid(lidnummer)
) ENGINE INNODB;

INSERT INTO telefoonnummers(telefoonnummer,lidnummer) VALUES
('0687456661','1'),
('0623574568','2'),
('0694026157','3'),
('0687456625','4'),
('0623574548','5'),
('0694026193','6'),
('0687456601','7'),
('0623574564','8'),
('0694026107','9'),
('0687456690','10'),
('0623574512','11'),
('0694026163','12');

CREATE TABLE teams (
teamnaam VARCHAR(32) NOT NULL,
omschrijving VARCHAR (128) NOT NULL,
PRIMARY KEY (teamnaam))ENGINE INNODB;

INSERT INTO teams (teamnaam,omschrijving) VALUES
('team1','5-9 jarige'),
('team2','10-14 jarige'),
('team3','15-18 jarige');

CREATE TABLE teamlid(
tl_ID INT NOT NULL AUTO_INCREMENT,
teamnaam VARCHAR(32) NOT NULL,
lidnummer INT UNSIGNED NOT NULL,
PRIMARY KEY (tl_ID),
FOREIGN KEY (teamnaam) REFERENCES teams(teamnaam),
FOREIGN KEY (lidnummer) REFERENCES lid(lidnummer))ENGINE INNODB;

INSERT INTO teamlid (teamnaam,lidnummer) VALUES
('team1','1'),
('team1','2'),
('team1','3'),
('team1','4'),
('team2','5'),
('team2','6'),
('team2','7'),
('team2','8'),
('team3','9'),
('team3','10'),
('team3','11'),
('team3','12');
