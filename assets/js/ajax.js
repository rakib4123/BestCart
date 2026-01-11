async function readJsonSafe(res) {
  const text = await res.text();
  const info = `HTTP ${res.status} ${res.statusText}${res.redirected ? " (redirected)" : ""}`;

  if (!text || !text.trim()) {
    throw new Error(
      "Server returned empty response. " + info + ". Check controller path and PHP error log."
    );
  }

  try {
    return JSON.parse(text);
  } catch (e) {
    const preview = text.trim().slice(0, 300);
    throw new Error(
      "Server did not return JSON. " + info + ". Response starts with: " + preview
    );
  }
}

// -----------------------------
// Toast (Popup) Notifications
// -----------------------------
function ensureToastContainer() {
  let c = document.getElementById("toast-container");
  if (!c) {
    c = document.createElement("div");
    c.id = "toast-container";
    document.body.appendChild(c);
  }
  return c;
}

function showToast(type, message, timeoutMs = 2600) {
  const c = ensureToastContainer();
  const t = document.createElement("div");
  t.className = `toast toast-${type || "info"}`;
  t.setAttribute("role", "status");
  t.innerHTML = `
    <div class="toast-body">
      <div class="toast-title">${type === "error" ? "Error" : type === "success" ? "Success" : "Notice"}</div>
      <div class="toast-msg"></div>
    </div>
    <button type="button" class="toast-close" aria-label="Close">×</button>
  `;
  t.querySelector(".toast-msg").textContent = message || "";

  const remove = () => {
    t.classList.add("toast-hide");
    setTimeout(() => t.remove(), 220);
  };

  t.querySelector(".toast-close").addEventListener("click", remove);
  c.appendChild(t);
  // animate-in
  requestAnimationFrame(() => t.classList.add("toast-show"));
  setTimeout(remove, timeoutMs);
}

// -----------------------------
// Custom Confirm Modal (Popup)
// -----------------------------
function confirmPopup(message) {
  return new Promise((resolve) => {
    const overlay = document.createElement("div");
    overlay.className = "modal-overlay";
    overlay.innerHTML = `
      <div class="modal">
        <div class="modal-title">Confirm</div>
        <div class="modal-msg"></div>
        <div class="modal-actions">
          <button type="button" class="btn btn-secondary modal-cancel">Cancel</button>
          <button type="button" class="btn btn-primary modal-ok">Yes</button>
        </div>
      </div>
    `;
    overlay.querySelector(".modal-msg").textContent = message || "Are you sure?";
    document.body.appendChild(overlay);

    const cleanup = () => overlay.remove();
    overlay.addEventListener("click", (e) => {
      if (e.target === overlay) {
        cleanup();
        resolve(false);
      }
    });
    overlay.querySelector(".modal-cancel").addEventListener("click", () => {
      cleanup();
      resolve(false);
    });
    overlay.querySelector(".modal-ok").addEventListener("click", () => {
      cleanup();
      resolve(true);
    });
  });
}

// -----------------------------
// Partial refresh helper
// -----------------------------
async function refreshPartial(targetSelector, url) {
  if (!targetSelector || !url) return;
  const target = document.querySelector(targetSelector);
  if (!target) return;

  const res = await fetch(url, {
    method: "GET",
    headers: { "X-Requested-With": "XMLHttpRequest", "X-CSRF-Token": getCsrfToken() }
  });
  const html = await res.text();
  target.innerHTML = html;
  // re-render lucide icons if present
  if (window.lucide && typeof window.lucide.createIcons === "function") {
    window.lucide.createIcons();
  }
}


function getCsrfToken() {
  const meta = document.querySelector('meta[name="csrf-token"]');
  return meta ? meta.getAttribute("content") : "";
}

async function ajaxPostForm(form, submitter) {
  const fd = new FormData(form);
  if (!fd.has('csrf_token')) {
    const t = getCsrfToken();
    if (t) fd.append('csrf_token', t);
  }
  if (submitter && submitter.name) {
    fd.append(submitter.name, submitter.value || "1");
  }

  const res = await fetch(form.action, {
    method: "POST",
    body: fd,
    headers: { "X-Requested-With": "XMLHttpRequest", "X-CSRF-Token": getCsrfToken() }
  });

  if (!res.ok) {
    const text = await res.text();
    const preview = (text || "").trim().slice(0, 300);
    throw new Error(`Request failed. HTTP ${res.status}. ${preview}`);
  }

  const json = await readJsonSafe(res);
  if (!json.status) throw new Error(json.message || "Failed");

  if (json.data && json.data.redirect) {
    window.location.href = json.data.redirect;
    return json;
  }

  return json;
}

async function ajaxGetLink(a) {
  const res = await fetch(a.href, {
    method: "GET",
    headers: { "X-Requested-With": "XMLHttpRequest", "X-CSRF-Token": getCsrfToken() }
  });

  const json = await readJsonSafe(res);
  if (!json.status) throw new Error(json.message || "Failed");
  return json;
}

document.addEventListener("submit", async (e) => {
  const form = e.target;
  if (!form.matches("form[data-ajax='true']")) return;

  e.preventDefault();

  // JS validation (HTML5 + simple rules)
  if (form.dataset.validate === 'true') {
    if (!form.checkValidity()) {
      form.reportValidity();
      return;
    }
    // Numeric fields must be >= 0
    form.querySelectorAll('input[type="number"]').forEach((inp) => {
      if (inp.value !== '' && Number(inp.value) < 0) inp.value = 0;
    });
  }

  try {
    const json = await ajaxPostForm(form, e.submitter);

    if (form.dataset.reset === "true") form.reset();

    if (!(json && json.data && json.data.redirect)) {
      showToast("success", (json && json.message) || "Success");
    }

    // Optional partial refresh (no full page reload)
    if (form.dataset.refreshTarget && form.dataset.refreshUrl) {
      await refreshPartial(form.dataset.refreshTarget, form.dataset.refreshUrl);
    }

    if (form.dataset.remove) {
      const el = document.querySelector(form.dataset.remove);
      if (el) el.remove();
    }
  } catch (err) {
    showToast("error", err.message || "Request failed");
  }
});

document.addEventListener("click", async (e) => {
  const a = e.target.closest("a[data-ajax-link='true']");
  if (!a) return;

  e.preventDefault();

  if (a.dataset.confirm === "true") {
    const ok = await confirmPopup(a.dataset.confirmText || "Are you sure?");
    if (!ok) return;
  }

  try {
    const json = await ajaxGetLink(a);

    if (a.dataset.remove) {
      const el = document.querySelector(a.dataset.remove);
      if (el) el.remove();
    }

    showToast("success", json.message || "Done");

    // Optional partial refresh (no full page reload)
    if (a.dataset.refreshTarget && a.dataset.refreshUrl) {
      await refreshPartial(a.dataset.refreshTarget, a.dataset.refreshUrl);
    }
  } catch (err) {
    showToast("error", err.message || "Request failed");
  }
});
