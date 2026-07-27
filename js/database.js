/* ===== TechNova Solutions - Support Ticket Database =====
   Full CRUD (Add / Retrieve / Update / Delete) using localStorage.
   Retrieve displays all records in a table.  */

var STORE = "tn_tickets";
var editingId = null;

function getTickets() {
  return JSON.parse(localStorage.getItem(STORE) || "[]");
}
function saveTickets(rows) {
  localStorage.setItem(STORE, JSON.stringify(rows));
}
function nextId(rows) {
  return rows.length ? Math.max.apply(null, rows.map(function (r) { return r.id; })) + 1 : 1;
}

/* ---------- ADD (or UPDATE when editingId is set) ---------- */
function saveTicket(e) {
  e.preventDefault();
  var client = document.getElementById("t-client").value.trim();
  var device = document.getElementById("t-device").value.trim();
  var issue = document.getElementById("t-issue").value.trim();
  var priority = document.getElementById("t-priority").value;
  var status = document.getElementById("t-status").value;
  var rows = getTickets();

  if (editingId === null) {
    // ADD RECORD
    rows.push({
      id: nextId(rows),
      client: client, device: device, issue: issue,
      priority: priority, status: status,
      date: new Date().toLocaleDateString()
    });
    flash("✅ Record added successfully.", "ok");
  } else {
    // UPDATE RECORD
    rows = rows.map(function (r) {
      if (r.id === editingId) {
        return { id: r.id, client: client, device: device, issue: issue,
                 priority: priority, status: status, date: r.date };
      }
      return r;
    });
    flash("✏️ Record #" + editingId + " updated.", "ok");
    editingId = null;
    document.getElementById("form-title").textContent = "Add New Ticket";
    document.getElementById("save-btn").textContent = "Add Record";
    document.getElementById("cancel-btn").style.display = "none";
  }
  saveTickets(rows);
  document.getElementById("ticket-form").reset();
  renderTable();
  return false;
}

/* ---------- RETRIEVE (render all records in a table) ---------- */
function renderTable() {
  var rows = getTickets();
  var tbody = document.getElementById("ticket-body");
  var empty = document.getElementById("empty-msg");
  tbody.innerHTML = "";
  if (rows.length === 0) {
    empty.style.display = "block";
    return;
  }
  empty.style.display = "none";
  rows.forEach(function (r) {
    var tr = document.createElement("tr");
    tr.innerHTML =
      "<td>" + r.id + "</td>" +
      "<td>" + esc(r.client) + "</td>" +
      "<td>" + esc(r.device) + "</td>" +
      "<td>" + esc(r.issue) + "</td>" +
      "<td>" + esc(r.priority) + "</td>" +
      "<td>" + esc(r.status) + "</td>" +
      "<td>" + esc(r.date) + "</td>" +
      '<td class="actions-cell">' +
        '<button class="btn alt" onclick="editTicket(' + r.id + ')">Update</button>' +
        '<button class="btn danger" onclick="deleteTicket(' + r.id + ')">Delete</button>' +
      "</td>";
    tbody.appendChild(tr);
  });
}

/* ---------- Load a record into the form for UPDATE ---------- */
function editTicket(id) {
  var r = getTickets().find(function (x) { return x.id === id; });
  if (!r) return;
  editingId = id;
  document.getElementById("t-client").value = r.client;
  document.getElementById("t-device").value = r.device;
  document.getElementById("t-issue").value = r.issue;
  document.getElementById("t-priority").value = r.priority;
  document.getElementById("t-status").value = r.status;
  document.getElementById("form-title").textContent = "Update Ticket #" + id;
  document.getElementById("save-btn").textContent = "Save Changes";
  document.getElementById("cancel-btn").style.display = "inline-block";
  window.scrollTo({ top: 0, behavior: "smooth" });
}

function cancelEdit() {
  editingId = null;
  document.getElementById("ticket-form").reset();
  document.getElementById("form-title").textContent = "Add New Ticket";
  document.getElementById("save-btn").textContent = "Add Record";
  document.getElementById("cancel-btn").style.display = "none";
}

/* ---------- DELETE ---------- */
function deleteTicket(id) {
  if (!confirm("Delete record #" + id + "? This cannot be undone.")) return;
  var rows = getTickets().filter(function (r) { return r.id !== id; });
  saveTickets(rows);
  flash("🗑️ Record #" + id + " deleted.", "ok");
  renderTable();
}

/* ---------- helpers ---------- */
function flash(msg, type) {
  var box = document.getElementById("db-msg");
  box.className = "alert " + type;
  box.textContent = msg;
  box.style.display = "block";
  setTimeout(function () { box.style.display = "none"; }, 2500);
}
function esc(s) {
  return String(s).replace(/[&<>"]/g, function (c) {
    return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;" }[c];
  });
}

/* ---------- Seed a couple of demo rows on first run ---------- */
function seedIfEmpty() {
  if (getTickets().length === 0) {
    saveTickets([
      { id: 1, client: "Ama Owusu", device: "Dell Laptop", issue: "Wi-Fi not connecting",
        priority: "High", status: "Open", date: new Date().toLocaleDateString() },
      { id: 2, client: "Kofi Adjei", device: "HP Printer", issue: "Paper jam error",
        priority: "Medium", status: "In Progress", date: new Date().toLocaleDateString() }
    ]);
  }
}
