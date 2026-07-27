TechNova Solutions — Group Information System Website
=====================================================
A group-of-7 coursework project.

Front-end : HTML, CSS, JavaScript
Back-end  : PHP + MySQL (real database, run through XAMPP)

The Home / About / Contact pages are plain HTML.
Login, Registration and the Support-Ticket database use PHP + MySQL,
so the data is stored in a REAL database on disk that the whole group
can share on the same server.

=====================================================
HOW TO RUN (XAMPP)
=====================================================
1. Install XAMPP (https://www.apachefriends.org) and open the
   XAMPP Control Panel.
2. Click "Start" for BOTH  Apache  and  MySQL.
3. Copy this whole "TechNova-Website" folder into the XAMPP
   htdocs folder:
      Windows : C:\xampp\htdocs\TechNova-Website
      macOS   : /Applications/XAMPP/htdocs/TechNova-Website
4. Create the database:
      - Open  http://localhost/phpmyadmin
      - Click the "Import" tab
      - Choose the file  database.sql  (in this folder)
      - Click "Go".  This creates the "technova_db" database,
        the "users" and "tickets" tables, and 2 demo tickets.
5. Open the website in your browser:
      http://localhost/TechNova-Website/index.html
6. Try it:  Register  ->  Login  ->  Dashboard, then
   Add / Retrieve / Update / Delete support tickets.

=====================================================
HOW THE ASSIGNMENT REQUIREMENTS ARE MET
=====================================================
1. Home page (index.html) with 7 member profiles
   (Picture, Full Name, ID) + navigation menu.
2. Company info page ..................... about.html
3. Contact page .......................... contact.html
4. Three JavaScript pop-ups .............. index.html
   (auto welcome alert, custom modal, confirm dialog)
5. Scrolling text ........................ ticker on index.html
6. Image swap (5 images) ................. slideshow on index.html
7. Login & Registration .................. login.php / register.php
   (passwords stored HASHED in the users table)
8. Database with CRUD .................... dashboard.php
   - Add Record, Retrieve (shows records in a table),
     Update Record, Delete Record — all against MySQL.

Bonus: responsive HAMBURGER menu on mobile screens.

=====================================================
FILES
=====================================================
index.html, about.html, contact.html ..... static pages (HTML/CSS/JS)
login.php, register.php, logout.php ....... authentication (PHP + MySQL)
dashboard.php ............................. CRUD ticket database (PHP + MySQL)
db.php .................................... database connection settings
database.sql ............................. database + tables to import
css/style.css ............................. all styling
js/script.js .............................. pop-ups, scrolling text, slideshow
images/ ................................... logo, member photos, slides

=====================================================
DATABASE SETTINGS (db.php)
=====================================================
host = localhost   user = root   password = ""   database = technova_db
These are the default XAMPP MySQL settings. If your MySQL uses a
different username/password, edit db.php to match.

=====================================================
NOTE ABOUT GITHUB PAGES
=====================================================
GitHub Pages can only serve static files — it CANNOT run PHP.
So the login/register/dashboard work on XAMPP (or any PHP web host),
but NOT on the github.io link. The Home/About/Contact pages still
work there. For the live database demo, run it through XAMPP.
