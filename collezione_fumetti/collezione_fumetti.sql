-- Creazione del database
CREATE DATABASE IF NOT EXISTS collezione;
USE collezione;

DROP TABLE IF EXISTS collezione;
DROP TABLE IF EXISTS fumetti;
DROP TABLE IF EXISTS utenti;
DROP TABLE IF EXISTS commenti;

CREATE TABLE utenti (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE NOT NULL,
  email VARCHAR(100) NOT NULL,
  password VARCHAR(255) NOT NULL
);

CREATE TABLE fumetti (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titolo VARCHAR(100) NOT NULL,
  autore VARCHAR(100) NOT NULL,
  anno INT NOT NULL,
  genere VARCHAR(50) NOT NULL,
  descrizione TEXT NOT NULL,
  numero_volumi INT NOT NULL,
  rating DECIMAL(3,1) DEFAULT 4.0
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

CREATE TABLE commenti (
  id INT AUTO_INCREMENT PRIMARY KEY,
  utente_id INT NOT NULL,
  fumetto_id INT NOT NULL,
  testo TEXT NOT NULL,
  data_inserimento DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (utente_id) REFERENCES utenti(id),
  FOREIGN KEY (fumetto_id) REFERENCES fumetti(id)
);

INSERT INTO fumetti (titolo, autore, anno, genere, descrizione, numero_volumi, rating) VALUES
('Gnomi Ribelli', 'Marco Elfi', 2021, 'Fantasy', 'La rivolta dei gnomi nel Bosco Antico', 12, 4.8),
('Folletti Urbani', 'Laura Magia', 2020, 'Urban Fantasy', 'Folletti metropolitani tra misteri e graffiti', 9, 4.6),
('Cronache del Bosco', 'L. Verde', 2019, 'Avventura', 'Viaggi e sfide in una foresta viva', 7, 4.7),
('Il Piccolo Goblin', 'F. Notte', 2022, 'Infanzia', 'Un goblin buono in un mondo di umani', 10, 4.9),
('La Congrega dei Funghi', 'G. Spora', 2023, 'Horror', 'Fungo stregoni al centro della terra', 5, 4.5),
('Le Cronache degli Gnomi', 'Elena Silverwing', 2023, 'Fantasy', 'Un''epica avventura nel regno sotterraneo degli gnomi', 12, 4.8),
('Il Custode del Bosco', 'Marco Treespirit', 2022, 'Fantasy', 'Un giovane gnomo scopre di essere l''ultimo custode', 8, 4.7),
('Regno di Cristallo', 'Sofia Crystal', 2024, 'Fantasy', 'Storie intrecciate nel misterioso regno di cristallo', 6, 4.9),
('Magia Sotterranea', 'Giovanni Underground', 2023, 'Fantasy', 'Misteri nelle profondità della terra', 10, 4.6),
('Folletti Detective', 'Sam Funnyclock', 2023, 'Comic Fantasy', 'Indagini comiche nel mondo magico', 10, 4.6);
