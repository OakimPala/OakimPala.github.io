const ORDERS_API = "https://edu.std-900.ist.mospolytech.ru/labs/api/orders";
const DISHES_API = "https://edu.std-900.ist.mospolytech.ru/labs/api/dishes";
const API_KEY = "eefd728b-acd4-47b7-89d2-af05c07a2afd";

let orderlist = [];
let dishes = [];

document.addEventListener("DOMContentLoaded", () => {
    loadOrderlist();
    setupModalEvents();
});

async function loadOrderlist() {
    try {
        const response = await fetch(`${ORDERS_API}?api_key=${API_KEY}`);
        if (!response.ok) throw new Error(`Ошибка сети: ${response.status}`);
        
        const data = await response.json();
        orderlist = Array.isArray(data) ? data : (data.orderlist || []);
        
        await loadDishes();
        
        orderlist.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
        
        renderOrderlist();
    } catch (error) {
        console.error("Ошибка загрузки заказов:", error);
        showNotification("Не удалось загрузить историю заказов", "error");
    }
}

async function loadDishes() {
    try {
        const response = await fetch(`${DISHES_API}?api_key=${API_KEY}`);
        if (!response.ok) return;
        
        const data = await response.json();
        dishes = Array.isArray(data) ? data : (data.dishes || []);
    } catch (error) {
        console.error("Ошибка загрузки блюд:", error);
    }
}

function renderOrderlist() {
    const container = document.getElementById("orderlist-container");
    const noOrderlist = document.getElementById("no-orderlist");
    
    if (orderlist.length === 0) {
        container.innerHTML = "";
        container.parentElement.style.display = "none";
        noOrderlist.style.display = "block";
        return;
    }
    
    container.parentElement.style.display = "table";
    noOrderlist.style.display = "none";
    container.innerHTML = "";
    
    orderlist.forEach((order, index) => {
        const orderRow = createOrderRow(order, index + 1);
        container.appendChild(orderRow);
    });
}

function createOrderRow(order, number) {
    const row = document.createElement("tr");
    row.className = "order-row";
    row.dataset.id = order.id;
    
    const dishNames = [];
    if (order.soup_id) {
        const dish = dishes.find(d => d.id === order.soup_id);
        if (dish) dishNames.push(dish.name);
    }
    if (order.main_course_id) {
        const dish = dishes.find(d => d.id === order.main_course_id);
        if (dish) dishNames.push(dish.name);
    }
    if (order.salad_id) {
        const dish = dishes.find(d => d.id === order.salad_id);
        if (dish) dishNames.push(dish.name);
    }
    if (order.drink_id) {
        const dish = dishes.find(d => d.id === order.drink_id);
        if (dish) dishNames.push(dish.name);
    }
    if (order.dessert_id) {
        const dish = dishes.find(d => d.id === order.dessert_id);
        if (dish) dishNames.push(dish.name);
    }
    
    const orderDate = new Date(order.created_at);
    const formattedDate = orderDate.toLocaleDateString('ru-RU', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    
    let totalPrice = 0;
    [order.soup_id, order.main_course_id, order.salad_id, order.drink_id, order.dessert_id].forEach(id => {
        if (id) {
            const dish = dishes.find(d => d.id === id);
            if (dish) totalPrice += dish.price;
        }
    });
    
    let deliveryTime = "Как можно скорее (с 7:00 до 23:00)";
    if (order.delivery_type === "by_time" && order.delivery_time) {
        deliveryTime = order.delivery_time;
    }
    
    row.innerHTML = `
        <td>${number}</td>
        <td>${formattedDate}</td>
        <td>${dishNames.join(', ') || 'Нет блюд'}</td>
        <td>${totalPrice}₽</td>
        <td>${deliveryTime}</td>
        <td>
            <div class="order-actions">
                <button class="btn-icon details-btn" title="Подробнее" data-order-id="${order.id}">
                    <i class="bi bi-eye"></i>
                </button>
                <button class="btn-icon edit-btn" title="Редактировать" data-order-id="${order.id}">
                    <i class="bi bi-pencil"></i>
                </button>
                <button class="btn-icon delete-btn" title="Удалить" data-order-id="${order.id}">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </td>
    `;
    
    const detailsBtn = row.querySelector('.details-btn');
    const editBtn = row.querySelector('.edit-btn');
    const deleteBtn = row.querySelector('.delete-btn');
    
    detailsBtn.addEventListener('click', () => showOrderDetails(order.id));
    editBtn.addEventListener('click', () => openEditModal(order.id));
    deleteBtn.addEventListener('click', () => openDeleteModal(order.id));
    
    return row;
}

async function fetchOrderDetails(orderId) {
    try {
        const response = await fetch(`${ORDERS_API}/${orderId}?api_key=${API_KEY}`);
        
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.error || `Ошибка ${response.status}`);
        }
        
        const orderData = await response.json();
        return orderData;
    } catch (error) {
        console.error('Ошибка загрузки данных заказа:', error);
        showNotification(`Не удалось загрузить данные заказа: ${error.message}`, 'error');
        return null;
    }
}

