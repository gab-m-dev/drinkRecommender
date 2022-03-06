#create DATABASE dbTest;
#-----------------------
create DATABASE dbTest;
use dbTest;

#Create table Drinks and Import Data from csv
#---------------------------------------------
create table Drinks(
    name VARCHAR(70),
    category VARCHAR(30),
    instructions text,
    glass VARCHAR(30),
    alcohol VARCHAR(25),
    Ingrediants JSON);

LOAD DATA INFILE '/mnt/hgfs/Shared Folder/cocktails_forDB_new.csv' INTO TABLE Drinks FIELDS TERMINATED BY ',' ENCLOSED BY '"' LINES TERMINATED BY '\r\n';
DELETE FROM Drinks WHERE name = 'name';
ALTER TABLE Drinks ADD ID INT(10)AUTO_INCREMENT NOT NULL PRIMARY KEY;

#Create table UsageData
#---------------------------------------------
create table UsageData(
    similarDrinks INT(15),
    searchedDrinks INT(15)
);

INSERT INTO UsageData (similarDrinks, searchedDrinks) VALUES (0,0);

#Create table Drinks_Similarity and Import Data from csv
#------------------------------------------------------
create table Drinks_Sim_temp(
    Drink1 VARCHAR(70),
    Drink2 VARCHAR(70),
    Value DOUBLE);

LOAD DATA INFILE '/mnt/hgfs/Shared Folder/cocktails_similarity_forDB_new.csv' INTO TABLE Drinks_Sim_temp FIELDS TERMINATED BY ',' ENCLOSED BY '"' LINES TERMINATED BY '\r\n';

create table Drinks_Sim_Inst_temp(
    Drink1 VARCHAR(70),
    Drink2 VARCHAR(70),
    Value DOUBLE);

LOAD DATA INFILE '/mnt/hgfs/Shared Folder/cocktails_similarity_instructions_forDB_new.csv' INTO TABLE Drinks_Sim_Inst_temp FIELDS TERMINATED BY ',' ENCLOSED BY '"' LINES TERMINATED BY '\r\n';

#Indizes für Temp Tabellen erstellen
CREATE INDEX i_name_sim_Inst1 ON Drinks_Sim_Inst_temp(Drink1);
CREATE INDEX i_name_sim_Inst2 ON Drinks_Sim_Inst_temp(Drink2);
CREATE INDEX i_name_sim_Ing1 ON Drinks_Sim_temp(Drink1);
CREATE INDEX i_name_sim_Ing2 ON Drinks_Sim_temp(Drink2);

#Similarity zusammenführen
ALTER TABLE Drinks_Sim_Inst_temp ADD ID INT(10)AUTO_INCREMENT NOT NULL PRIMARY KEY;
ALTER TABLE Drinks_Sim_temp ADD ID INT(10)AUTO_INCREMENT NOT NULL PRIMARY KEY;
ALTER TABLE Drinks_Sim_temp ADD Value_Instructions double;

UPDATE Drinks_Sim_temp AS A INNER JOIN Drinks_Sim_Inst_temp AS B ON A.ID = B.ID
    SET A.Value_Instructions = B.Value
    where A.Drink1 = B.Drink1 AND A.Drink2 = B.Drink2;


create table Drinks_Similarity(
    Similarity_ID INT(10) AUTO_INCREMENT NOT NULL PRIMARY KEY,
    fk_Drink1 INT(11) not null ,
    fk_Drink2 INT(11) not null ,
    Value_Ingrediants FLOAT not null ,
    Value_Instructions FLOAT not null,
    constraint fk_drink1 FOREIGN KEY (fk_Drink1) REFERENCES Drinks(ID),
    constraint fk_drink2 FOREIGN KEY (fk_Drink2) REFERENCES Drinks(ID));


#Indizes für Name erstellen, sonst wartet man ewig (O^2)
CREATE INDEX i_name ON Drinks(name);

INSERT INTO Drinks_Similarity (fk_Drink1, fk_Drink2, Value_Ingrediants, Value_Instructions)
SELECT D.ID, D2.ID, ROUND(D_S.Value, 5), ROUND(D_S.Value_Instructions, 5)
  FROM Drinks_Sim_temp as D_S, Drinks as D, Drinks as D2 where D_S.Drink1 = D.name and D_S.Drink2 = D2.name;

CREATE INDEX i_name_sim1 ON Drinks_Similarity(fk_Drink1);
CREATE INDEX i_name_sim2 ON Drinks_Similarity(fk_Drink2);

#For Test
select * from Drinks_Similarity ORDER BY fk_Drink1 DESC LIMIT 50;
select * from Drinks_Similarity limit 100;

#Drop temp Table
#---------------
drop TABLE Drinks_Sim_Inst_temp,Drinks_Sim_temp;


#Test get Similar Drinks as JSON
select JSON_OBJECT(
    'DrinkID', D_S.fk_Drink2,
    'Name', D2.name,
    'Similarity_Ingrediants',D_S.Value_Ingrediants,
    'Similarity_Ingrediants', D_S.Value_Instructions
    )
from Drinks D,Drinks_Similarity D_S, Drinks D2 where D.name = 'whiskey sOur' and D.ID = D_S.fk_Drink1 AND D2.ID = D_S.fk_Drink2 ORDER BY Value_Ingrediants DESC limit 0,10

#Test get Drinks Properties as JSON
select JSON_OBJECT(
    'Name', name,
    'Ingrediants', Ingrediants,
    'Category', category,
    'Alcohol', alcohol,
    'Glass', glass,
    'Instructions', instructions
    ) from Drinks where name = 'Whiskey Sour'


select * from Drinks where name = 'mojito';

SELECT * FROM Drinks WHERE LOCATE('mint',LOWER(Ingrediants)) AND LOCATE('vodka',LOWER(Ingrediants));

# Richtige Formatierung der Zutaten als JSON
Select JSON_CONTAINS(JSON_EXTRACT(lower(REPLACE(Ingrediants, '\'', '\"')), '$'), 4, '$') from Drinks
