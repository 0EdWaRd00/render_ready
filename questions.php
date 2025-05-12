<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Часто задаваемые вопросы</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="img/globe_10723716.png" type="image/png">
</head>
<body>
<header>
    <nav>
        <ul>
            <li><a href="index.php">Главная</a></li>
            <li><a href="questions.php">Частые вопросы</a></li>
            <li><a href="order.php" id="order-btn">Оформить тур</a></li>
        </ul>
    </nav>
    
    <?php if (isLoggedIn()): ?>
        <button id="profileButton">
            Вы вошли как <?= htmlspecialchars($_SESSION['username']) ?>
        </button>
    <?php else: ?>
        <button id="auth-btn">Вы вошли как гость</button>
    <?php endif; ?>
</header>

<!-- Модальные окна -->
<div id="register-modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Регистрация</h2>
        <form id="register-form">
            <input type="text" name="username" placeholder="Имя" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Пароль" required>
            <button type="submit">Зарегистрироваться</button>
        </form>
        <p class="auth-switch">Уже есть аккаунт? <a href="#" id="show-login">Войти</a></p>
    </div>
</div>

<div id="login-modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Вход</h2>
        <form id="login-form">
            <input type="text" name="username" placeholder="Логин" required>
            <input type="password" name="password" placeholder="Пароль" required>
            <button type="submit">Войти</button>
        </form>
        <p class="auth-switch">Нет аккаунта? <a href="#" id="show-register">Зарегистрироваться</a></p>
    </div>
</div>

<div class="container">
    <main>
        <h2 align="center">Часто задаваемые вопросы</h2>
        <div class="accordion">
            <div class="accordion-item">
                <button class="accordion-header">Как забронировать тур?</button>
                <div class="accordion-content">Вы можете выбрать тур на странице "Оформить тур" и нажать кнопку "Оформить заказ". Требуется предварительная авторизация на сайте.</div>
            </div>
            <div class="accordion-item">
                <button class="accordion-header">Какие способы оплаты доступны?</button>
                <div class="accordion-content">Мы принимаем оплату банковскими картами и наличными.</div>
            </div>
            <div class="accordion-item">
                <button class="accordion-header">Можно ли отменить бронирование?</button>
                <div class="accordion-content">Да, вы можете отменить бронирование за 48 часов до начала тура.</div>
            </div>
            <div class="accordion-item">
                <button class="accordion-header">Почему меня заблокировали на сайте?</button>
                <div class="accordion-content">Мы прибегаем к блокировке только в крайних случаях. Причина всегда описывается и вы можете ее узнать при повторной попытке зайти на сайт.</div>
            </div>
        </div>
    </main>

    <aside>
        <div class="advantages">
            <h3>Наши преимущества</h3>
            <ul>
                <li>🌍 Уникальные направления</li>
                <li>🎒 Экскурсионные туры</li>
                <li>✨ Специальные предложения</li>
                <li>🧳 Удобные трансферы</li>
            </ul>
        </div>

        <div class="news">
    <h3>Новости</h3>
    <?php
    require_once 'config.php'; // Подключение к БД

    // Получаем актуальную рекламу (если есть)
    $stmt = $pdo->query("SELECT * FROM ads WHERE end_date IS NULL OR end_date > NOW() ORDER BY start_date DESC LIMIT 3");
    $ads = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($ads)) {
        foreach ($ads as $ad) {
            echo '<div class="news-item">';
            if ($ad['image_url']) {
                echo '<a href="' . htmlspecialchars($ad['link']) . '" target="_blank">';
                echo '<img src="' . htmlspecialchars($ad['image_url']) . '" alt="' . htmlspecialchars($ad['title']) . '" style="max-width: 100%;">';
                echo '</a>';
            }
            echo htmlspecialchars($ad['title']);
            echo '</div>';
        }
    } else {
        echo '<div class="news-item">Новостей пока нет</div>';
    }
    ?>
</div>

        </aside>
    </div>
    </aside>
</div>

<!-- Модальное окно профиля -->
<div id="profileModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Профиль</h2>
        <p><strong>Логин:</strong> <span id="profile-username"></span></p>
        <p><strong>Email:</strong> <span id="profile-email"></span></p>
        <h3>Оформленные туры:</h3>
        <ul id="profile-tours"></ul>
        <button id="logoutBtn">Выйти</button>
    </div>
</div>

<footer>
    <p>&copy; 2025 ВокругСвета. Все права защищены.</p>
</footer>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const items = document.querySelectorAll('.accordion-item');

        items.forEach((item, index) => {
            const button = item.querySelector('.accordion-header');
            const content = item.querySelector('.accordion-content');

            if (index === 0) {
                content.style.maxHeight = content.scrollHeight + "px";
                content.style.padding = "15px";
                item.classList.add('active');
            }

            button.addEventListener('click', () => {
                if (item.classList.contains('active')) {
                    content.style.maxHeight = null;
                    content.style.padding = "0 15px";
                } else {
                    content.style.maxHeight = content.scrollHeight + "px";
                    content.style.padding = "15px";
                }

                item.classList.toggle('active');
            });
        });
    });
</script>


<style>
        .accordion {
            border-top: 1px solid #d2b48c;
        }
        .accordion-item {
            border-bottom: 1px solid #d2b48c;
        }
        .accordion-header {
            width: 100%;
            background: #deb887;
            color: #ffff;
            border: none;
            padding: 15px;
            text-align: left;
            font-size: 16px;
            cursor: pointer;
            outline: none;
            transition: background 0.3s;
        }
        .accordion-header:hover {
            background: #c19a6b;
        }
        .accordion-content {
            max-height: 0;
            overflow: hidden;
            padding: 0 15px;
            background: #faebd7;
            color: #5c4033;
            transition: max-height 0.3s ease-out, padding 0.3s ease-out;
        }
        .accordion-item.active .accordion-content {
            max-height: 200px;
            padding: 15px;
        }
    </style>
<script src="script.js"></script>

</body>
</html>