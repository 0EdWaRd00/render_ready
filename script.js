document.addEventListener("DOMContentLoaded", function () {
    checkAuthStatus();

    // Получаем модальные окна и кнопки
    const loginModal = document.getElementById("login-modal");
    const registerModal = document.getElementById("register-modal");
    const profileModal = document.getElementById("profileModal");
    const authBtn = document.getElementById("auth-btn");
    const profileButton = document.getElementById("profileButton");
    const showRegisterBtn = document.getElementById("show-register");
    const showLoginBtn = document.getElementById("show-login");
    const closeButtons = document.querySelectorAll(".close");

    // Функция открытия модального окна
    function openModal(modal) {
        closeAllModals(); // Закрываем все перед открытием нового
        modal.style.display = "block";
    }

    // Функция закрытия всех модальных окон
    function closeAllModals() {
        document.querySelectorAll(".modal").forEach(modal => {
            modal.style.display = "none";
        });
    }

    // Кнопка "Вы вошли как гость" -> Открывает окно входа
    authBtn?.addEventListener("click", function () {
        openModal(loginModal);
    });

    // Кнопка "Вы вошли как ..." -> Открывает окно профиля
    profileButton?.addEventListener("click", function () {
        openModal(profileModal);
    });

    // Переключение между входом и регистрацией
    showRegisterBtn?.addEventListener("click", function () {
        openModal(registerModal);
    });

    showLoginBtn?.addEventListener("click", function () {
        openModal(loginModal);
    });

    // Закрытие модального окна по кнопке "×"
    closeButtons.forEach(button => {
        button.addEventListener("click", closeAllModals);
    });

    // Закрытие окна по клику вне его области
    window.addEventListener("click", function (event) {
        if (event.target.classList.contains("modal")) {
            closeAllModals();
        }
    });

    // Форма регистрации
    document.getElementById("register-form")?.addEventListener("submit", async function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        try {
            const response = await fetch("auth/register.php", {
                method: "POST",
                body: formData
            });
        
            const result = await response.json();
            
            showNotification(result.message, result.status === "success" ? "success" : "error");
            
            if (result.status === "success") {
                setTimeout(() => location.reload(), 1500);
            }
        } catch (error) {
            showNotification("Ошибка соединения с сервером", "error");
            console.error("Ошибка регистрации:", error);
        }
    });
    
    

    // Форма входа
    document.getElementById('login-form')?.addEventListener('submit', async function (e) {
        e.preventDefault();
        
        const formData = {
            username: this.querySelector('[name="username"]').value.trim(),
            password: this.querySelector('[name="password"]').value.trim()
        };
        
        // Валидация на клиенте
        if (!formData.username || !formData.password) {
            showNotification('Заполните все поля', 'error');
            return;
        }
        
        try {
            const response = await fetch('auth/login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });
            
            const result = await response.json();
            
            if (!response.ok) {
                throw new Error(result.message || 'Ошибка сервера');
            }
            
            if (result.status === 'admin') {
                showNotification("Успешный вход как администратор", "success");
                setTimeout(() => window.location.href = 'admin/index.php', 1500);
            } else if (result.status === 'success') {
                showNotification("Вы успешно вошли в систему", "success");
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification(result.message || "Ошибка входа", "error");
            }
        } catch (error) {
            showNotification(error.message || "Ошибка соединения с сервером", "error");
            console.error("Ошибка входа:", error);
        }
    });
    
    
    

    // Выход из аккаунта
document.getElementById('logoutBtn')?.addEventListener('click', async function() {
    try {
        const response = await fetch('../auth/logout.php', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const result = await response.json();
        
        if (result.status === 'success') {
            showNotification(result.message, "success");
            
            // Плавное перенаправление после показа уведомления
            setTimeout(() => {
                window.location.href = result.redirect;
            }, 1500);
        } else {
            showNotification("Ошибка при выходе", "error");
        }
    } catch (error) {
        showNotification("Ошибка соединения с сервером", "error");
        console.error("Ошибка выхода:", error);
    }
});

// Обработчик кнопки оформления тура
document.getElementById('order-btn')?.addEventListener('click', function(e) {
    if (!isLoggedIn()) {
        e.preventDefault();
        showNotification('Для оформления тура войдите или зарегистрируйтесь', 'warning');
        openModal(loginModal);
    }
});

// Вспомогательная функция проверки авторизации
function isLoggedIn() {
    return document.getElementById('profileButton') !== null;
}

// Проверка авторизации и загрузка данных профиля
async function checkAuthStatus() {
    try {
        const response = await fetch("auth/check_auth.php");
        const data = await response.json();
        
        if (data.isLoggedIn) {
            // Обновляем кнопку профиля
            const profileButton = document.getElementById("profileButton");
            if (profileButton) {
                profileButton.textContent = `Вы вошли как ${data.username}`;
            }
            
            // Предзагружаем данные профиля
            await loadProfileData();
        }
    } catch (error) {
        console.error("Ошибка проверки авторизации:", error);
    }
}

// Функция загрузки данных профиля
async function loadProfileData() {
    try {
        console.log('Fetching profile data...');
        const response = await fetch('auth/get_profile.php');
        
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Profile data:', data);
        
        if (data.status === "error") {
            throw new Error(data.message);
        }
        
        // Обновляем данные в модальном окне
        document.getElementById('profile-username').textContent = data.username || 'Не указан';
        document.getElementById('profile-email').textContent = data.email || 'Не указан';
        
        const toursList = document.getElementById('profile-tours');
        toursList.innerHTML = '';
        
        if (data.tours && data.tours.length > 0) {
            data.tours.forEach(tour => {
                const li = document.createElement('li');
                li.innerHTML = `
                    <strong>${tour.destination}</strong>
                    <span class="tour-status ${tour.status}">${tour.status || 'в обработке'}</span>
                `;
                toursList.appendChild(li);
            });
        } else {
            toursList.innerHTML = '<li>У вас нет оформленных туров</li>';
        }
        
    } catch (error) {
        console.error('Error loading profile:', error);
        showNotification(`Ошибка загрузки профиля: ${error.message}`, 'error');
        
        // Если ошибка авторизации, предлагаем войти
        if (error.message.includes('авторизоваться')) {
            setTimeout(() => openModal(loginModal), 2000);
        }
    }
}

document.getElementById('order-form')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    // Проверка авторизации
    if (!isLoggedIn()) {
        showNotification('Для оформления тура войдите в систему', 'warning');
        openModal(loginModal);
        return;
    }

    // Валидация дат
    const dateFrom = new Date(this.date_from.value);
    const dateTo = new Date(this.date_to.value);
    
    if (dateFrom >= dateTo) {
        showNotification('Дата возвращения должна быть позже даты отправления', 'error');
        return;
    }

    // Подготовка данных
    const formData = {
        destination: this.destination.value,
        date_from: this.date_from.value,
        date_to: this.date_to.value,
        people: parseInt(this.people.value),
        payment: this.payment.value
    };

    try {
        const response = await fetch('process_order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        });

        const result = await response.json();

        if (!response.ok || result.status !== 'success') {
            throw new Error(result.message || 'Ошибка при оформлении заказа');
        }

        showNotification(result.message, 'success');
        
        // Дополнительные действия после успешного оформления
        console.log('Order details:', {
            id: result.order_id,
            price: result.price
        });
        
        // Очистка формы
        this.reset();
        
        // Обновление списка туров в профиле
        if (typeof loadProfileData === 'function') {
            loadProfileData();
        }

    } catch (error) {
        console.error('Order error:', error);
        showNotification(error.message, 'error');
    }
});

// Функция для отображения уведомлений
function showNotification(message, type = 'info') {
    let container = document.getElementById('notifications-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'notifications-container';
        container.style.position = 'fixed';
        container.style.top = '20px';
        container.style.right = '20px';
        container.style.zIndex = '10000';
        document.body.appendChild(container);
    }

    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <div class="notification-content">${message}</div>
    `;

    container.insertBefore(notification, container.firstChild);

    const timer = setTimeout(() => {
        closeNotification(notification);
    }, 5000);

    notification._timer = timer;
}

function closeNotification(notification) {
    if (notification._timer) {
        clearTimeout(notification._timer);
    }
    notification.classList.add('fade-out');
    setTimeout(() => notification.remove(), 500);
}


});

document.addEventListener('DOMContentLoaded', () => {
    const slides = document.querySelector('.slides');
    const slideImages = document.querySelectorAll('.slides img');
    const prevBtn = document.querySelector('.prev');
    const nextBtn = document.querySelector('.next');

    let index = 1;
    const totalSlides = slideImages.length;

    function getSlideWidth() {
        return slideImages[0].clientWidth;
    }

    function setSlidePosition(noTransition = false) {
        if (noTransition) {
            slides.style.transition = 'none';
        } else {
            slides.style.transition = 'transform 1s ease-in-out';
        }
        slides.style.transform = `translateX(-${getSlideWidth() * index}px)`;
    }

    setSlidePosition(true); // Начальная позиция

    function goToNextSlide() {
        if (index >= totalSlides - 1) return;
        index++;
        setSlidePosition();
    }

    function goToPrevSlide() {
        if (index <= 0) return;
        index--;
        setSlidePosition();
    }

    function resetIfCloned() {
        if (slideImages[index].alt.includes('Clone First')) {
            index = 1;
            setTimeout(() => setSlidePosition(true), 50); // немного подождём, чтобы transition завершился
        } else if (slideImages[index].alt.includes('Clone Last')) {
            index = totalSlides - 2;
            setTimeout(() => setSlidePosition(true), 50);
        }
    }

    nextBtn.addEventListener('click', () => {
        goToNextSlide();
        resetAutoSlide();
    });

    prevBtn.addEventListener('click', () => {
        goToPrevSlide();
        resetAutoSlide();
    });

    slides.addEventListener('transitionend', resetIfCloned);

    let slideInterval = setInterval(() => {
        goToNextSlide();
    }, 5000);

    function resetAutoSlide() {
        clearInterval(slideInterval);
        slideInterval = setInterval(() => {
            goToNextSlide();
        }, 5000);
    }

    window.addEventListener('resize', () => {
        setSlidePosition(true);
    });
});

    






