-- régi táblák eldobása
DROP TABLE szoba_kerdesei;
DROP TABLE eredmeny;
DROP TABLE szoba;
DROP TABLE valasz;
DROP TABLE kerdes;
DROP TABLE tema;
DROP TABLE felhasznalo;


-- felhasznaló tábla létrehozása
CREATE TABLE felhasznalo (
    id NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    nev VARCHAR2(50),
    email VARCHAR2(100),
    jelszo VARCHAR2(100),
    admin_e INTEGER
);

-- Téma tábla létrehozása
CREATE TABLE tema (
    id NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    nev VARCHAR2(50)
);

-- Kérdés tábla létrehozása
CREATE TABLE kerdes (
    id NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    kerdes VARCHAR2(255),
    tema_id NUMBER , 
    FOREIGN KEY (tema_id) REFERENCES tema(id) ON DELETE SET NULL 
);

-- Válasz tábla létrehozása
CREATE TABLE valasz (
    id NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    kerdes_id NUMBER,
    valasz VARCHAR2(255),
    helyes_e INTEGER,
    FOREIGN KEY (kerdes_id) REFERENCES kerdes(id) ON DELETE CASCADE
);

-- Szobák Létrehozása
CREATE TABLE szoba (
    id NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    nev VARCHAR2(50),
    jelszo VARCHAR2(100),
    felhasznalo_id NUMBER,
    FOREIGN KEY (felhasznalo_id) REFERENCES felhasznalo(id) ON DELETE CASCADE
);

-- Felhasználók eredményei
CREATE TABLE eredmeny (
    id NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    szoba_id NUMBER,
    felhasznalo_id NUMBER,
    pontszam INTEGER,
    FOREIGN KEY (szoba_id) REFERENCES szoba(id) ON DELETE SET NULL,
    FOREIGN KEY (felhasznalo_id) REFERENCES felhasznalo(id) ON DELETE CASCADE
);

--
CREATE TABLE szoba_kerdesei (
    id NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    szoba_id NUMBER,
    kerdes_id NUMBER,
    FOREIGN KEY (szoba_id) REFERENCES szoba(id) ON DELETE CASCADE,
    FOREIGN KEY (kerdes_id) REFERENCES kerdes(id) ON DELETE CASCADE
);

-- Példa rekordok felvitele a felhasznalo táblába
INSERT INTO felhasznalo (nev, email, jelszo, admin_e) VALUES ('János', 'janos@gmail.com', 'jelszo123', 0);
INSERT INTO felhasznalo (nev, email, jelszo, admin_e) VALUES ('Alice Johnson', 'alice@example.com', 'pass123', 0);
INSERT INTO felhasznalo (nev, email, jelszo, admin_e) VALUES ('Bob Smith', 'bob@example.com', 'pass456', 0);
INSERT INTO felhasznalo (nev, email, jelszo, admin_e) VALUES ('Charlie Brown', 'charlie@example.com', 'pass789', 0);
INSERT INTO felhasznalo (nev, email, jelszo, admin_e) VALUES ('Diana Miller', 'diana@example.com', 'pass321', 0);
INSERT INTO felhasznalo (nev, email, jelszo, admin_e) VALUES ('Ethan Davis', 'ethan@example.com', 'pass654', 0);
INSERT INTO felhasznalo (nev, email, jelszo, admin_e) VALUES ('Fiona Lee', 'fiona@example.com', 'pass987', 0);
INSERT INTO felhasznalo (nev, email, jelszo, admin_e) VALUES ('George Clark', 'george@example.com', 'pass1234', 0);
INSERT INTO felhasznalo (nev, email, jelszo, admin_e) VALUES ('Hannah Scott', 'hannah@example.com', 'pass5678', 0);
INSERT INTO felhasznalo (nev, email, jelszo, admin_e) VALUES ('Isaac Taylor', 'isaac@example.com', 'pass9012', 0);
INSERT INTO felhasznalo (nev, email, jelszo, admin_e) VALUES ('Julia Wilson', 'julia@example.com', 'pass3456', 0);
INSERT INTO felhasznalo (nev, email, jelszo, admin_e) VALUES ('Kevin White', 'kevin@example.com', 'pass7890', 0);
INSERT INTO felhasznalo (nev, email, jelszo, admin_e) VALUES ('Lily Martinez', 'lily@example.com', 'pass12345', 0);
INSERT INTO felhasznalo (nev, email, jelszo, admin_e) VALUES ('Mike Garcia', 'mike@example.com', 'pass67890', 0);
INSERT INTO felhasznalo (nev, email, jelszo, admin_e) VALUES ('Natalie Anderson', 'natalie@example.com', 'pass23456', 0);
INSERT INTO felhasznalo (nev, email, jelszo, admin_e) VALUES ('Oliver Thomas', 'oliver@example.com', 'pass78901', 0);

