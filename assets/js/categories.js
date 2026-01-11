document.addEventListener("DOMContentLoaded", () => {
  const grid = document.getElementById("all-category-grid");
  const profileBtn = document.getElementById("profileBtn");

  // login status from PHP
  const isLoggedIn = window.IS_LOGGED_IN === true;

  if (isLoggedIn) {
    profileBtn.innerText = "🙎🏻‍♂️ Profile";
    profileBtn.href = "../../controllers/loginHomeController.php";
  } else {
    profileBtn.innerText = "🙎🏻‍♂️ Sign In";
    profileBtn.href = "login.php";
  }

  loadAllCategories();

  async function loadAllCategories() {
    grid.innerHTML = "";

    try {
      const res = await fetch(`../../api/api.php?action=get_categories`);
      const cats = await res.json();

      if (!Array.isArray(cats) || cats.length === 0) {
        grid.innerHTML = `<div>No categories found.</div>`;
        return;
      }

      cats.forEach((c) => {
        const img = c.image
          ? `../../uploads/${c.image}`
          : `../../assets/images/default.png`;
        const name = c.name || "Category";

        const card = document.createElement("div");
        card.className = "cat-card";
        card.innerHTML = `
          <div class="featured-img">
            <img src="${img}" onerror="this.src='../../assets/images/default.png'">
          </div>
          <div class="cat-name">${name}</div>
        `;

        card.addEventListener("click", () => {
          window.location.href = `search.php?category=${encodeURIComponent(name)}`;
        });

        grid.appendChild(card);
      });
    } catch (e) {
      grid.innerHTML = `<div>Failed to load categories.</div>`;
      console.error(e);
    }
  }
});
