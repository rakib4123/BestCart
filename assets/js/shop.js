// =============================
// BANNER FEATURE ONLY (SLIDERS)
// =============================

// CONFIGURATION & STATE
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

// INITIALIZATION (Banner only)
document.addEventListener("DOMContentLoaded", function () {
  fetchBanners();

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
