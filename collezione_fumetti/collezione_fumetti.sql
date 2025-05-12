
CREATE DATABASE IF NOT EXISTS collezione;
USE collezione;

DROP TABLE IF EXISTS collezione;
DROP TABLE IF EXISTS fumetti;
DROP TABLE IF EXISTS utenti;

CREATE TABLE utenti (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE NOT NULL,
  email VARCHAR(100) NOT NULL,
  password VARCHAR(255) NOT NULL
);

CREATE TABLE fumetti (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titolo VARCHAR(100),
  autore VARCHAR(100),
  anno INT,
  genere VARCHAR(50),
  descrizione TEXT,
  numero_volumi INT
);

CREATE TABLE collezione (
  id INT AUTO_INCREMENT PRIMARY KEY,
  utente_id INT,
  fumetto_id INT,
  quantita INT,
  quantita_letti INT,
  quantita_nonletti INT,
  FOREIGN KEY (utente_id) REFERENCES utenti(id),
  FOREIGN KEY (fumetto_id) REFERENCES fumetti(id)
);

INSERT INTO fumetti (titolo, autore, anno, genere, descrizione, numero_volumi) VALUES
('Gnomi Ribelli', 'Marco Elfi', 2021, 'Fantasy', 'La rivolta dei gnomi nel Bosco Antico', 12),
('Folletti Urbani', 'Laura Magia', 2020, 'Urban Fantasy', 'Folletti metropolitani tra misteri e graffiti', 9),
('Cronache del Bosco', 'L. Verde', 2019, 'Avventura', 'Viaggi e sfide in una foresta viva', 7),
('Il Piccolo Goblin', 'F. Notte', 2022, 'Infanzia', 'Un goblin buono in un mondo di umani', 10),
('La Congrega dei Funghi', 'G. Spora', 2023, 'Horror', 'Fungo stregoni al centro della terra', 5);
