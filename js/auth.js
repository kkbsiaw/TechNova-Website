/* ===== TechNova Solutions - Authentication =====
   Simulated Login & Registration using browser localStorage.
   (For a coursework demo — not a real security system.)  */

function getUsers() {
  return JSON.parse(localStorage.getItem("tn_users") || "[]");
}
function saveUsers(users) {
  localStorage.setItem("tn_users", JSON.stringify(users));
}

/* ---------- Registration ---------- */
function registerUser(e) {
  e.preventDefault();
  var name = document.getElementById("reg-name").value.trim();
  var email = document.getElementById("reg-email").value.trim().toLowerCase();
  var pass = document.getElementById("reg-pass").value;
  var pass2 = document.getElementById("reg-pass2").value;
  var box = document.getElementById("reg-msg");

  if (pass !== pass2) {
    box.className = "alert err";
    box.textContent = "Passwords do not match.";
    return false;
  }
  var users = getUsers();
  if (users.some(function (u) { return u.email === email; })) {
    box.className = "alert err";
    box.textContent = "An account with this email already exists.";
    return false;
  }
  users.push({ name: name, email: email, pass: pass });
  saveUsers(users);
  box.className = "alert ok";
  box.textContent = "✅ Account created! Redirecting to login...";
  setTimeout(function () { window.location.href = "login.html"; }, 1200);
  return false;
}

/* ---------- Login ---------- */
function loginUser(e) {
  e.preventDefault();
  var email = document.getElementById("log-email").value.trim().toLowerCase();
  var pass = document.getElementById("log-pass").value;
  var box = document.getElementById("log-msg");

  var users = getUsers();
  var user = users.find(function (u) { return u.email === email && u.pass === pass; });
  if (user) {
    sessionStorage.setItem("tn_current", JSON.stringify({ name: user.name, email: user.email }));
    box.className = "alert ok";
    box.textContent = "✅ Login successful! Opening dashboard...";
    setTimeout(function () { window.location.href = "dashboard.html"; }, 900);
  } else {
    box.className = "alert err";
    box.textContent = "Invalid email or password.";
  }
  return false;
}

/* ---------- Guard: protect the dashboard ---------- */
function requireLogin() {
  var cur = sessionStorage.getItem("tn_current");
  if (!cur) {
    alert("Please log in to access the support portal.");
    window.location.href = "login.html";
    return null;
  }
  return JSON.parse(cur);
}

function logout() {
  sessionStorage.removeItem("tn_current");
  window.location.href = "login.html";
}
