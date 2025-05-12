<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ВокругСвета</title>
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


    <div class="container">
        <main>
            <div class="content-wrapper">
                <section class="hero">
                <div class="slider">
                    <div class="slides">
                        <img src="img/2.jpg" alt="Clone Last">
                        <img src="img/orig.webp" alt="Slide 1">
                        <img src="img/1.jpg" alt="Slide 2">
                        <img src="img/2.jpg" alt="Slide 3">
                        <img src="img/orig.webp" alt="Clone First">
                    </div>
                    <button class="prev">&#10094;</button>
                    <button class="next">&#10095;</button>
                </div>
                </section>

                <section class="tours">
                    <h2 class="zagolovok">Туры в разные страны</h2>
                    
                    <div class="tour">
                        <img src="img/japan.jpg" alt="Япония">
                        <div class="tour-info">
                            <h3>Тур в Японию</h3>
                            <p>Японская гармония: от древних храмов до современных мегаполисов</p>
                            <span class="price">99.999₽</span>
                        </div>
                    </div>

                    <div class="tour">
                        <img src="img/thailand.jpg" alt="Тайланд">
                        <div class="tour-info">
                            <h3>Тур в Тайланд</h3>
                            <p>Тайский рай: приключения на пляжах и в джунглях</p>
                            <span class="price">69.999₽</span>
                        </div>
                    </div>

                    <div class="tour">
                        <img src="img/italy.jpg" alt="Италия">
                        <div class="tour-info">
                            <h3>Тур в Италию</h3>
                            <p>Романтика Рима: следами древних империй</p>
                            <span class="price">199.900₽</span>
                        </div>
                    </div>

                    <div class="tour">
                        <img src="img/france.jpg" alt="Франция">
                        <div class="tour-info">
                            <h3>Тур во Францию</h3>
                            <p>Парижская мечта: романтика и искусство города огней</p>
                            <span class="price">176.500₽</span>
                        </div>
                    </div>
                </section>
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

    <script src="script.js"></script>
</body>
</html>
