-- ============================================================
-- TechNova Solutions - Database (InfinityFree / shared hosting)
--
-- Use this file (NOT database.sql) when importing on InfinityFree.
-- It does NOT create or select a database, because on InfinityFree
-- you create the database yourself in the control panel, then import
-- these tables INTO it.
--
-- HOW TO IMPORT:
--   1. Control Panel -> MySQL Databases -> create a database
--      (its name looks like  if0_42523368_technova).
--   2. Control Panel -> phpMyAdmin -> open that database.
--   3. "Import" tab -> choose this file -> "Go".
-- ============================================================

-- ---------- Users (Login & Registration) ----------
CREATE TABLE IF NOT EXISTS users (
  id       INT AUTO_INCREMENT PRIMARY KEY,
  name     VARCHAR(100)  NOT NULL,
  email    VARCHAR(150)  NOT NULL UNIQUE,
  password VARCHAR(255)  NOT NULL,
  role     ENUM('user','admin')    NOT NULL DEFAULT 'user',
  status   ENUM('pending','active') NOT NULL DEFAULT 'pending',
  created  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Default administrator — Login: admin@technova.com  Password: admin123
INSERT INTO users (name, email, password, role, status) VALUES
  ('Administrator', 'admin@technova.com',
   '$2y$10$hTJ0P2n2P2iW5xKJZY2P7ul4rWDhwylWccxgTBQO1U4SoKZxWA1ua', 'admin', 'active');

-- ---------- Support Tickets ----------
CREATE TABLE IF NOT EXISTS tickets (
  id       INT AUTO_INCREMENT PRIMARY KEY,
  user_id  INT NULL,
  client   VARCHAR(120) NOT NULL,
  device   VARCHAR(120) NOT NULL,
  issue    VARCHAR(255) NOT NULL,
  priority ENUM('Low','Medium','High') NOT NULL DEFAULT 'Medium',
  status   ENUM('Open','In Progress','Resolved') NOT NULL DEFAULT 'Open',
  created  DATE NOT NULL
);

-- ---------- Ticket comments ----------
CREATE TABLE IF NOT EXISTS ticket_comments (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  ticket_id   INT NOT NULL,
  author_id   INT NULL,
  author_name VARCHAR(100) NOT NULL,
  comment     TEXT NOT NULL,
  created     TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ---------- Demo tickets ----------
INSERT INTO tickets (client, device, issue, priority, status, created) VALUES
  ('Ama Owusu', 'Dell Laptop', 'Wi-Fi not connecting', 'High',   'Open',        CURDATE()),
  ('Kofi Adjei','HP Printer',  'Paper jam error',      'Medium', 'In Progress', CURDATE());
