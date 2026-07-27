TechNova Solutions — Group Information System Website
=====================================================
A group-of-13 coursework project. Pure HTML, CSS and JavaScript —
no server or installation needed. Just open index.html in a browser.

HOW TO RUN
----------
Double-click "index.html" to open the site in your web browser.
(Chrome, Edge or Firefox recommended.)

HOW THE ASSIGNMENT REQUIREMENTS ARE MET
---------------------------------------
1. Home Page (index.html) with 13 group members' profiles
   (Picture, Full Name and ID) + navigation menu.
2. Information about the company .......... about.html
3. Contact Page .......................... contact.html
4. Three JavaScript pop-ups .............. index.html
   - Pop-up 1: automatic welcome alert on load + "Alert" button
   - Pop-up 2: custom modal dialog
   - Pop-up 3: confirm() subscribe dialog
5. Scrolling text ........................ ticker on index.html (CSS/JS)
6. Image swap (5 images) ................. slideshow on index.html
   (auto-rotates every 3s; click dots to swap manually)
7. Login & Registration .................. login.html / register.html
8. Database with CRUD .................... dashboard.html
   - Add Record, Retrieve (shows records in a table),
     Update Record, Delete Record.

NOTES
-----
* Login/Registration and the ticket database use the browser's
  localStorage to simulate a real database, so everything works
  offline with no backend.
* To try the portal: Register an account -> Login -> you land on the
  dashboard where you can Add / Retrieve / Update / Delete tickets.
* Profile pictures are placeholder avatars (member1.svg ... member13.svg)
  in the /images folder — replace them with real photos of the same
  names when ready.

FOLDER STRUCTURE
----------------
index.html, about.html, contact.html, login.html, register.html, dashboard.html
css/style.css
js/script.js   (pop-ups, scrolling text, slideshow)
js/auth.js     (login & registration)
js/database.js (CRUD ticket database)
images/        (logo, 13 member avatars, 5 slides)