async function showOrderDetails(orderId) {
    showNotification('Загрузка данных заказа...', 'info');
    
    const order = await fetchOrderDetails(orderId);
    if (!order) return;
    
    const modal = document.getElementById('details-modal');

    const dishItems = [];
    let totalPrice = 0;

    try {
        const dishesResponse = await fetch(`${DISHES_API}?api_key=${API_KEY}`);
        const dishesData = await dishesResponse.json();
        const currentDishes = Array.isArray(dishesData) ? dishesData : (dishesData.dishes || []);
        
        [['soup', order.soup_id], ['main_course', order.main_course_id], 
         ['salad', order.salad_id], ['drink', order.drink_id], 
         ['dessert', order.dessert_id]].forEach(([type, id]) => {
            if (id) {
                const dish = currentDishes.find(d => d.id === id);
                if (dish) {
                    dishItems.push(`<li>${dish.name} (${dish.price} ₽)</li>`);
                    totalPrice += dish.price;
                }
            }
        });
    } catch (error) {
        console.error('Ошибка загрузки данных блюд:', error);
        dishItems.push('<li>Не удалось загрузить данные о блюдах</li>');
    }
    
    const orderDate = new Date(order.created_at);
    const formattedDate = orderDate.toLocaleDateString('ru-RU', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });

    document.getElementById('detail-order-date').textContent = formattedDate;
    document.getElementById('detail-delivery-type').textContent = 
        order.delivery_type === "by_time" ? "Ко времени" : "Как можно скорее";
    document.getElementById('detail-full-name').textContent = order.full_name || 'Не указано';
    document.getElementById('detail-address').textContent = order.delivery_address || 'Не указан';
    document.getElementById('detail-delivery-time').textContent = 
        order.delivery_type === "by_time" && order.delivery_time ? order.delivery_time : 'Без времени';
    document.getElementById('detail-phone').textContent = order.phone || 'Не указан';
    document.getElementById('detail-email').textContent = order.email || 'Не указан';
    document.getElementById('detail-comment').textContent = order.comment || 'Нет комментария';
    document.getElementById('detail-dishes-list').innerHTML = dishItems.join('');
    document.getElementById('detail-total-price').textContent = totalPrice;
    
    modal.style.display = 'block';
}

async function openEditModal(orderId) {
    showNotification('Загрузка данных для редактирования...', 'info');
    
    const order = await fetchOrderDetails(orderId);
    if (!order) return;
    
    const modal = document.getElementById('edit-modal');

    const dishItems = [];
    let totalPrice = 0;
    
    try {
        const dishesResponse = await fetch(`${DISHES_API}?api_key=${API_KEY}`);
        const dishesData = await dishesResponse.json();
        const currentDishes = Array.isArray(dishesData) ? dishesData : (dishesData.dishes || []);
        
        [['soup', order.soup_id], ['main_course', order.main_course_id], 
         ['salad', order.salad_id], ['drink', order.drink_id], 
         ['dessert', order.dessert_id]].forEach(([type, id]) => {
            if (id) {
                const dish = currentDishes.find(d => d.id === id);
                if (dish) {
                    dishItems.push(`<li>${dish.name} (${dish.price} ₽)</li>`);
                    totalPrice += dish.price;
                }
            }
        });
    } catch (error) {
        console.error('Ошибка загрузки данных блюд:', error);
        dishItems.push('<li>Не удалось загрузить данные о блюдах</li>');
    }
    
    const orderDate = new Date(order.created_at);
    const formattedDate = orderDate.toLocaleDateString('ru-RU', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });

    document.getElementById('edit-order-id').value = order.id;
    document.getElementById('edit-order-date').textContent = formattedDate;
    document.getElementById('edit-full-name').value = order.full_name || '';
    document.getElementById('edit-email').value = order.email || '';
    document.getElementById('edit-phone').value = order.phone || '';
    document.getElementById('edit-address').value = order.delivery_address || '';
    
    const deliveryTypeSelect = document.getElementById('edit-delivery-type-select');
    deliveryTypeSelect.value = order.delivery_type || 'now';
    
    const timeGroup = document.getElementById('edit-time-group');
    const timeInput = document.getElementById('edit-delivery-time');
    
    if (order.delivery_type === 'by_time' && order.delivery_time) {
        timeGroup.style.display = 'flex';
        timeInput.value = order.delivery_time;
    } else {
        timeGroup.style.display = 'none';
        timeInput.value = '';
    }
    
    document.getElementById('edit-comment').value = order.comment || '';
    document.getElementById('edit-dishes-list').innerHTML = dishItems.join('');
    document.getElementById('edit-total-price').textContent = totalPrice;
    
    modal.style.display = 'block';
}

