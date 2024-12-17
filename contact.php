<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Контакты - Высшее Учебное Заведение</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('https://sitkodenis.ru/wp-content/uploads/2017/03/20170302.gif');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            color: #f0f0f0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        header {
            background: rgba(30, 30, 30, 0.9);
            color: #ffffff;
            padding: 20px 0;
            text-align: center;
            animation: fadeIn 1s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.7);
        }

        nav ul {
            list-style-type: none;
            padding: 0;
        }

        nav ul li {
            display: inline;
            margin: 0 15px;
        }

        nav ul li a {
            color: #ffffff;
            text-decoration: none;
            transition: color 0.3s, text-shadow 0.3s;
        }

        nav ul li a:hover {
            color: #ffcc00;
            text-shadow: 0 0 10px #ffcc00;
        }

        main {
            padding: 20px;
            max-width: 800px;
            margin: auto;
            background: rgba(50, 50, 50, 0.8);
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
            position: relative;
            z-index: 1;
            animation: slideIn 1s ease;
        }

        h1 {
            color: #ffcc00;
            border-bottom: 2px solid #ffcc00;
            padding-bottom: 5px;
            animation: grow 0.5s ease;
        }

        .contact-info {
            margin-top: 20px;
            padding: 15px;
            background: rgba(70, 70, 70, 0.9);
            border-radius: 5px;
            transition: transform 0.3s, background 0.3s;
        }

        .contact-info:hover {
            transform: scale(1.02);
            background: rgba(70, 70, 70, 1);
        }

        footer {
            text-align: center;
            padding: 10px 0;
            background: rgba(30, 30, 30, 0.9);
            color: #ffffff;
            position: relative;
            bottom: 0;
            width: 100%;
            animation: fadeIn 1s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideIn {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @keyframes grow {
            from { transform: scale(0.9); }
            to { transform: scale(1); }
        }
    </style>
</head>
<body>
    <header>
        <h1>Контакты нашего университета</h1>
        <nav>
            <ul>
                <li><a href="registration.php">Регистрация</a></li>
                <li><a href="login.php">Вход</a></li>
                <li><a href="index.php">Главная</a></li>
                <li><a href="courses.php">Курсы</a></li>
                <li><a href="contact.php">Контакты</a></li>
                <li><a href="logout.php">Выход</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h1>Свяжитесь с нами</h1>
        <div class="contact-info">
            <h2>Адрес:</h2>
            <p>г. Москва, ул. Академическая, д. 45</p>
            <h2>Телефон:</h2>
            <p>+7 (495) 123-45-67</p>
            <h2>E-mail:</h2>
            <p><a href="mailto:info@university.ru" style="color: #ffcc00;">info@university.ru</a></p>
            <h2>Сайт:</h2>
            <p><a href="http://www.university.ru" target="_blank" style="color: #ffcc00;">www.university.ru</a></p>
        </div>
    </main>
    <footer>
        <p>&copy; 2024 Высшее Учебное Заведение. Все права защищены.</p>
    </footer>
</body>
</html>
