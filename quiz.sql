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
    admin_e INTEGER,
    utolso_aktivitas_datum TIMESTAMP
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
    felhasznalo_id NUMBER,
    globalis_kerdes NUMBER(1) DEFAULT 0,
    FOREIGN KEY (tema_id) REFERENCES tema(id) ON DELETE SET NULL,
    FOREIGN KEY (felhasznalo_id) REFERENCES felhasznalo(id) ON DELETE SET NULL
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
    utolso_aktivitas_datum TIMESTAMP, 
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

CREATE OR REPLACE TRIGGER update_last_activity_trigger
BEFORE INSERT ON eredmeny
FOR EACH ROW
BEGIN
    UPDATE felhasznalo
    SET utolso_aktivitas_datum = CURRENT_TIMESTAMP
    WHERE id = :new.felhasznalo_id;
END;
/

CREATE OR REPLACE TRIGGER update_room_last_activity_trigger
AFTER INSERT OR UPDATE OR DELETE ON szoba_kerdesei
FOR EACH ROW
DECLARE
    v_room_id szoba.id%TYPE;
BEGIN
    -- Szoba azonosítójának lekérdezése az érintett sor alapján
    IF INSERTING THEN
        v_room_id := :new.szoba_id;
    ELSIF UPDATING THEN
        v_room_id := :new.szoba_id;
    ELSIF DELETING THEN
        v_room_id := :old.szoba_id;
    END IF;

    -- Szoba utolsó aktivitás dátumának frissítése
    UPDATE szoba
    SET utolso_aktivitas_datum = CURRENT_TIMESTAMP
    WHERE id = v_room_id;
END;
/

CREATE OR REPLACE PROCEDURE get_average_score(user_id IN NUMBER,avg_score OUT NUMBER)
IS
BEGIN
    SELECT AVG(pontszam)
    INTO avg_score
    FROM eredmeny
    WHERE felhasznalo_id = user_id;
END;
/


CREATE OR REPLACE PROCEDURE get_all_room_results (
    result OUT SYS_REFCURSOR
)
IS
BEGIN
    OPEN result FOR
        SELECT e.szoba_id, e.felhasznalo_id, e.pontszam
        FROM eredmeny e;
END;
/



