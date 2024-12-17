<?php
$server = "134.90.167.42:10306";
$user = "Ivannikov"; 
$pw = "tJq4ri";   
$db = "project_Ivannikov";

// Подключение к базе данных
$conn = new mysqli($server, $user, $pw, $db);

// Проверка подключения
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Функция для получения всех курсов
function getAllCourses() {
    global $conn;
    $stmt = $conn->prepare("SELECT title, description FROM courses");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $courses = [];
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
    return $courses;
}

// Получение курсов
$courses = getAllCourses();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Курсы - Высшее Учебное Заведение</title>
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

        .course-list {
            margin-top: 20px;
        }

        .course-item {
            background: rgba(70, 70, 70, 0.9);
            margin: 10px 0;
            padding: 15px;
            border-radius: 5px;
            transition: transform 0.3s, background 0.3s;
        }

        .course-item:hover {
            transform: scale(1.02);
            background: rgba(70, 70, 70, 1);
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
        <h1>Курсы нашего университета</h1>
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
        <h1>Доступные курсы</h1>
        <div class="course-list">
            <?php if (empty($courses)): ?>
                <p>Курсы не найдены.</p>
            <?php else: ?>
                <?php foreach ($courses as $course): ?>
                    <div class="course-item">
                        <h2><?php echo htmlspecialchars($course['title']); ?></h2>
                        <p><?php echo htmlspecialchars($course['description']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
    <footer>
        <p>&copy; 2024 Высшее Учебное Заведение.</p>
    </footer>
</body>
</html>

<?php
// Закрытие соединения
$conn->close();
?>

