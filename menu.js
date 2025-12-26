const API_URL = "https://edu.std-900.ist.mospolytech.ru/labs/api/dishes";
const STORAGE_KEY = "order"; 
const API_KEY = "eefd728b-acd4-47b7-89d2-af05c07a2afd";

let dishes = [];
let orderObj = JSON.parse(localStorage.getItem(STORAGE_KEY) || "{}");

const categories = {
  soup: { container: "soups", displayName: "Суп" },
  "main-course": { container: "mains", displayName: "Главное блюдо" },
  salad: { container: "salads", displayName: "Салат/стартер" },
  drink: { container: "drinks", displayName: "Напиток" },
  dessert: { container: "desserts", displayName: "Десерт" },
};

function normalizeCategory(cat) {
  if (!cat) return cat;
  if (cat === "main") return "main-course";
  return cat;
}

document.addEventListener("DOMContentLoaded", () => {
  loadDishes();
});

async function loadDishes() {
  try {
    const res = await fetch(`${API_URL}?api_key=${API_KEY}`);
    if (!res.ok) throw new Error("Ошибка сети: " + res.status);
    const data = await res.json();
    dishes = Array.isArray(data) ? data : (data.dishes || []);
    dishes.forEach(d => { d.category = normalizeCategory(d.category); });

    initFiltersAndRender();
    restoreSelectionsUI(); 
    ensureCheckoutPanel();
    updateCheckoutPanel();
  } catch (e) {
    console.error("Ошибка загрузки блюд:", e);
    const menuSection = document.getElementById("menu-section");
    if (menuSection) menuSection.innerHTML = "<p>Не удалось загрузить меню. Повторите позже.</p>";
  }
}

function initFiltersAndRender() {
  Object.entries(categories).forEach(([category, meta]) => {
    const section = document.getElementById(meta.container);
    if (!section) return;
    const existing = section.parentNode.querySelector(".filters");
    if (existing) existing.remove();

    const filterBlock = document.createElement("div");
    filterBlock.className = "filters";
    const filtersMap = {
      soup: { fish: "Рыбные", meat: "Мясные", veg: "Овощные" },
      "main-course": { fish: "Рыбные", meat: "Мясные", veg: "Овощные" },
      salad: { fish: "С рыбой", meat: "С мясом", veg: "Овощные", cheese: "С сыром" },
      drink: { cold: "Холодные", hot: "Горячие" },
      dessert: { small: "Маленькие", medium: "Средние", large: "Большие" },
    };
    const filters = filtersMap[category] || {};
    Object.entries(filters).forEach(([kind, label]) => {
      const btn = document.createElement("button");
      btn.textContent = label;
      btn.type = "button";
      btn.addEventListener("click", () => renderCategory(category, kind));
      filterBlock.appendChild(btn);
    });
    const resetBtn = document.createElement("button");
    resetBtn.textContent = "Сбросить фильтр";
    resetBtn.type = "button";
    resetBtn.addEventListener("click", () => renderCategory(category));
    filterBlock.appendChild(resetBtn);

    section.parentNode.insertBefore(filterBlock, section);
    renderCategory(category);
  });
}

function renderCategory(category, filterKind = null) {
  const meta = categories[category];
  if (!meta) return;
  const container = document.getElementById(meta.container);
  if (!container) return;
  container.innerHTML = "";

  let list = dishes.filter(d => normalizeCategory(d.category) === category);
  if (filterKind) list = list.filter(d => d.kind === filterKind);

  if (list.length === 0) {
    container.innerHTML = `<p class="empty">Ничего не найдено</p>`;
    return;
  }

  list.forEach(dish => {
    const card = document.createElement("div");
    card.className = "dish-card";
    const isSelected = orderObj[category] === dish.keyword;
    if (isSelected) card.classList.add("selected");

    card.innerHTML = `
      <img src="${dish.image}" alt="${dish.name}">
      <p><strong>${dish.price}₽</strong></p>
      <p>${dish.name}</p>
      <p class="dish-count">${dish.count || ""}</p>
      <button type="button" class="add-btn">${isSelected ? "Удалить" : "Добавить"}</button>
    `;
    const btn = card.querySelector(".add-btn");
    btn.addEventListener("click", () => {
      if (orderObj[category] === dish.keyword) {
        delete orderObj[category];
      } else {
        orderObj[category] = dish.keyword;
      }
      persistOrder();
      renderCategory(category);
      restoreSelectionsUI();
      updateCheckoutPanel();
    });

    container.appendChild(card);
  });
}

