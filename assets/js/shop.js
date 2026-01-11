let currentSlide = 0;
let autoTimer = null;
let bannerImages = [];

function fetchBanners() {
  fetch("../../api/api.php?action=get_sliders")
    .then((r) => r.json())
    .then((data) => {
      bannerImages = Array.isArray(data) ? data : [];
      if (bannerImages.length > 0) {
        renderBanners();
      }
    })
    .catch((err) => console.error("Error loading banners:", err));
}

function renderBanners() {
  const slider = document.getElementById("slider");
  if (!slider || bannerImages.length === 0) return;

  slider.src = `../../uploads/${bannerImages[0].image}`;
  startAutoSlide();
}

function showSlide(index) {
  const slider = document.getElementById("slider");
  if (!slider || bannerImages.length === 0) return;

  if (index < 0) index = bannerImages.length - 1;
  if (index >= bannerImages.length) index = 0;

  currentSlide = index;
  slider.src = `../../uploads/${bannerImages[currentSlide].image}`;
}

function nextSlide() {
  showSlide(currentSlide + 1);
}

function prevSlide() {
  showSlide(currentSlide - 1);
}

function startAutoSlide() {
  stopAutoSlide();
  autoTimer = setInterval(nextSlide, 3500);
}

function stopAutoSlide() {
  if (autoTimer) clearInterval(autoTimer);
  autoTimer = null;
}

/* --- Categories Feature (from feature2) --- */

function fetchCategories() {
  fetch("../../api/api.php?action=get_categories")
    .then((r) => r.json())
    .then((data) => renderCategories(Array.isArray(data) ? data : []))
    .catch((err) => console.error("Error loading categories:", err));
}

function renderCategories(categories) {
  const headerList = document.getElementById("category-list");
  const homeGrid = document.getElementById("category-grid");

  // Header dropdown
  if (headerList) {
    headerList.innerHTML = categories
      .map(
        (cat) =>
          `<a href="search.php?category=${encodeURIComponent(cat.name)}">${cat.name}</a>`
      )
      .join("");
  }

  // Homepage grid
  if (homeGrid) {
    homeGrid.innerHTML = categories
      .map((cat) => {
        const imgPath = `../../uploads/${cat.image}`;
        return `
          <a class="cat-item" href="search.php?category=${encodeURIComponent(cat.name)}">
            <div class="cat-circle">
              <img src="${imgPath}" alt="${cat.name}" onerror="this.src='../../assets/images/default.png'">
            </div>
            <div class="cat-name">${cat.name}</div>
          </a>
        `;
      })
      .join("");
  }
}

document.addEventListener("DOMContentLoaded", function () {
  fetchBanners();
  fetchCategories();

  const prevBtn = document.getElementById("prevBtn");
  const nextBtn = document.getElementById("nextBtn");
  if (prevBtn) prevBtn.addEventListener("click", prevSlide);
  if (nextBtn) nextBtn.addEventListener("click", nextSlide);

  const sliderContainer = document.getElementById("slider-container");
  if (sliderContainer) {
    sliderContainer.addEventListener("mouseenter", stopAutoSlide);
    sliderContainer.addEventListener("mouseleave", startAutoSlide);
  }
});