async function handleEditSubmit(e) {
    e.preventDefault();
    
    const orderId = document.getElementById('edit-order-id').value;
    const deliveryType = document.getElementById('edit-delivery-type-select').value;
    
    const updatedOrder = {
        full_name: document.getElementById('edit-full-name').value,
        email: document.getElementById('edit-email').value,
        phone: document.getElementById('edit-phone').value,
        delivery_address: document.getElementById('edit-address').value,
        delivery_type: deliveryType,
        comment: document.getElementById('edit-comment').value
    };
    
    if (deliveryType === 'by_time') {
        const timeValue = document.getElementById('edit-delivery-time').value;
        if (timeValue) {
            updatedOrder.delivery_time = timeValue;
        }
    } else {
        updatedOrder.delivery_time = null;
    }
    
    try {
        showNotification('Сохранение изменений...', 'info');
        
        const response = await fetch(`${ORDERS_API}/${orderId}?api_key=${API_KEY}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(updatedOrder)
        });
        
        const result = await response.json();
        
        if (!response.ok) {
            throw new Error(result.error || `Ошибка ${response.status}`);
        }
        
        const index = orderlist.findIndex(o => o.id == orderId);
        if (index !== -1) {

            const updatedOrderData = await fetchOrderDetails(orderId);
            if (updatedOrderData) {
                orderlist[index] = updatedOrderData;
            }
        }
        
        closeAllModals();
        renderOrderlist();
        showNotification('Заказ успешно изменён', 'success');
    } catch (error) {
        console.error('Ошибка редактирования заказа:', error);
        showNotification(`Не удалось изменить заказ: ${error.message}`, 'error');
    }
}

async function openDeleteModal(orderId) {
    showNotification('Загрузка данных заказа...', 'info');
    
    const order = await fetchOrderDetails(orderId);
    if (!order) return;
    
    const modal = document.getElementById('delete-modal');
    document.getElementById('confirm-delete').dataset.orderId = order.id;
    
    const orderDate = new Date(order.created_at);
    const formattedDate = orderDate.toLocaleDateString('ru-RU', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
    
    const modalBody = modal.querySelector('.modal-body');
    modalBody.innerHTML = `
        <p>Вы уверены, что хотите удалить заказ #${order.id} от ${formattedDate}?</p>
        <p class="text-muted small">Клиент: ${order.full_name || 'Не указано'}</p>
        <p class="text-muted small">Сумма: ${order.total_price || 'Не указана'}₽</p>
    `;
    
    modal.style.display = 'block';
}

async function handleDelete() {
    const orderId = document.getElementById('confirm-delete').dataset.orderId;
    
    try {
        showNotification('Удаление заказа...', 'info');
        
        const response = await fetch(`${ORDERS_API}/${orderId}?api_key=${API_KEY}`, {
            method: 'DELETE'
        });
        
        const result = await response.json();
        
        if (!response.ok) {
            throw new Error(result.error || `Ошибка ${response.status}`);
        }
        
        orderlist = orderlist.filter(o => o.id != orderId);
        
        closeAllModals();
        renderOrderlist();
        showNotification('Заказ успешно удалён', 'success');
    } catch (error) {
        console.error('Ошибка удаления заказа:', error);
        showNotification(`Не удалось удалить заказ: ${error.message}`, 'error');
    }
}

function setupModalEvents() {
    document.querySelectorAll('.close').forEach(closeBtn => {
        closeBtn.addEventListener('click', closeAllModals);
    });

    document.querySelectorAll('.close-modal').forEach(btn => {
        btn.addEventListener('click', closeAllModals);
    });

    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeAllModals();
        });
    });

    document.getElementById('edit-form').addEventListener('submit', handleEditSubmit);

    document.getElementById('confirm-delete').addEventListener('click', handleDelete);

    document.getElementById('edit-delivery-type-select').addEventListener('change', function() {
        const timeGroup = document.getElementById('edit-time-group');
        timeGroup.style.display = this.value === 'by_time' ? 'flex' : 'none';
    });
}

function closeAllModals() {
    document.querySelectorAll('.modal').forEach(modal => {
        modal.style.display = 'none';
    });
}

function showNotification(message, type = 'info') {
    const oldNotification = document.querySelector('.notification');
    if (oldNotification) oldNotification.remove();
    
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            if (notification.parentNode) notification.remove();
        }, 300);
    }, 3000);
}