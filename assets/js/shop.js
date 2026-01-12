let currentSlide = 0;
let autoTimer = null;
let bannerImages = [];
let productOffset = 0;
const productsPerLoad = 5;

const LOADED_PRODUCT_IDS = new Set();

// banner logic 
function fetchBanners() {
  fetch("../../api/api.php?action=get_sliders")
    .then((r) => r.json())
    .then((data) => {
      bannerImages = Array.isArray(data) ? data : [];
      if (bannerImages.length > 0) renderBanners();
    })
    .catch((err) => console.error("Error loading banners:", err));
}

function renderBanners() {
  const slider = document.getElementById("slider");
  if (slider && bannerImages.length > 0) {
    slider.src = `../../uploads/${bannerImages[0].image}`;
    startAutoSlide();
  }
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

// categories logic 
function fetchCategories() {
  fetch("../../api/api.php?action=get_categories")
    .then((r) => r.json())
    .then((data) => renderCategories(Array.isArray(data) ? data : []))
    .catch((err) => console.error("Error loading categories:", err));
}

function renderCategories(categories) {
  const headerList = document.getElementById("category-list");
  const homeGrid = document.getElementById("category-grid");

  if (headerList) {
    headerList.innerHTML = categories
      .map(
        (cat) =>
          `<a href="search.php?category=${encodeURIComponent(cat.name)}">${
            cat.name
          }</a>`
      )
      .join("");
  }

  if (homeGrid) {
    homeGrid.innerHTML = categories
      .map((cat) => {
        const imgPath = `../../uploads/${cat.image}`;
        return `
          <a class="cat-item" href="search.php?category=${encodeURIComponent(
            cat.name
          )}">
            <div class="cat-circle">
              <img src="${imgPath}" alt="${
          cat.name
        }" onerror="this.src='../../assets/images/default.png'">
            </div>
            <div class="cat-name">${cat.name}</div>
          </a>
        `;
      })
      .join("");
  }
}

// load products loogic 
function loadProducts() {
  const loadMoreBtn = document.getElementById("load-more-btn");
  if (loadMoreBtn) loadMoreBtn.innerText = "Loading...";

  fetch(
    `../../api/api.php?action=get_products&limit=${productsPerLoad}&page=${productOffset}`
  )
    .then((res) => res.json())
    .then((products) => {
      products = Array.isArray(products) ? products : [];

      if (products.length === 0) {
        if (loadMoreBtn) loadMoreBtn.style.display = "none";
        return;
      }

      renderProducts(products);
      productOffset += products.length;

      if (loadMoreBtn) {
        loadMoreBtn.innerText = "Load More";
        if (products.length < productsPerLoad) {
          loadMoreBtn.style.display = "none";
        }
      }
    })
    .catch((err) => {
      console.error("Error loading products:", err);
      if (loadMoreBtn) loadMoreBtn.innerText = "Error (Try Again)";
    });
}

function renderProducts(products) {
  const grid = document.getElementById("featured-grid");
  if (!grid) return;

  let html = "";

  products.forEach((p) => {
    const pid = String(p.id);
    if (LOADED_PRODUCT_IDS.has(pid)) return;
    LOADED_PRODUCT_IDS.add(pid);

    const imgPath = `../../uploads/${p.image}`;
    const originalPrice = parseFloat(p.price) || 0;
    const discountPrice = parseFloat(p.discount_price) || 0;

    const priceHtml =
      discountPrice > 0
        ? `<span style="text-decoration: line-through; color: #888; font-size: 14px; margin-right: 5px;">৳${originalPrice}</span>
           <span style="color: #e74c3c; font-weight: bold;">৳${discountPrice}</span>`
        : `<span style="color: #e74c3c; font-weight: bold;">৳${originalPrice}</span>`;

    html += `
      <a class="featured-item" href="product_details.php?id=${p.id}">
        <div class="featured-img">
          <img src="${imgPath}" alt="${p.name}" onerror="this.src='../../assets/images/default.png'">
        </div>
        <div class="featured-name">${p.name}</div>
        <div class="featured-price">${priceHtml}</div>
      </a>
    `;
  });

  grid.insertAdjacentHTML("beforeend", html);
}

document.addEventListener("DOMContentLoaded", function () {
  fetchBanners();
  fetchCategories();
  loadProducts();

  const prevBtn = document.getElementById("prevBtn");
  const nextBtn = document.getElementById("nextBtn");
  if (prevBtn) prevBtn.addEventListener("click", prevSlide);
  if (nextBtn) nextBtn.addEventListener("click", nextSlide);

  const loadMoreBtn = document.getElementById("load-more-btn");
  if (loadMoreBtn) loadMoreBtn.addEventListener("click", loadProducts);

  const sliderContainer = document.getElementById("slider-container");
  if (sliderContainer) {
    sliderContainer.addEventListener("mouseenter", stopAutoSlide);
    sliderContainer.addEventListener("mouseleave", startAutoSlide);
  }
});
