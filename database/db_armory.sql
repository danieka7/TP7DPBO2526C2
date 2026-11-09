-- =====================================================
-- database/db_armory.sql
-- Skema bertema perdagangan senjata. Data contoh fiktif dan administratif.
-- =====================================================
CREATE DATABASE db_armory;
USE db_armory;

-- weapons: inventaris senjata
CREATE TABLE weapons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    manufacturer VARCHAR(100) NOT NULL,
    model_code VARCHAR(13) UNIQUE,
    stock INT NOT NULL DEFAULT 0
);

-- buyers: pembeli/mitra terdaftar
CREATE TABLE buyers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    phone VARCHAR(15)
);

-- transactions: transaksi keluar-masuk (relasi ke weapons dan buyers lewat foreign key)
CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    weapon_id INT NOT NULL,
    buyer_id INT NOT NULL,
    dispatch_date DATE NOT NULL,
    return_date DATE,
    FOREIGN KEY (weapon_id) REFERENCES weapons(id),
    FOREIGN KEY (buyer_id) REFERENCES buyers(id)
);

INSERT INTO weapons (name, manufacturer, model_code, stock) VALUES
('Viper MK-II', 'Ironclad Works', 'WPN01', 5),
('Falcon S9', 'Northgate Arms', 'WPN0000000002', 3),
('Bulwark R4', 'Ironclad Works', 'WPN0000000003', 4),
('Sentinel X1', 'Redfield Armory', 'WPN0000000004', 2);

INSERT INTO buyers (name, email, phone) VALUES
('Garnisun Alpha', 'alpha@garnisun.test', '08123456789'),
('Divisi Bravo', 'bravo@divisi.test', '08234567890');