-- Példa rekordok felvitele a felhasznalo táblába
INSERT INTO felhasznalo (nev, email, jelszo, admin_e) VALUES ('János', 'janos@gmail.com', '$2y$10$V.q7ctyCwR8/vC2OoK0SJ.xnfEJsiKEwCd87X.B0QDqYvhA9sp1py', 1);
INSERT INTO felhasznalo (nev, email, jelszo, admin_e) VALUES ('Alice Johnson', 'alice@example.com', '$2y$10$V.q7ctyCwR8/vC2OoK0SJ.xnfEJsiKEwCd87X.B0QDqYvhA9sp1py', 1);
INSERT INTO felhasznalo (nev, email, jelszo, admin_e) VALUES ('Bob Smith', 'bob@example.com', '$2y$10$V.q7ctyCwR8/vC2OoK0SJ.xnfEJsiKEwCd87X.B0QDqYvhA9sp1py', 0);
INSERT INTO felhasznalo (nev, email, jelszo, admin_e) VALUES ('Charlie Brown', 'charlie@example.com', '$2y$10$V.q7ctyCwR8/vC2OoK0SJ.xnfEJsiKEwCd87X.B0QDqYvhA9sp1py', 0);
INSERT INTO felhasznalo (nev, email, jelszo, admin_e) VALUES ('Diana Miller', 'diana@example.com', '$2y$10$V.q7ctyCwR8/vC2OoK0SJ.xnfEJsiKEwCd87X.B0QDqYvhA9sp1py', 0);
INSERT INTO felhasznalo (nev, email, jelszo, admin_e) VALUES ('Ethan Davis', 'ethan@example.com', '$2y$10$V.q7ctyCwR8/vC2OoK0SJ.xnfEJsiKEwCd87X.B0QDqYvhA9sp1py', 0);
INSERT INTO felhasznalo (nev, email, jelszo, admin_e) VALUES ('Fiona Lee', 'fiona@example.com', '$2y$10$V.q7ctyCwR8/vC2OoK0SJ.xnfEJsiKEwCd87X.B0QDqYvhA9sp1py', 0);
INSERT INTO felhasznalo (nev, email, jelszo, admin_e) VALUES ('George Clark', 'george@example.com', '$2y$10$V.q7ctyCwR8/vC2OoK0SJ.xnfEJsiKEwCd87X.B0QDqYvhA9sp1py', 0);
INSERT INTO felhasznalo (nev, email, jelszo, admin_e) VALUES ('Hannah Scott', 'hannah@example.com', '$2y$10$V.q7ctyCwR8/vC2OoK0SJ.xnfEJsiKEwCd87X.B0QDqYvhA9sp1py', 0);
INSERT INTO felhasznalo (nev, email, jelszo, admin_e) VALUES ('Isaac Taylor', 'isaac@example.com', '$2y$10$V.q7ctyCwR8/vC2OoK0SJ.xnfEJsiKEwCd87X.B0QDqYvhA9sp1py', 0);
INSERT INTO felhasznalo (nev, email, jelszo, admin_e) VALUES ('Julia Wilson', 'julia@example.com', '$2y$10$V.q7ctyCwR8/vC2OoK0SJ.xnfEJsiKEwCd87X.B0QDqYvhA9sp1py', 0);
INSERT INTO felhasznalo (nev, email, jelszo, admin_e) VALUES ('Kevin White', 'kevin@example.com', '$2y$10$V.q7ctyCwR8/vC2OoK0SJ.xnfEJsiKEwCd87X.B0QDqYvhA9sp1py', 0);
INSERT INTO felhasznalo (nev, email, jelszo, admin_e) VALUES ('Lily Martinez', 'lily@example.com', '$2y$10$V.q7ctyCwR8/vC2OoK0SJ.xnfEJsiKEwCd87X.B0QDqYvhA9sp1py', 0);
INSERT INTO felhasznalo (nev, email, jelszo, admin_e) VALUES ('Mike Garcia', 'mike@example.com', '$2y$10$V.q7ctyCwR8/vC2OoK0SJ.xnfEJsiKEwCd87X.B0QDqYvhA9sp1py', 0);
INSERT INTO felhasznalo (nev, email, jelszo, admin_e) VALUES ('Natalie Anderson', 'natalie@example.com', '$2y$10$V.q7ctyCwR8/vC2OoK0SJ.xnfEJsiKEwCd87X.B0QDqYvhA9sp1py', 0);
INSERT INTO felhasznalo (nev, email, jelszo, admin_e) VALUES ('Oliver Thomas', 'oliver@example.com', '$2y$10$V.q7ctyCwR8/vC2OoK0SJ.xnfEJsiKEwCd87X.B0QDqYvhA9sp1py', 0);

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
INSERT INTO kerdes (kerdes, tema_id, felhasznalo_id,globalis_kerdes) VALUES ('Mi az algebrai egyenletek megoldásának első lépése?', 1,1,1);
INSERT INTO kerdes (kerdes, tema_id, felhasznalo_id,globalis_kerdes) VALUES ('Ki volt az első amerikai elnök?', 2,1,1);
INSERT INTO kerdes (kerdes, tema_id, felhasznalo_id,globalis_kerdes) VALUES ('Mi a Föld legnagyobb kontinense?', 3,1,1);
INSERT INTO kerdes (kerdes, tema_id, felhasznalo_id,globalis_kerdes) VALUES ('Mi az angol nyelv legfontosabb nyelvtani alapja?', 4,1,1);
INSERT INTO kerdes (kerdes, tema_id, felhasznalo_id,globalis_kerdes) VALUES ('Mi a víz képlete?', 5,1,1);
INSERT INTO kerdes (kerdes, tema_id, felhasznalo_id,globalis_kerdes) VALUES ('Mi az Einstein által kidolgozott elmélet neve, amely a tömeg és energia kapcsolatát írja le?', 6,1,1);
INSERT INTO kerdes (kerdes, tema_id, felhasznalo_id,globalis_kerdes) VALUES ('Melyik állat a legnagyobb a Földön?', 7,1,1);
INSERT INTO kerdes (kerdes, tema_id, felhasznalo_id,globalis_kerdes) VALUES ('Mi a legnépszerűbb programozási nyelv a világon?', 8,1,1);
INSERT INTO kerdes (kerdes, tema_id, felhasznalo_id,globalis_kerdes) VALUES ('Ki írta a Hamlet című drámát?', 9,1,1);
INSERT INTO kerdes (kerdes, tema_id, felhasznalo_id,globalis_kerdes) VALUES ('Kik voltak a Római Birodalom vezetői?', 10,1,1);
INSERT INTO kerdes (kerdes, tema_id, felhasznalo_id,globalis_kerdes) VALUES ('Melyik ország a legnagyobb kávétermelő a világon?', 11,1,1);
INSERT INTO kerdes (kerdes, tema_id, felhasznalo_id,globalis_kerdes) VALUES ('Mi a GDP?', 12,1,1);
INSERT INTO kerdes (kerdes, tema_id, felhasznalo_id,globalis_kerdes) VALUES ('Melyik sportágban nyert aranyérmet Phelps Michael az olimpián?', 13,1,1);
INSERT INTO kerdes (kerdes, tema_id, felhasznalo_id,globalis_kerdes) VALUES ('Mi a buddhizmus alapítójának neve?', 14,1,1);
INSERT INTO kerdes (kerdes, tema_id, felhasznalo_id,globalis_kerdes) VALUES ('Ki volt a Star Wars filmek rendezője?', 15,1,1);
INSERT INTO kerdes (kerdes, tema_id, felhasznalo_id,globalis_kerdes) VALUES ('Ki volt az "Éljen soká az újvilág!" szlogen szerzője?', 16,1,1);
INSERT INTO kerdes (kerdes, tema_id, felhasznalo_id,globalis_kerdes) VALUES ('Ki volt a Beatlesek egyik alapító tagja?', 17,1,1);
INSERT INTO kerdes (kerdes, tema_id, felhasznalo_id,globalis_kerdes) VALUES ('Mi a legfőbb forrása a földi hőnek?', 18,1,1);
INSERT INTO kerdes (kerdes, tema_id, felhasznalo_id,globalis_kerdes) VALUES ('Melyik zöldségfajta a legnépszerűbb a világon?', 19,1,1);
INSERT INTO kerdes (kerdes, tema_id, felhasznalo_id,globalis_kerdes) VALUES ('Melyik város a leghíresebb turistalátványosságok egyike a Machu Picchuval?', 20,1,1);

