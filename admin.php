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

// Функция для получения всех пользователей
function getAllUsers() {
    global $conn;
    $stmt = $conn->prepare("SELECT id, login, email, Role FROM users");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    return $users;
}

// Добавление пользователя
if (isset($_POST['add_user'])) {
    $login = $_POST['login'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    
    $stmt = $conn->prepare("INSERT INTO users (login, email, password, Role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $login, $email, $password, $role);
    $stmt->execute();
}

// Удаление пользователя
if (isset($_GET['delete_user'])) {
    $id = $_GET['delete_user'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

// Редактирование пользователя
if (isset($_POST['edit_user'])) {
    $id = $_POST['id'];
    $login = $_POST['login'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    
    $stmt = $conn->prepare("UPDATE users SET login = ?, email = ?, Role = ? WHERE id = ?");
    $stmt->bind_param("sssi", $login, $email, $role, $id);
    $stmt->execute();
}

// Получение пользователей
$users = getAllUsers();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админка - Управление пользователями</title>
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
        <h1>Управление пользователями</h1>

        <h2>Добавить пользователя</h2>
        <form method="POST">
            <div class="input-group">
                <input type="text" name="login" placeholder="Логин" required>
            </div>
            <div class="input-group">
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <div class="input-group">
                <input type="password" name="password" placeholder="Пароль" required>
            </div>
            <div class="input-group">
                <select name="role" required>
                    <option value="">Выберите роль</option>
                    <option value="admin">Администратор</option>
                    <option value="user">Пользователь</option>
                    <option value="teacher">Преподаватель</option> 
                    <option value="student">Студент</option> 
                </select>
            </div>
            <button type="submit" name="add_user" class="btn">Добавить</button>
        </form>

        <h2>Список пользователей</h2>
        <div class="search-results">
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                <tr>
                    <th style="padding: 10px; background: rgba(255, 255, 255, 0.2);">ID</th>
                    <th style="padding: 10px; background: rgba(255, 255, 255, 0.2);">Логин</th>
                    <th style="padding: 10px; background: rgba(255, 255, 255, 0.2);">Email</th>
                    <th style="padding: 10px; background: rgba(255, 255, 255, 0.2);">Роль</th>
                    <th style="padding: 10px; background: rgba(255, 255, 255, 0.2);">Действия</th>
                </tr>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td style="padding: 10px; background: rgba(255, 255, 255, 0.1);"><?php echo htmlspecialchars($user['id']); ?></td>
                        <td style="padding: 10px; background: rgba(255, 255, 255, 0.1);"><?php echo htmlspecialchars($user['login']); ?></td>
                        <td style="padding: 10px; background: rgba(255, 255, 255, 0.1);"><?php echo htmlspecialchars($user['email']); ?></td>
                        <td style="padding: 10px; background: rgba(255, 255, 255, 0.1);"><?php echo htmlspecialchars($user['Role']); ?></td>
                        <td style="padding: 10px; background: rgba(255, 255, 255, 0.1);">
                            <a href="?delete_user=<?php echo $user['id']; ?>" class="btn" style="background-color: #d9534f;">Удалить</a>
                            <button type="button" onclick="editUser  (<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['login']); ?>', '<?php echo htmlspecialchars($user['email']); ?>', '<?php echo htmlspecialchars($user['Role']); ?>')" class="btn">Редактировать</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <div id="editForm" style="display:none;">
            <h2>Редактировать пользователя</h2>
            <form method="POST" id="userEditForm">
                <input type="hidden" name="id" id="editUser Id">
                <div class="input-group">
                    <input type="text" name="login" id="editLogin" required>
                </div>
                <div class="input-group">
                    <input type="email" name="email" id="editEmail" required>
                </div>
                <div class="input-group">
                    <select name="role" id="editRole" required>
                        <option value="admin">Администратор</option>
                        <option value="user">Пользователь</option>
                        <option value="teacher">Преподаватель</option> <!-- Добавлено -->
                    </select>
                </div>
                <button type="submit" name="edit_user" class="btn">Сохранить</button>
                <button type="button" onclick="closeEditForm()" class="btn" style="background-color: #d9534f;">Отмена</button>
            </form>
        </div>
    </div>

    <script>
        function editUser  (id, login, email, role) {
            document.getElementById('editUser Id').value = id;
            document.getElementById('editLogin').value = login;
            document.getElementById('editEmail').value = email;
            document.getElementById('editRole').value = role;
            document.getElementById('editForm').style.display = 'block';
        }

        function closeEditForm() {
            document.getElementById('editForm').style.display = 'none';
        }
    </script>
</body>
</html>

<?php
// Закрытие соединения
$conn->close();
?>

