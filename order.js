const DISHES_API = "https://edu.std-900.ist.mospolytech.ru/labs/api/dishes";
const ORDERS_API = "https://edu.std-900.ist.mospolytech.ru/labs/api/orders";
const API_KEY = "eefd728b-acd4-47b7-89d2-af05c07a2afd";
const STORAGE_KEY = "order";

let dishes = [];
let orderObj = JSON.parse(localStorage.getItem(STORAGE_KEY) || "{}");

function normalizeCategory(cat) {
    if (!cat) return cat;
    if (cat === "main") return "main-course";
    return cat;
}

document.addEventListener("DOMContentLoaded", () => {
    loadDishesAndRender();
    document.getElementById("order-form")?.addEventListener("submit", onSubmit);
});

async function loadDishesAndRender() {
    try {
        const res = await fetch(`${DISHES_API}?api_key=${API_KEY}`);
        if (!res.ok) throw new Error("Ошибка сети " + res.status);
        const data = await res.json();
        dishes = Array.isArray(data) ? data : (data.dishes || []);
        dishes.forEach(d => d.category = normalizeCategory(d.category));
        renderSelectedList();
        updateTotalsAndSlots();
    } catch (e) {
        console.error(e);
        const container = document.getElementById("order-items");
        if (container) container.innerHTML = "<p>Не удалось загрузить данные о блюдах.</p>";
    }
}

function renderSelectedList() {
    const container = document.getElementById("order-items");
    if (!container) return;
    container.innerHTML = "";

    const keys = Object.values(orderObj);
    if (!keys || keys.length === 0) {
        container.innerHTML = `<p>Ничего не выбрано. Чтобы добавить блюда в заказ, перейдите на страницу <a href="order.html">Собрать ланч</a>.</p>`;
        return;
    }

    keys.forEach(kw => {
        const dish = dishes.find(d => d.keyword === kw);
        if (!dish) return;
        const card = document.createElement("div");
        card.className = "dish-card order-card";
        card.innerHTML = `
            <img src="${dish.image}" alt="${dish.name}">
            <p><strong>${dish.price}₽</strong></p>
            <p>${dish.name}</p>
            <p>${dish.count || ""}</p>
            <button type="button" class="remove-btn" data-key="${dish.keyword}">Удалить</button>
        `;
        container.appendChild(card);
    });

    container.querySelectorAll(".remove-btn").forEach(btn => {
        btn.addEventListener("click", (e) => {
            const key = e.currentTarget.dataset.key;
            removeFromOrder(key);
        });
    });
}

function removeFromOrder(keyword) {
    Object.keys(orderObj).forEach(cat => {
        if (orderObj[cat] === keyword) delete orderObj[cat];
    });
    localStorage.setItem(STORAGE_KEY, JSON.stringify(orderObj));
    renderSelectedList();
    updateTotalsAndSlots();
}

function updateTotalsAndSlots() {
    const slots = {
        soup: document.getElementById("order-soup"),
        "main-course": document.getElementById("order-main"),
        salad: document.getElementById("order-salad"),
        drink: document.getElementById("order-drink"),
        dessert: document.getElementById("order-dessert")
    };

    Object.values(slots).forEach(el => { if (el) el.textContent = "Блюдо не выбрано"; });

    const selectedDishes = Object.values(orderObj)
        .map(k => dishes.find(d => d.keyword === k))
        .filter(Boolean);

    let total = 0;
    selectedDishes.forEach(d => {
        const slotEl = slots[d.category] || slots[normalizeCategory(d.category)];
        if (slotEl) slotEl.textContent = `${d.name} ${d.price}₽`;
        total += Number(d.price) || 0;
    });

    const totalEl = document.getElementById("order-total-sum");
    if (totalEl) totalEl.textContent = `${total}₽`;
}

function validateCombo(items) {
    const counts = { soup: 0, "main-course": 0, salad: 0, drink: 0 };
    items.forEach(i => {
        const cat = normalizeCategory(i.category);
        if (counts[cat] !== undefined) counts[cat]++;
    });
    const soup = counts.soup, main = counts["main-course"], salad = counts.salad, drink = counts.drink;
    const total = soup + main + salad + drink;
    if (total === 0) return false;
    if (drink === 0) return false;
    if (soup > 0 && main === 0 && salad === 0) return false;
    if (salad > 0 && soup === 0 && main === 0) return false;
    if (drink > 0 && soup === 0 && main === 0 && salad === 0) return false;
    return true;
}

async function onSubmit(e) {
    e.preventDefault();
    const form = e.target;

    const selectedItems = Object.values(orderObj)
        .map(k => dishes.find(d => d.keyword === k))
        .filter(Boolean);

    if (!validateCombo(selectedItems)) {
        notify("Состав заказа не соответствует доступным комбо. Проверьте выбор.");
        return;
    }

    const getDishIdByKeyword = (keyword) => {
        const dish = dishes.find(d => d.keyword === keyword);
        return dish ? dish.id : null;
    };

    const orderData = {
        full_name: form.querySelector("#name")?.value || "",
        email: form.querySelector("#email")?.value || "",
        subscribe: form.querySelector("#subscribe")?.checked ? 1 : 0,
        phone: form.querySelector("#phone")?.value || "",
        delivery_address: form.querySelector("#address")?.value || "",
        delivery_type: form.querySelector('input[name="delivery-type"]:checked')?.value === "by_time" ? "by_time" : "now",
        comment: form.querySelector("#comment")?.value || "",
        drink_id: getDishIdByKeyword(orderObj.drink)
    };

    if (orderObj.soup) orderData.soup_id = getDishIdByKeyword(orderObj.soup);
    if (orderObj["main-course"]) orderData.main_course_id = getDishIdByKeyword(orderObj["main-course"]);
    if (orderObj.salad) orderData.salad_id = getDishIdByKeyword(orderObj.salad);
    if (orderObj.dessert) orderData.dessert_id = getDishIdByKeyword(orderObj.dessert);

    if (orderData.delivery_type === "by_time") {
        const timeInput = form.querySelector("#delivery-time");
        if (timeInput && timeInput.value) {
            orderData.delivery_time = timeInput.value;
        } else {
            notify("Укажите время доставки");
            return;
        }
    }

    try {
        const res = await fetch(`${ORDERS_API}?api_key=${API_KEY}`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(orderData)
        });

        const result = await res.json();
        
        if (!res.ok) {
            throw new Error(result.error || `Ошибка ${res.status}: ${JSON.stringify(result)}`);
        }

        console.log("Заказ создан:", result);

        localStorage.removeItem(STORAGE_KEY);
        orderObj = {};
        
        renderSelectedList();
        updateTotalsAndSlots();
        form.reset();
        
        notify("Заказ успешно оформлен!");
        
    } catch (err) {
        console.error("Ошибка оформления заказа:", err);
        notify(`Ошибка оформления заказа: ${err.message}`);
    }
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