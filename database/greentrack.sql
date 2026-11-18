-- GreenTrack - Skema Database
-- Import file ini ke database "greentrack" melalui phpMyAdmin.

CREATE DATABASE IF NOT EXISTS greentrack CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE greentrack;

DROP TABLE IF EXISTS target;
DROP TABLE IF EXISTS pemakaian;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    tarif_listrik DECIMAL(10,2) NOT NULL DEFAULT 1444.70,   -- Rp per kWh
    faktor_emisi DECIMAL(8,4) NOT NULL DEFAULT 0.8700,      -- kg CO2 per kWh
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE pemakaian (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nama_perangkat VARCHAR(100) NOT NULL,
    daya_watt DECIMAL(10,2) NOT NULL,
    durasi_jam DECIMAL(6,2) NOT NULL,         -- jam pemakaian per hari
    jumlah_hari INT NOT NULL DEFAULT 1,
    tanggal DATE NOT NULL,
    kwh DECIMAL(12,4) NOT NULL,
    biaya DECIMAL(14,2) NOT NULL,
    emisi DECIMAL(12,4) NOT NULL,             -- kg CO2
    catatan VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_tanggal (user_id, tanggal)
) ENGINE=InnoDB;

CREATE TABLE target (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    judul VARCHAR(120) NOT NULL,
    periode CHAR(7) NOT NULL,                 -- format YYYY-MM
    batas_kwh DECIMAL(12,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