-- Témák beszúrása
INSERT INTO tema (nev) VALUES ('Matematika');
INSERT INTO tema (nev) VALUES ('Történelem');
INSERT INTO tema (nev) VALUES ('Földrajz');
INSERT INTO tema (nev) VALUES ('Nyelvtan');
INSERT INTO tema (nev) VALUES ('Kémia');
INSERT INTO tema (nev) VALUES ('Fizika');
INSERT INTO tema (nev) VALUES ('Biológia');
INSERT INTO tema (nev) VALUES ('Informatika');
INSERT INTO tema (nev) VALUES ('Irodalom');
INSERT INTO tema (nev) VALUES ('Művészet');
INSERT INTO tema (nev) VALUES ('Politika');
INSERT INTO tema (nev) VALUES ('Egészségügy');
INSERT INTO tema (nev) VALUES ('Gazdaság');
INSERT INTO tema (nev) VALUES ('Sport');
INSERT INTO tema (nev) VALUES ('Vallás');
INSERT INTO tema (nev) VALUES ('Film és TV');
INSERT INTO tema (nev) VALUES ('Zene');
INSERT INTO tema (nev) VALUES ('Természettudományok');
INSERT INTO tema (nev) VALUES ('Élelmiszerek');
INSERT INTO tema (nev) VALUES ('Utazás és Felfedezés');

-- Kérdések beszúrása
INSERT INTO kerdes (kerdes, tema_id) VALUES ('Mi az algebrai egyenletek megoldásának első lépése?', 1);
INSERT INTO kerdes (kerdes, tema_id) VALUES ('Ki volt az első amerikai elnök?', 2);
INSERT INTO kerdes (kerdes, tema_id) VALUES ('Mi a Föld legnagyobb kontinense?', 3);
INSERT INTO kerdes (kerdes, tema_id) VALUES ('Mi az angol nyelv legfontosabb nyelvtani alapja?', 4);
INSERT INTO kerdes (kerdes, tema_id) VALUES ('Mi a víz képlete?', 5);
INSERT INTO kerdes (kerdes, tema_id) VALUES ('Mi az Einstein által kidolgozott elmélet neve, amely a tömeg és energia kapcsolatát írja le?', 6);
INSERT INTO kerdes (kerdes, tema_id) VALUES ('Melyik állat a legnagyobb a Földön?', 7);
INSERT INTO kerdes (kerdes, tema_id) VALUES ('Mi a legnépszerűbb programozási nyelv a világon?', 8);
INSERT INTO kerdes (kerdes, tema_id) VALUES ('Ki írta a Hamlet című drámát?', 9);
INSERT INTO kerdes (kerdes, tema_id) VALUES ('Kik voltak a Római Birodalom vezetői?', 10);
INSERT INTO kerdes (kerdes, tema_id) VALUES ('Melyik ország a legnagyobb kávétermelő a világon?', 11);
INSERT INTO kerdes (kerdes, tema_id) VALUES ('Mi a GDP?', 12);
INSERT INTO kerdes (kerdes, tema_id) VALUES ('Melyik sportágban nyert aranyérmet Phelps Michael az olimpián?', 13);
INSERT INTO kerdes (kerdes, tema_id) VALUES ('Mi a buddhizmus alapítójának neve?', 14);
INSERT INTO kerdes (kerdes, tema_id) VALUES ('Ki volt a Star Wars filmek rendezője?', 15);
INSERT INTO kerdes (kerdes, tema_id) VALUES ('Ki volt az "Éljen soká az újvilág!" szlogen szerzője?', 16);
INSERT INTO kerdes (kerdes, tema_id) VALUES ('Ki volt a Beatlesek egyik alapító tagja?', 17);
INSERT INTO kerdes (kerdes, tema_id) VALUES ('Mi a legfőbb forrása a földi hőnek?', 18);
INSERT INTO kerdes (kerdes, tema_id) VALUES ('Melyik zöldségfajta a legnépszerűbb a világon?', 19);
INSERT INTO kerdes (kerdes, tema_id) VALUES ('Melyik város a leghíresebb turistalátványosságok egyike a Machu Picchuval?', 20);

