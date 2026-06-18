// Mobile menu toggle
const toggleBtn = document.querySelector(".menu-toggle");
const navList = document.querySelector("nav ul");

if (toggleBtn && navList) {
  toggleBtn.addEventListener("click", () => {
    navList.classList.toggle("open");
  });
}
