-- ============================================================
-- TechNova Solutions - Database Schema
-- Import this file into phpMyAdmin (XAMPP) to create the
-- database and its tables.
--
-- HOW TO IMPORT:
--   1. Start XAMPP -> start Apache + MySQL.
--   2. Open http://localhost/phpmyadmin
--   3. Click the "Import" tab -> choose this file -> "Go".
--      (This automatically creates the database and tables.)
-- ============================================================

CREATE DATABASE IF NOT EXISTS technova_db
  CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE technova_db;

-- ---------- Users (Login & Registration) ----------
CREATE TABLE IF NOT EXISTS users (
  id       INT AUTO_INCREMENT PRIMARY KEY,
  name     VARCHAR(100)  NOT NULL,
  email    VARCHAR(150)  NOT NULL UNIQUE,
  password VARCHAR(255)  NOT NULL,          -- stored hashed
  created  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ---------- Support Tickets (CRUD records) ----------
CREATE TABLE IF NOT EXISTS tickets (
  id       INT AUTO_INCREMENT PRIMARY KEY,
  client   VARCHAR(120) NOT NULL,
  device   VARCHAR(120) NOT NULL,
  issue    VARCHAR(255) NOT NULL,
  priority ENUM('Low','Medium','High') NOT NULL DEFAULT 'Medium',
  status   ENUM('Open','In Progress','Resolved') NOT NULL DEFAULT 'Open',
  created  DATE NOT NULL
);

-- ---------- A couple of demo tickets to start with ----------
INSERT INTO tickets (client, device, issue, priority, status, created) VALUES
  ('Ama Owusu', 'Dell Laptop', 'Wi-Fi not connecting', 'High',   'Open',        CURDATE()),
  ('Kofi Adjei','HP Printer',  'Paper jam error',      'Medium', 'In Progress', CURDATE());
