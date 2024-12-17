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

// Функции для работы с курсами
function addCourse($title, $description, $teacher_id) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO courses (title, description, teacher_id) VALUES (?, ?, ?)");
    
    if (!$stmt) {
        die("Ошибка подготовки запроса: " . $conn->error);
    }

    $stmt->bind_param("ssi", $title, $description, $teacher_id);
    
    if ($stmt->execute()) {
        return true;
    } else {
        echo "Ошибка добавления курса: " . $stmt->error;
        return false;
    }
}

function editCourse($course_id, $title, $description) {
    global $conn;
    $stmt = $conn->prepare("UPDATE courses SET title = ?, description = ? WHERE id = ?");
    $stmt->bind_param("ssi", $title, $description, $course_id);
    return $stmt->execute();
}

function deleteCourse($course_id) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM courses WHERE id = ?");
    $stmt->bind_param("i", $course_id);
    return $stmt->execute();
}

function getCourses() {
    global $conn;
    $result = $conn->query("SELECT * FROM courses");
    
    $courses = [];
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
    return $courses;
}

function searchCoursesByTitle($title) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM courses WHERE title LIKE ?");
    $searchTerm = "%" . $title . "%";
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $courses = [];
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
    return $courses;
}

function addGrade($student_id, $course_id, $grade, $semester) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO grades (student_id, course_id, grade, semester) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $student_id, $course_id, $grade, $semester);
    return $stmt->execute();
}

// Получение списка студентов
function getStudents() {
    global $conn;
    $result = $conn->query("SELECT id, full_name FROM students");
    
    $students = [];
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
    return $students;
}

// Обработка запросов
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_course'])) {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $teacher_id = $_POST['teacher_id']; // Получаем ID учителя из скрытого поля
        
        // Проверка на наличие данных
        if (!empty($title) && !empty($description)) {
            addCourse($title, $description, $teacher_id);
        } else {
            echo "<div class='error-message'>Пожалуйста, заполните все поля.</div>";
        }
    } elseif (isset($_POST['edit_course'])) {
        $course_id = $_POST['course_id'];
        $title = $_POST['title'];
        $description = $_POST['description'];
        editCourse($course_id, $title, $description);
    } elseif (isset($_POST['delete_course'])) {
        $course_id = $_POST['course_id'];
        deleteCourse($course_id);
    } elseif (isset($_POST['add_grade'])) {
        $student_id = $_POST['student_id']; // ID студента передается из выпадающего списка
        $course_id = $_POST['course_id'];
        $grade = $_POST['grade'];
        $semester = $_POST['semester'];
        addGrade($student_id, $course_id, $grade, $semester);
    }
}

// Получение курсов и студентов для отображения
$courses = getCourses();
$students = getStudents();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление курсами</title>
    <style>
        /* Ваши стили здесь */
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
            color: #fff; /* Цвет текста */
            overflow: hidden; /* Убираем прокрутку на фоне */
        }

        h1, h2 {
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.8); /* Тень для заголовков */
        }

        .container {
            background: rgba(0, 0, 0, 0.5); /* Темный фон для контейнера */
            backdrop-filter: blur(10px);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.5);
            width: 500px; /* Увеличение ширины контейнера */
            max-height: 90vh; /* Ограничение максимальной высоты */
            overflow-y: auto; /* Добавление прокрутки при необходимости */
            animation: fadeIn 1s;
        }

        .input-group input, .input-group textarea, .input-group select {
            background-color: rgba(255, 255, 255, 0.2); /* Полупрозрачный фон для полей ввода */
            color: #fff; /* Цвет текста в полях ввода */
        }

        .input-group input:focus, .input-group textarea:focus {
            box-shadow: 0 0 5px rgba(255, 255, 255, 0.8);
            background-color: rgba(255, 255, 255, 0.3); /* Изменение фона при фокусе */
        }

        .course-list {
            margin-top: 15px;
            text-align: left;
            max-height: 200px;
            overflow-y: auto;
        }

        .course-item {
            background: rgba(255, 255, 255, 0.2); /* Полупрозрачный фон для элементов курсов */
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
            color: #fff; /* Цвет текста для элементов курсов */
            transition: background-color 0.3s; /* Плавный переход фона */
        }

        .course-item:hover {
            background: rgba(255, 255, 255, 0.3); /* Увеличение контрастности при наведении */
        }

        .error-message {
            color: #ff4d4d; /* Цвет для сообщения об ошибке */
            margin-top: 5px;
            font-size: 12px;
        }

        /* Адаптивность */
        @media (max-width: 600px) {
            .container {
                width: 90%; /* Адаптивная ширина для маленьких экранов */
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Управление курсами</h1>

        <h2>Добавить курс</h2>
        <form method="POST">
            <div class="input-group">
                <input type="text" name="title" placeholder="Название курса" required>
            </div>
            <div class="input-group">
                <textarea name="description" placeholder="Описание курса" required></textarea>
            </div>
            <input type="hidden" name="teacher_id" value="1"> <!-- Скрытое поле для ID учителя -->
            <button type="submit" name="add_course" class="btn">Добавить</button>
        </form>

        <h2>Существующие курсы</h2>
        <div class="course-list">
            <?php foreach ($courses as $course): ?>
                <div class="course-item">
                    ID: <?= $course['id'] ?>, Название: <?= $course['title'] ?>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                        <button type="submit" name="delete_course" class="btn" style="font-size: 12px; padding: 5px;">Удалить</button>
                    </form>
                    <button onclick="document.getElementById('editForm<?= $course['id'] ?>').style.display='block'" class="btn" style="font-size: 12px; padding: 5px;">Редактировать</button>
                    <div id="editForm<?= $course['id'] ?>" style="display:none;">
                        <form method="POST">
                            <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                            <input type="text" name="title" value="<?= $course['title'] ?>" required>
                            <textarea name="description" required><?= $course['description'] ?></textarea>
                            <button type="submit" name="edit_course" class="btn" style="font-size: 12px; padding: 5px;">Сохранить</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <h2>Выставить оценку</h2>
        <form method="POST">
            <div class="input-group">
                <select name="student_id" required>
                    <option value="">Выберите студента</option>
                    <?php foreach ($students as $student): ?>
                        <option value="<?= $student['id'] ?>"><?= $student['full_name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="input-group">
                <select name="course_id" required>
                    <option value="">Выберите курс</option>
                    <?php foreach ($courses as $course): ?>
                        <option value="<?= $course['id'] ?>"><?= $course['title'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="input-group">
                <input type="number" name="grade" placeholder="Оценка" required>
            </div>
            <div class="input-group">
                <input type="text" name="semester" placeholder="Семестр" required>
            </div>
            <button type="submit" name="add_grade" class="btn">Добавить оценку</button>
        </form>

        <h2>Поиск курсов</h2>
        <form method="GET">
            <div class="input-group">
                <input type="text" name="search" placeholder="Поиск по названию">
            </div>
            <button type="submit" class="btn">Поиск</button>
        </form>
        <div class="search-results">
            <?php
            if (isset($_GET['search'])) {
                $search_results = searchCoursesByTitle($_GET['search']);
                echo "<h3>Результаты:</h3><ul>";
                foreach ($search_results as $course) {
                    echo "<li>ID: " . $course['id'] . ", Название: " . $course['title'] . "</li>";
                }
                echo "</ul>";
            }
            ?>
        </div>
    </div>

</body>
</html>

<?php
// Закрытие соединения
$conn->close();
?>

