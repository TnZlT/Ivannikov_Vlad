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

// Функция для получения всех студентов
function getAllStudents() {
    global $conn;
    $stmt = $conn->prepare("SELECT s.id, s.login, s.email, s.full_name, c.title AS course_title 
                             FROM students s 
                             LEFT JOIN courses c ON s.courses_id = c.id");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $students = [];
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
    return $students;
}

// Добавление студента
if (isset($_POST['add_student'])) {
    $login = $_POST['login'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $full_name = $_POST['full_name'];
    $courses_id = $_POST['courses_id'];
    
    $stmt = $conn->prepare("INSERT INTO students (login, password, email, full_name, courses_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $login, $password, $email, $full_name, $courses_id);
    $stmt->execute();
}

// Удаление студента
if (isset($_GET['delete_student'])) {
    $id = $_GET['delete_student'];
    $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

// Редактирование студента
if (isset($_POST['edit_student'])) {
    $id = $_POST['id'];
    $login = $_POST['login'];
    $email = $_POST['email'];
    $full_name = $_POST['full_name'];
    $courses_id = $_POST['courses_id'];
    
    $stmt = $conn->prepare("UPDATE students SET login = ?, email = ?, full_name = ?, courses_id = ? WHERE id = ?");
    $stmt->bind_param("sssii", $login, $email, $full_name, $courses_id, $id);
    $stmt->execute();
}

// Получение студентов
$students = getAllStudents();

// Получение курсов для выпадающего списка
function getCourses() {
    global $conn;
    $stmt = $conn->prepare("SELECT id, title FROM courses");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $courses = [];
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
    return $courses;
}

$courses = getCourses();

// Проверка, нужно ли редактировать студента
$studentToEdit = null;
if (isset($_GET['edit_student'])) {
    $id = $_GET['edit_student'];
    $stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $studentToEdit = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админка - Управление студентами</title>
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

        input, select {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            margin-top: 5px;
            outline: none;
            transition: all 0.3s ease;
        }

        input:focus, select:focus {
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

        .student-item {
            margin: 10px 0;
            padding: 10px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            text-align: left;
        }

        .student-item button {
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
        <h1>Управление студентами</h1>

        <h2><?php echo $studentToEdit ? 'Редактировать студента' : 'Добавить студента'; ?></h2>
        <form method="POST">
            <?php if ($studentToEdit): ?>
                <input type="hidden" name="id" value="<?php echo $studentToEdit['id']; ?>">
            <?php endif; ?>
            <div class="input-group">
                <input type="text" name="login" placeholder="Логин" required value="<?php echo $studentToEdit ? htmlspecialchars($studentToEdit['login']) : ''; ?>">
            </div>
            <div class="input-group">
                <input type="email" name="email" placeholder="Email" required value="<?php echo $studentToEdit ? htmlspecialchars($studentToEdit['email']) : ''; ?>">
            </div>
            <div class="input-group">
                <input type="password" name="password" placeholder="Пароль" <?php echo $studentToEdit ? '': 'required'; ?>>
            </div>
            <div class="input-group">
                <input type="text" name="full_name" placeholder="ФИО" required value="<?php echo $studentToEdit ? htmlspecialchars($studentToEdit['full_name']) : ''; ?>">
            </div>
            <div class="input-group">
                <select name="courses_id" required>
                    <option value="">Выберите курс</option>
                    <?php foreach ($courses as $course): ?>
                        <option value="<?php echo $course['id']; ?>" <?php echo $studentToEdit && $studentToEdit['courses_id'] == $course['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($course['title']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" name="<?php echo $studentToEdit ? 'edit_student' : 'add_student'; ?>" class="btn">
                <?php echo $studentToEdit ? 'Сохранить' : 'Добавить'; ?>
            </button>
        </form>

        <h2>Список студентов</h2>
        <div class="search-results">
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                <tr>
                    <th style="padding: 10px; background: rgba(255, 255, 255, 0.2);">ID</th>
                    <th style="padding: 10px; background: rgba(255, 255, 255, 0.2);">Логин</th>
                    <th style="padding: 10px; background: rgba(255, 255, 255, 0.2);">Email</th>
                    <th style="padding: 10px; background: rgba(255, 255, 255, 0.2);">ФИО</th>
                    <th style="padding: 10px; background: rgba(255, 255, 255, 0.2);">Курс</th>
                    <th style="padding: 10px; background: rgba(255, 255, 255, 0.2);">Действия</th>
                </tr>
                <?php foreach ($students as $student): ?>
                    <tr>
                        <td style="padding: 10px; background: rgba(255, 255, 255, 0.1);"><?php echo htmlspecialchars($student['id']); ?></td>
                        <td style="padding: 10px; background: rgba(255, 255, 255, 0.1);"><?php echo htmlspecialchars($student['login']); ?></td>
                        <td style="padding: 10px; background: rgba(255, 255, 255, 0.1);"><?php echo htmlspecialchars($student['email']); ?></td>
                        <td style="padding: 10px; background: rgba(255, 255, 255, 0.1);"><?php echo htmlspecialchars($student['full_name']); ?></td>
                        <td style="padding: 10px; background: rgba(255, 255, 255, 0.1);"><?php echo htmlspecialchars($student['course_title']); ?></td>
                        <td style="padding: 10px; background: rgba(255, 255, 255, 0.1);">
                            <a href="?delete_student=<?php echo $student['id']; ?>" class="btn" style="background-color: #d9534f;">Удалить</a>
                            <a href="?edit_student=<?php echo $student['id']; ?>" class="btn">Редактировать</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</body>
</html>

<?php
// Закрытие соединения
$conn->close();
?>

