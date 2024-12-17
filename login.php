<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизация</title>
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
            width: 300px;
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

        input {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            margin-top: 5px;
            outline: none;
            transition: all 0.3s ease;
        }

        input:focus {
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

        .guest-btn {
            background-color: #007bff; /* Цвет кнопки для входа как гость */
            margin-top: 10px;
        }

        .guest-btn:hover {
            background-color: #0056b3; /* Цвет кнопки при наведении */
        }

        .register-link {
            margin-top: 10px;
            font-size: 14px;
        }

        .error-message {
            color: red;
            margin-top: 10px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="https://belovokyzgty.ru/">
            <img src="https://kuzstu.ru/application/frontend/skin/default/assets/images/emblem.png" alt="Emblem" style="display: block; margin: 0 auto 20px; width: 100px;">
        </a>
        <form method="POST">
            <h1>Войти</h1>
            <div class="input-group">
                <input type="text" name="login" placeholder="Логин" required>
            </div>
            <div class="input-group">
                <input type="password" name="password" placeholder="Пароль" required>
            </div>
            <button type="submit" class="btn">Войти</button>
            <button type="button" class="btn guest-btn" onclick="window.location.href='index.php';">Войти как гость</button>
            <div class="register-link">
                <p>Нет аккаунта? <a href="register.php" style="color: #5cb85c;">Регистрация</a></p>
            </div>
            <div class="error-message">
                <?php 
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        $login = $_POST['login'];
                        $password = $_POST['password'];

                        $server = "134.90.167.42:10306";
                        $user = "Ivannikov"; 
                        $pw = "tJq4ri";   
                        $db = "project_Ivannikov";

                        $connect = mysqli_connect($server, $user, $pw, $db);
                        if (!$connect) {
                            die("Ошибка подключения: " . mysqli_connect_error());
                        }

                        $query = "SELECT * FROM users WHERE login='$login'";
                        $result = mysqli_query($connect, $query);

                        if ($result && mysqli_num_rows($result) > 0) {
                            $user_data = mysqli_fetch_assoc($result);
                            if (password_verify($password, $user_data['password'])) {
                                // Успешный вход
                                switch ($user_data['Role']) {
                                    case 'admin':
                                        header("Location: panel.php");
                                        break;
                                        
                                    case 'user':
                                        header("Location: index.php");
                                        break;
                                    case 'teacher':
                                        header("Location: prepod.php");
                                        break;
                                    case 'student':
                                        header("Location: index.php");
                                        break;
                                }
                                exit();
                            } else {
                                echo "<p>Неверный пароль.</p>";
                            }
                        } else {
                            echo "<p>Пользователь не найден.</p>";
                        }
                        mysqli_close($connect);
                    }
                ?>
            </div>
        </form>
    </div>
</body>
</html>