function persistOrder() {
  localStorage.setItem(STORAGE_KEY, JSON.stringify(orderObj));
}

function restoreSelectionsUI() {
  updateCheckoutPanel();
}

let checkoutPanel = null;

function ensureCheckoutPanel() {
  if (document.getElementById("checkout-panel")) return;
  checkoutPanel = document.createElement("div");
  checkoutPanel.id = "checkout-panel";
  checkoutPanel.className = "checkout-panel";
  checkoutPanel.innerHTML = `
    <div class="checkout-row">
      <div>Стоимость: <span id="checkout-sum">0</span>₽</div>
      <a id="checkout-link" href="order.html" class="checkout-link disabled" aria-disabled="true">Перейти к оформлению</a>
    </div>
  `;
  document.body.appendChild(checkoutPanel);
}

function updateCheckoutPanel() {
  const sumEl = document.getElementById("checkout-sum");
  const link = document.getElementById("checkout-link");
  if (!sumEl || !link) return;
  
  let sum = 0;
  Object.entries(orderObj).forEach(([cat, kw]) => {
    const dish = dishes.find(d => d.keyword === kw);
    if (dish) sum += Number(dish.price) || 0;
  });
  sumEl.textContent = sum;

  const items = getSelectedObjects();
  const valid = validateComboForLink(items); 

  if (Object.keys(orderObj).length === 0 || !valid) {
    link.classList.add("disabled");
    link.setAttribute("aria-disabled", "true");
    link.style.pointerEvents = "none";
    link.href = "#";
  } else {
    link.classList.remove("disabled");
    link.setAttribute("aria-disabled", "false");
    link.style.pointerEvents = "";
    link.href = "makeorder.html";
  }

  if (Object.keys(orderObj).length === 0) {
    checkoutPanel.style.display = "none";
  } else {
    checkoutPanel.style.display = "block";
  }
}

function getSelectedObjects() {
  const selected = [];
  Object.entries(orderObj).forEach(([cat, kw]) => {
    const dish = dishes.find(d => d.keyword === kw);
    if (dish) {
      const dishCopy = { ...dish };
      dishCopy.category = normalizeCategory(dish.category);
      selected.push(dishCopy);
    }
  });
  return selected;
}

function validateComboForLink(items) {
  const counts = { soup: 0, "main-course": 0, salad: 0, drink: 0 };
  items.forEach(i => {
    const cat = normalizeCategory(i.category);
    if (counts[cat] !== undefined) counts[cat]++;
  });
  
  const { soup, "main-course": main, salad, drink } = counts;
  const total = soup + main + salad + drink;

  if (total === 0) return false;
  if (drink === 0) return false;
  if (soup > 0 && main === 0 && salad === 0) return false;
  if (salad > 0 && soup === 0 && main === 0) return false;
  if (drink > 0 && soup === 0 && main === 0 && salad === 0) return false;
  
  return true;
}

function validateComboWithMessages(items) {
  const counts = { soup: 0, "main-course": 0, salad: 0, drink: 0 };
  items.forEach(i => {
    const cat = normalizeCategory(i.category);
    if (counts[cat] !== undefined) counts[cat]++;
  });
  
  const { soup, "main-course": main, salad, drink } = counts;
  const total = soup + main + salad + drink;

  if (total === 0) {
    return { valid: false, message: "Ничего не выбрано. Выберите блюда для заказа" };
  }

  if (total > 0 && drink === 0) {
    return { valid: false, message: "Выберите напиток" };
  }

  if (soup > 0 && main === 0 && salad === 0) {
    return { valid: false, message: "Выберите главное блюдо или салат" };
  }

  if (salad > 0 && soup === 0 && main === 0) {
    return { valid: false, message: "Выберите суп или главное блюдо" };
  }

  if (drink > 0 && soup === 0 && main === 0 && salad === 0) {
    return { valid: false, message: "Выберите главное блюдо" };
  }

  return { valid: true, message: "" };
}

function notify(text) {
  const old = document.querySelector(".alert-box");
  if (old) old.remove();

  const box = document.createElement("div");
  box.className = "alert-box";
  box.innerHTML = `
    <div class="alert-content">
      <p>${text}</p>
      <button id="alert-ok">Окей 👌</button>
    </div>
  `;
  document.body.appendChild(box);

  document.getElementById("alert-ok").onclick = () => box.remove();
}