let ALL_PRODUCTS = [];
let FILTERS_HOOKED = false;

function getFinalPrice(p) {
  const original = parseFloat(p.price) || 0;
  const discount = parseFloat(p.discount_price) || 0;
  return discount > 0 ? discount : original;
}

function renderProducts(products, emptyMsg = "No products found") {
  const grid = document.getElementById("search-grid");

  if (!products || products.length === 0) {
    grid.innerHTML = `<h3 style="color:#666;">${emptyMsg}</h3>`;
    return;
  }

  let html = "";
  products.forEach((p) => {
    const imgPath = `../../uploads/${p.image}`;
    const originalPrice = parseFloat(p.price) || 0;
    const discountPrice = parseFloat(p.discount_price) || 0;

    const priceHtml =
      discountPrice > 0
        ? `<span style="text-decoration:line-through;color:#888;margin-right:5px;">৳${originalPrice}</span>
           <span style="color:#e74c3c;font-weight:bold;">৳${discountPrice}</span>`
        : `<span style="color:#e74c3c;font-weight:bold;">৳${originalPrice}</span>`;

    html += `
      <a class="featured-item" href="product_details.php?id=${p.id}">
        <div class="featured-img">
          <img src="${imgPath}" onerror="this.src='../../assets/images/default.png'">
        </div>
        <div class="featured-name">${p.name}</div>
        <div class="featured-price">${priceHtml}</div>
      </a>
    `;
  });

  grid.innerHTML = html;
}

function applyFilters() {
  let list = [...ALL_PRODUCTS];

  const minPriceEl = document.getElementById("minPrice");
  const maxPriceEl = document.getElementById("maxPrice");
  const inStockEl = document.getElementById("inStockOnly");
  const sortEl = document.getElementById("sortPrice");

  const minP = parseFloat(minPriceEl?.value);
  const maxP = parseFloat(maxPriceEl?.value);
  const inStockOnly = !!inStockEl?.checked;
  const sortPrice = sortEl?.value || "";


  if (inStockOnly) {
    list = list.filter((p) => (parseInt(p.quantity, 10) || 0) > 0);
  }

  if (!isNaN(minP)) {
    list = list.filter((p) => getFinalPrice(p) >= minP);
  }
  if (!isNaN(maxP)) {
    list = list.filter((p) => getFinalPrice(p) <= maxP);
  }

  if (sortPrice === "low") {
    list.sort((a, b) => getFinalPrice(a) - getFinalPrice(b));
  } else if (sortPrice === "high") {
    list.sort((a, b) => getFinalPrice(b) - getFinalPrice(a));
  }

  renderProducts(list, "No products match your filters.");
}

function hookFilterEventsOnce() {
  if (FILTERS_HOOKED) return;
  FILTERS_HOOKED = true;

  const resetBtn = document.getElementById("resetFilterBtn");
  const minPriceEl = document.getElementById("minPrice");
  const maxPriceEl = document.getElementById("maxPrice");
  const inStockEl = document.getElementById("inStockOnly");
  const sortEl = document.getElementById("sortPrice");

  if (minPriceEl) minPriceEl.addEventListener("input", applyFilters);
  if (maxPriceEl) maxPriceEl.addEventListener("input", applyFilters);
  if (inStockEl) inStockEl.addEventListener("change", applyFilters);
  if (sortEl) sortEl.addEventListener("change", applyFilters);

  if (resetBtn) {
    resetBtn.addEventListener("click", () => {
      if (minPriceEl) minPriceEl.value = "";
      if (maxPriceEl) maxPriceEl.value = "";
      if (inStockEl) inStockEl.checked = false;
      if (sortEl) sortEl.value = "";
      applyFilters();
    });
  }
}

function runSearch(term) {
  const grid = document.getElementById("search-grid");
  grid.innerHTML = "<h3>Searching...</h3>";

  const params = new URLSearchParams(window.location.search);
  const category = params.get("category");
  const query = params.get("query");

  let apiUrl = "";
  let emptyMsg = "No products found";

  if (category) {
    apiUrl = `../../api/api.php?action=get_products&category=${encodeURIComponent(category)}`;
    emptyMsg = `No products found in "${category}" category`;
  } else {
    const searchTerm = query || term || "";
    apiUrl = `../../api/api.php?action=get_products&search=${encodeURIComponent(searchTerm)}`;
    emptyMsg = `No products found for "${searchTerm}"`;
  }

  fetch(apiUrl)
    .then((res) => res.json())
    .then((products) => {
      if (!Array.isArray(products)) products = [];

      ALL_PRODUCTS = products;
      hookFilterEventsOnce();

      renderProducts(products, emptyMsg);
    })
    .catch((err) => {
      console.error(err);
      grid.innerHTML = "<h3>Error loading results.</h3>";
    });
}