-- Szobák beszúrása
INSERT INTO szoba (nev, jelszo, felhasznalo_id) VALUES ('Matematika Szoba', 'math123', 1);
INSERT INTO szoba (nev, jelszo, felhasznalo_id) VALUES ('Történelem Terem', 'hist456', 2);
INSERT INTO szoba (nev, jelszo, felhasznalo_id) VALUES ('Földrajz Kuckó', 'geo789', 3);
INSERT INTO szoba (nev, jelszo, felhasznalo_id) VALUES ('Nyelvtan Stúdió', 'lang321', 4);
INSERT INTO szoba (nev, jelszo, felhasznalo_id) VALUES ('Kémia Klub', 'chem654', 5);
INSERT INTO szoba (nev, jelszo, felhasznalo_id) VALUES ('Fizika Fiók', 'phys987', 6);
INSERT INTO szoba (nev, jelszo, felhasznalo_id) VALUES ('Biológia Bázis', 'bio123', 7);
INSERT INTO szoba (nev, jelszo, felhasznalo_id) VALUES ('Informatika Inc.', 'it456', 8);
INSERT INTO szoba (nev, jelszo, felhasznalo_id) VALUES ('Irodalom Ház', 'lit789', 9);
INSERT INTO szoba (nev, jelszo, felhasznalo_id) VALUES ('Művészet Műhely', 'art321', 10);

-- Eredmények beszúrása
INSERT INTO eredmeny (szoba_id, felhasznalo_id, pontszam) VALUES (1, 1, 85);
INSERT INTO eredmeny (szoba_id, felhasznalo_id, pontszam) VALUES (1, 2, 72);
INSERT INTO eredmeny (szoba_id, felhasznalo_id, pontszam) VALUES (1, 3, 90);
INSERT INTO eredmeny (szoba_id, felhasznalo_id, pontszam) VALUES (2, 4, 65);
INSERT INTO eredmeny (szoba_id, felhasznalo_id, pontszam) VALUES (2, 5, 78);
INSERT INTO eredmeny (szoba_id, felhasznalo_id, pontszam) VALUES (2, 6, 82);
INSERT INTO eredmeny (szoba_id, felhasznalo_id, pontszam) VALUES (3, 7, 91);
INSERT INTO eredmeny (szoba_id, felhasznalo_id, pontszam) VALUES (3, 8, 79);
INSERT INTO eredmeny (szoba_id, felhasznalo_id, pontszam) VALUES (3, 9, 88);
INSERT INTO eredmeny (szoba_id, felhasznalo_id, pontszam) VALUES (4, 10, 70);

-- Szobák kérdéseinek beszúrása
INSERT INTO szoba_kerdesei (szoba_id, kerdes_id) VALUES (1, 1);
INSERT INTO szoba_kerdesei (szoba_id, kerdes_id) VALUES (1, 2);
INSERT INTO szoba_kerdesei (szoba_id, kerdes_id) VALUES (1, 3);
INSERT INTO szoba_kerdesei (szoba_id, kerdes_id) VALUES (2, 4);
INSERT INTO szoba_kerdesei (szoba_id, kerdes_id) VALUES (2, 5);
INSERT INTO szoba_kerdesei (szoba_id, kerdes_id) VALUES (2, 6);
INSERT INTO szoba_kerdesei (szoba_id, kerdes_id) VALUES (3, 7);
INSERT INTO szoba_kerdesei (szoba_id, kerdes_id) VALUES (3, 8);
INSERT INTO szoba_kerdesei (szoba_id, kerdes_id) VALUES (3, 9);
INSERT INTO szoba_kerdesei (szoba_id, kerdes_id) VALUES (4, 10);
INSERT INTO szoba_kerdesei (szoba_id, kerdes_id) VALUES (5, 11);
INSERT INTO szoba_kerdesei (szoba_id, kerdes_id) VALUES (5, 12);
INSERT INTO szoba_kerdesei (szoba_id, kerdes_id) VALUES (5, 13);
INSERT INTO szoba_kerdesei (szoba_id, kerdes_id) VALUES (6, 14);
INSERT INTO szoba_kerdesei (szoba_id, kerdes_id) VALUES (6, 15);
INSERT INTO szoba_kerdesei (szoba_id, kerdes_id) VALUES (6, 16);
INSERT INTO szoba_kerdesei (szoba_id, kerdes_id) VALUES (7, 17);
INSERT INTO szoba_kerdesei (szoba_id, kerdes_id) VALUES (7, 18);
INSERT INTO szoba_kerdesei (szoba_id, kerdes_id) VALUES (7, 19);
INSERT INTO szoba_kerdesei (szoba_id, kerdes_id) VALUES (8, 20);
INSERT INTO szoba_kerdesei (szoba_id, kerdes_id) VALUES (8, 1);
INSERT INTO szoba_kerdesei (szoba_id, kerdes_id) VALUES (8, 2);
INSERT INTO szoba_kerdesei (szoba_id, kerdes_id) VALUES (9, 3);
INSERT INTO szoba_kerdesei (szoba_id, kerdes_id) VALUES (9, 4);
INSERT INTO szoba_kerdesei (szoba_id, kerdes_id) VALUES (9, 5);
INSERT INTO szoba_kerdesei (szoba_id, kerdes_id) VALUES (10, 6);
INSERT INTO szoba_kerdesei (szoba_id, kerdes_id) VALUES (10, 7);
INSERT INTO szoba_kerdesei (szoba_id, kerdes_id) VALUES (10, 8);