-- Szobák beszúrása
INSERT INTO szoba (nev, jelszo, felhasznalo_id) VALUES ('Matematika Szoba', '$2y$10$V.q7ctyCwR8/vC2OoK0SJ.xnfEJsiKEwCd87X.B0QDqYvhA9sp1py', 1);
INSERT INTO szoba (nev, jelszo, felhasznalo_id) VALUES ('Történelem Terem', '$2y$10$V.q7ctyCwR8/vC2OoK0SJ.xnfEJsiKEwCd87X.B0QDqYvhA9sp1py', 2);
INSERT INTO szoba (nev, jelszo, felhasznalo_id) VALUES ('Földrajz Kuckó', '$2y$10$V.q7ctyCwR8/vC2OoK0SJ.xnfEJsiKEwCd87X.B0QDqYvhA9sp1py', 3);
INSERT INTO szoba (nev, jelszo, felhasznalo_id) VALUES ('Nyelvtan Stúdió', '$2y$10$V.q7ctyCwR8/vC2OoK0SJ.xnfEJsiKEwCd87X.B0QDqYvhA9sp1py', 4);
INSERT INTO szoba (nev, jelszo, felhasznalo_id) VALUES ('Kémia Klub', '$2y$10$V.q7ctyCwR8/vC2OoK0SJ.xnfEJsiKEwCd87X.B0QDqYvhA9sp1py', 5);
INSERT INTO szoba (nev, jelszo, felhasznalo_id) VALUES ('Fizika Fiók', '$2y$10$V.q7ctyCwR8/vC2OoK0SJ.xnfEJsiKEwCd87X.B0QDqYvhA9sp1py', 6);
INSERT INTO szoba (nev, jelszo, felhasznalo_id) VALUES ('Biológia Bázis', '$2y$10$V.q7ctyCwR8/vC2OoK0SJ.xnfEJsiKEwCd87X.B0QDqYvhA9sp1py', 7);
INSERT INTO szoba (nev, jelszo, felhasznalo_id) VALUES ('Informatika Inc.', '$2y$10$V.q7ctyCwR8/vC2OoK0SJ.xnfEJsiKEwCd87X.B0QDqYvhA9sp1py', 8);
INSERT INTO szoba (nev, jelszo, felhasznalo_id) VALUES ('Irodalom Ház', '$2y$10$V.q7ctyCwR8/vC2OoK0SJ.xnfEJsiKEwCd87X.B0QDqYvhA9sp1py', 9);
INSERT INTO szoba (nev, jelszo, felhasznalo_id) VALUES ('Művészet Műhely', '$2y$10$V.q7ctyCwR8/vC2OoK0SJ.xnfEJsiKEwCd87X.B0QDqYvhA9sp1py', 10);

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

