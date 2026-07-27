/* ===== TechNova Solutions - Shared UI Scripts =====
   Handles: 3 pop-ups, scrolling text, image slideshow/swap  */

/* ---------- POP-UP #1: Welcome alert on page load ---------- */
window.addEventListener("load", function () {
  // Only greet once per browser session
  if (!sessionStorage.getItem("greeted")) {
    alert("👋 Welcome to TechNova Solutions!\nExplore our team, services and support portal.");
    sessionStorage.setItem("greeted", "1");
  }
});

/* ---------- POP-UP #2: Custom modal dialog ---------- */
function openModal(id) {
  var el = document.getElementById(id);
  if (el) el.classList.add("open");
}
function closeModal(id) {
  var el = document.getElementById(id);
  if (el) el.classList.remove("open");
}

/* ---------- POP-UP #3: Confirm dialog ---------- */
function confirmPopup() {
  var ok = confirm("Would you like to subscribe to our IT newsletter?");
  if (ok) {
    alert("✅ Thank you! You are now subscribed.");
  } else {
    alert("No problem — maybe next time!");
  }
}

/* ---------- Image slideshow / swap (max 5 images) ---------- */
var slideIndex = 0;
var slideTimer = null;

function showSlide(n) {
  var slides = document.querySelectorAll(".slideshow img");
  var dots = document.querySelectorAll(".dot");
  if (slides.length === 0) return;
  if (n >= slides.length) slideIndex = 0;
  if (n < 0) slideIndex = slides.length - 1;
  slides.forEach(function (s) { s.classList.remove("show"); });
  dots.forEach(function (d) { d.classList.remove("active"); });
  slides[slideIndex].classList.add("show");
  if (dots[slideIndex]) dots[slideIndex].classList.add("active");
}

function currentSlide(n) {
  slideIndex = n;
  showSlide(slideIndex);
  restartAuto();
}

function nextSlide() { slideIndex++; showSlide(slideIndex); }

function restartAuto() {
  if (slideTimer) clearInterval(slideTimer);
  slideTimer = setInterval(function () { nextSlide(); }, 3000);
}

window.addEventListener("load", function () {
  if (document.querySelector(".slideshow img")) {
    showSlide(slideIndex);
    restartAuto();
  }
});
