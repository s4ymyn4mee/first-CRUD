<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <title>PHP-вебсайт</title>
</head>
<body>
<nav class="py-2 bg-light border-bottom">
    <div class="container d-flex flex-wrap">
        <ul class="nav me-auto">
            <li class="nav-item"><a href="#" class="nav-link link-dark px-2 active" aria-current="page">Главная</a></li>
            <li class="nav-item"><a href="#" class="nav-link link-dark px-2">Обратная связь</a></li>
        </ul>
        <ul class="nav">
            <li class="nav-item"><a href="#" class="btn btn-outline-secondary me-2">Войти</a></li>
        </ul>
    </div>
</nav>
<div class="container mt-5">
    <h3>Список студентов:</h3>
    <h5 class="m-3">
        <?php
        $name = $surname = $age = '';
        $connection_data = file("connection_data.txt",
            FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?? [];
        [$servername, $db_login, $db_password, $db_name] = $connection_data;
        $connection = new mysqli($servername, $db_login, $db_password, $db_name);

        $sql_query = "SELECT * FROM `students`";
        $all_students = $connection->query($sql_query)->fetch_all(MYSQLI_ASSOC);
        $connection->close();

        if (count($all_students) > 0) {
            $last_id = count($all_students) - 1;
            $next_id = $all_students[$last_id]['id'] + 1;
        } else {
            $next_id = 1;
        }


        if (isset($_POST['add_student'])) {
            $name = $_POST['name'] ?? '';
            $surname = $_POST['surname'] ?? '';
            $age = $_POST['age'] ?? '';

            $connection = new mysqli($servername, $db_login, $db_password, $db_name);
            $sql_query = "INSERT INTO `students`(`id`,`name`,`surname`,`age`) 
                          VALUES ('$next_id','$name','$surname','$age')";
            $connection->query($sql_query);
            $connection->close();

            header("Location:" . $_SERVER['PHP_SELF']);
            exit;
        }

        if (isset($_GET['edit'])) {
            $record_number = $_GET['edit'];
            $name = $all_students[$record_number]['name'] ?? '';
            $surname = $all_students[$record_number]['surname'] ?? '';
            $age = $all_students[$record_number]['age'] ?? '';
            $id = $all_students[$record_number]['id'] ?? '';

        }

        if (isset($_POST['save_student'])) {
            if ($name != $_POST['name'] or $surname != $_POST['surname'] or $age != $_POST['age']) {
                $name = $_POST['name'] ?? '';
                $surname = $_POST['surname'] ?? '';
                $age = $_POST['age'] ?? 0;

                $connection = new mysqli($servername, $db_login, $db_password, $db_name);
                $sql_query = "UPDATE `students`
                          SET `name` = '$name',`surname` = '$surname',`age` = '$age' 
                          WHERE `id` = '$id'";
                $connection->query($sql_query);
                $connection->close();
            }

            header("Location:" . $_SERVER['PHP_SELF']);
            exit;
        }

        if (isset($_POST['delete_student'])) {
            $connection = new mysqli($servername, $db_login, $db_password, $db_name);
            $sql_query = "DELETE FROM `students` WHERE `id` = '$id'";
            $connection->query($sql_query);
            $connection->close();

            header("Location:" . $_SERVER['PHP_SELF']);
            exit;
        }

        ?>
        <form action="#" method="POST">
            <div class="row mb-4">
                <div class="col-2">
                    <input type="text" name="name" value="<?= $name ?>" class="form-control" placeholder="Имя">
                </div>
                <div class="col-2">
                    <input type="text" name="surname" value="<?= $surname ?>" class="form-control"
                           placeholder="Фамилия">
                </div>
                <div class="col-2">
                    <input type="number" name="age" value="<?= $age ?>" class="form-control" placeholder="Возраст">
                </div>
                <?php if (!isset($_GET['edit'])): ?>
                    <div class="col-auto">
                        <button name="add_student" class="btn btn-outline-primary">Добавить студента</button>
                    </div>
                <?php endif; ?>
                <?php if (isset($_GET['edit'])): ?>
                    <div class="col-auto d-grid gap-1 d-md-block">
                        <button name="save_student" class="btn btn-outline-primary">Сохранить</button>
                        <button name="delete_student" class="btn btn-outline-danger">Удалить</button>
                    </div>
                <?php endif; ?>
            </div>

        </form>

        <?php
        foreach ($all_students as $key => $record) {
            print_r("<p style='display: inline;'>
                    {$record['id']} |
                    {$record['name']} |
                    {$record['surname']} | 
                    {$record['age']}&emsp;
                    <form method='GET' style='display: inline;'>
                    <input type='hidden' name='edit' value='$key'>
                    <input type='submit' class='btn btn-primary' value='Изменить'>
                    </form>
                    </p>");
        }

        ?>
    </h5>
    <h3 class="mb-3 mt-4">До зимней сессии осталось:
        <?php
        const offset = 5;
        $current_time = time() + offset * 3600;
        $current_year = (int)date("Y", $current_time);
        $next_year = $current_year + 1;
        $new_year_time = strtotime("9 January $next_year");
        $remain_time = $new_year_time - $current_time;

        $days_remain = intdiv($remain_time, 60 * 60 * 24);
        $remain_time %= (60 * 60 * 24);

        $hours_remain = intdiv($remain_time, 60 * 60);
        $remain_time %= (60 * 60);

        $minutes_remain = intdiv($remain_time, 60);
        $remain_time %= 60;

        $seconds_remain = $remain_time;

        print_r("{$days_remain} дней
                               {$hours_remain} часов
                               {$minutes_remain} минут
                               {$seconds_remain} секунд (GMT+5)");
        ?>
    </h3>
    <h3 class="mb-3 mt-4">Народные мудрости:</h3>
    <div class="col-sm-6 col-lg-4 mb-4 mt-1" style="position: absolute">
        <div class="card text-bg-primary text-center p-3 mb-3">
            <figure class="mb-0">
                <blockquote class="blockquote mb-4">
                    <p>Слово — не воробей. Вообще ничто не воробей, кроме самого воробья.</p>
                </blockquote>
                <figcaption class="blockquote-footer mb-0 text-white">
                    Джейсон Стэтхем.
                </figcaption>
            </figure>
        </div>

        <div class="card text-center p-4">
            <figure class="mb-0">
                <blockquote class="blockquote">
                    <p>Если тебе где-то не рады в рваных носках, то и в целых туда идти не стоит.</p>
                </blockquote>
                <figcaption class="blockquote-footer mb-0 text-body-secondary">
                    Дж. Стейтем.
                </figcaption>
            </figure>
        </div>

    </div>
</div>

</body>
</html>