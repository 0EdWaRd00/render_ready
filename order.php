<?php
require_once 'config.php';
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Оформление тура</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="img/globe_10723716.png" type="image/png">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="index.php">Главная</a></li>
                <li><a href="questions.php">Частые вопросы</a></li>
                <li><a href="order.php">Оформить тур</a></li>
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
    
    <!-- Модальное окно регистрации -->
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

    <!-- Модальное окно входа -->
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
        
        <div class="container2">
    <main>
    <h2 align="center">Оформление тура</h2>
        <form id="order-form">
            <label for="destination">Выберите страну:</label>
            <select id="destination" name="destination" required>
                <option value="Италия">Италия</option>
                <option value="Франция">Франция</option>
                <option value="Япония">Япония</option>
                <option value="Тайланд">Тайланд</option>
            </select>

            <label for="date_from">Дата отправления:</label>
            <input type="date" id="date_from" name="date_from" required>

            <label for="date_to">Дата возвращения:</label>
            <input type="date" id="date_to" name="date_to" required>

            <label for="people">Количество человек:</label>
            <input type="number" id="people" name="people" min="1" required>

            <label for="payment">Способ оплаты:</label>
            <select id="payment" name="payment" required>
                <option value="Карта">Банковская карта</option>
                <option value="Наличные">Наличные</option>
            </select>

            <button type="submit">Оформить заказ</button>
        </form>
    </main>

        <div>
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
</div>

<footer>
    <p>&copy; 2025 ВокругСвета. Все права защищены.</p>
</footer>

    <script src="script.js"></script>
</body>
</html>
