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

// Функция для получения оценок
function getGrades($student_id) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT c.name, g.grade, g.semester
        FROM grades g
        JOIN courses c ON g.course_id = c.id
        WHERE g.student_id = ?
    ");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $grades = [];
    while ($row = $result->fetch_assoc()) {
        $grades[] = $row;
    }
    return $grades;
}

// Функция для аутентификации студента
function authenticateStudent($login, $password) {
    global $conn;
    $stmt = $conn->prepare("SELECT id, password, courses_id FROM students WHERE (login = ? OR email = ?) AND role = 'student'");
    $stmt->bind_param("ss", $login, $login);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return null; // Пользователь не найден
    }
    
    $user = $result->fetch_assoc();
    
    // Проверка пароля
    if (password_verify($password, $user['password'])) {
        return $user; // Возвращаем данные пользователя
    } else {
        return null; // Неверный пароль
    }
}

// Проверка входа
$student_data = null;
if (isset($_POST['login'])) {
    $login = $_POST['login'];
    $password = $_POST['password'];
    $student_data = authenticateStudent($login, $password);
}

// Получение оценок, если студент аутентифицирован
$grades = $student_data ? getGrades($student_data['id']) : [];

// Получение доступных курсов для записи
$courses_result = $conn->query("SELECT * FROM courses");
$courses = $courses_result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Студент - Портал</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #1c1c1c, #3a3a3a);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            color: #fff;
        }

        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.5);
            width: 500px;
            text-align: center;
            animation: fadeIn 1s;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        h1 {
            margin-bottom: 20px;
            font-size: 24px;
            text-shadow: 0 0 5px rgba(255, 255, 255, 0.7);
        }

        .input-group {
            margin-bottom: 15px;
        }

        input, textarea {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            margin-top: 5px;
            outline: none;
            transition: all 0.3s ease;
        }

        input:focus, textarea:focus {
            box-shadow: 0 0 5px rgba(255, 255, 255, 0.8);
            background-color: rgba(255, 255, 255, 0.2);
        }

        .btn {
            width: 100%;
            padding: 10px;
            background-color: #5cb85c;
            border: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-size: 16px;
            margin-top: 10px;
        }

        .btn:hover {
            background-color: #4cae4c;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .course-item {
            margin: 10px 0;
            padding: 10px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            text-align: left;
        }

        .course-item button {
            margin-right: 10px;
        }

        .search-results {
            margin-top: 20px;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Добро пожаловать на студенческий портал!</h1>

        <?php if (!$student_data): ?>
            <h2>Вход для студентов</h2>
            <form method="POST">
                <div class="input-group">
                    <input type="text" name="login" placeholder="Логин или Email" required>
                </div>
                <div class="input-group">
                    <input type="password" name="password" placeholder="Пароль" required>
                </div>
                <button type="submit" class="btn">Войти</button>
            </form>
        <?php else: ?>
            <h2>Оценки</h2>
            <table>
                <tr>
                    <th>Курс</th>
                    <th>Оценка</th>
                    <th>Семестр</th>
                </tr>
                <?php foreach ($grades as $grade): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($grade['name']); ?></td>
                        <td><?php echo htmlspecialchars($grade['grade']); ?></td>
                        <td><?php echo htmlspecialchars($grade['semester']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <h2>Информация о курсе</h2>
            <p>Вы обучаетесь на курсе: 
                <?php 
                // Получение названия курса по course_id
                if ($student_data['courses_id']) {
                    $course_stmt = $conn->prepare("SELECT name FROM courses WHERE id = ?");
                    $course_stmt->bind_param("i", $student_data['courses_id']);
                    $course_stmt->execute();
                    $course_result = $course_stmt->get_result();
                    $course = $course_result->fetch_assoc();
                    echo htmlspecialchars($course['name']);
                } else {
                    echo "Не записан ни на один курс.";
                }
                ?>
            </p>

            <div class="enroll-form">
                <h2>Запись на курс</h2>
                <form method="POST">
                    <input type="hidden" name="student_id" value="<?php echo $student_data['id']; ?>">
                    <select name="courses_id" required>
                        <option value="">Выберите курс</option>
                        <?php foreach ($courses as $course): ?>
                            <option value="<?php echo $course['id']; ?>"><?php echo htmlspecialchars($course['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" name="enroll_course" class="btn">Записаться</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
// Закрытие соединения
$conn->close();
?>