-- Válaszok beszúrása
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (1, 'Az egyenlet megfelelő oldalának kifejtése', 0);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (1, 'Az egyenlet rendezése', 1);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (1, 'Az egyenlet átírása diszkrimináns formába', 0);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (1, 'Az egyenlet megoldásának egyszerűsítése', 0);

INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (2, 'George Washington', 1);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (2, 'Thomas Jefferson', 0);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (2, 'John Adams', 0);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (2, 'James Madison', 0);

INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (3, 'Ázsia', 0);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (3, 'Európa', 0);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (3, 'Afrika', 0);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (3, 'Észak-Amerika', 1);

INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (4, 'Igeidők', 0);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (4, 'Témaindítók', 0);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (4, 'Szórend', 0);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (4, 'Az alany-közép tárgyas szórend', 1);

INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (5, 'H2O', 1);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (5, 'CO2', 0);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (5, 'N2', 0);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (5, 'CH4', 0);

INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (6, 'Relativitáselmélet', 1);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (6, 'Kvantummechanika', 0);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (6, 'Elektrodinamika', 0);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (6, 'Gravitációs elmélet', 0);

INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (7, 'Elefánt', 0);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (7, 'Kék bálna', 1);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (7, 'Afrikai oroszlán', 0);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (7, 'Őrszarvas', 0);

INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (8, 'Java', 0);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (8, 'C++', 0);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (8, 'Python', 1);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (8, 'JavaScript', 0);

INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (9, 'William Shakespeare', 1);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (9, 'Jane Austen', 0);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (9, 'Charles Dickens', 0);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (9, 'Emily Brontë', 0);

INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (10, 'Császárkori', 0);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (10, 'Királyi', 0);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (10, 'Konzuli', 1);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (10, 'Senatori', 0);

INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (11, 'Kolumbia', 0);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (11, 'Brazília', 0);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (11, 'Etiópia', 0);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (11, 'Vietnám', 1);

INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (12, 'Gross Domestic Product', 1);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (12, 'Gross Domestic Production', 0);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (12, 'Global Domestic Product', 0);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (12, 'Global Domestic Production', 0);

INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (13, 'Úszás', 1);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (13, 'Tenisz', 0);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (13, 'Golf', 0);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (13, 'Kosárlabda', 0);

INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (14, 'Sziddhártha Gautama', 1);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (14, 'Dalai Láma', 0);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (14, 'Gandhi', 0);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (14, 'Laozi', 0);

INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (15, 'George Lucas', 1);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (15, 'Steven Spielberg', 0);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (15, 'James Cameron', 0);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (15, 'Martin Scorsese', 0);

INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (16, 'Francis Bellamy', 1);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (16, 'Thomas Jefferson', 0);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (16, 'Benjamin Franklin', 0);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (16, 'Alexander Hamilton', 0);

INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (17, 'Paul McCartney', 1);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (17, 'Ringo Starr', 0);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (17, 'John Lennon', 0);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (17, 'George Harrison', 0);

INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (18, 'Nap', 1);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (18, 'Belső felszíni hő', 0);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (18, 'Geotermikus energia', 0);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (18, 'Hőszivattyúk', 0);

INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (19, 'Burgonya', 1);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (19, 'Paradicsom', 0);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (19, 'Sárgarépa', 0);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (19, 'Hagyma', 0);

INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (20, 'Peru', 1);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (20, 'Brazília', 0);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (20, 'Kolumbia', 0);
INSERT INTO valasz (kerdes_id, valasz, helyes_e) VALUES (20, 'Chile', 0);

--  A törléseket az alábbi utasításokkal teszteltük le:
DELETE FROM felhasznalo WHERE id = 1;
DELETE FROM kerdes WHERE id = 1;
DELETE FROM tema WHERE id = 1;

-- a GENERATED ALWAYS kulcsszóval előállított azonosítóoszlop nem módosítható
-- Tesztelt utasítás
-- UPDATE felhasznalo SET id = 15 WHERE id = 2;