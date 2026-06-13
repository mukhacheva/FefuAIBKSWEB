<?php
if (!isset($_COOKIE['User'])) {
    header('Location: http://localhost/AibksWEBkate/FefuAIBKSWEB/login.php');
    exit();
}

require_once('db.php');
$link = mysqli_connect('127.0.0.1', 'root', '', 'first');

$upload_message = "";

if (isset($_POST['submit'])) {
    $title = $_POST['postTitle'];
    $main_text = $_POST['postContent'];
    
    if (!$title || !$main_text) die("no data post");
    
    $image_name = "";
    
    // Обработка загрузки файла
    if (!empty($_FILES["file"]["name"])) {
        $file_tmp = $_FILES["file"]["tmp_name"];
        $file_name = $_FILES["file"]["name"];
        $file_size = $_FILES["file"]["size"];
        
        // Разрешенные расширения (только картинки)
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        if (in_array($file_extension, $allowed_extensions) && $file_size < 2097152) {
            if (!is_dir('upload')) {
                mkdir('upload', 0777, true);
            }
            
            $safe_name = time() . "_" . $file_name;
            
            if (move_uploaded_file($file_tmp, "upload/" . $safe_name)) {
                $image_name = $safe_name;
                $upload_message = "Load in: upload/" . $safe_name;
            } else {
                $upload_message = "Failed to move uploaded file!";
            }
        } else {
            $upload_message = "upload failed! Allowed: jpg, jpeg, png, gif, webp. Max size: 2MB";
        }
    }
    
    // БЕЗ ЭКРАНИРОВАНИЯ - ДЛЯ SQL ИНЪЕКЦИЙ
    $sql = "INSERT INTO posts (title, main_text, image) VALUES ('$title', '$main_text', '$image_name')";
    if (!mysqli_query($link, $sql)) {
        die("error insert data post: " . mysqli_error($link));
    } else {
        if ($upload_message) {
            echo '<div class="alert alert-info">' . $upload_message . '</div>';
        }
        echo '<div class="alert alert-success">Post saved successfully!</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="http://localhost/AibksWEBkate/FefuAIBKSWEB/css/style.css">
    <title>Мухачева Екатерина Петровна</title>
</head>
<body>
    <div class="navbar navbar-dark bg-dark p-3">
        <div class="container-fluid">
            <a href="#" class="navbar-brand d-flex align-items-center">
                <img src="http://localhost/AibksWEBkate/FefuAIBKSWEB/logo.jpg" alt="логотип-сайта" class="me-2">
                <span class="text-light">History</span>
            </a>
            <?php if (isset($_COOKIE['User'])): ?>
                <form action="http://localhost/AibksWEBkate/FefuAIBKSWEB/logout.php" method="POST" class="d-flex">
                    <button class="btn btn-outline-danger" type="submit">Logout</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <div class="container mt-5">
        <div class="story-container">
            <div class="story-text">
                <p>In a world where every morning begins with a frog choir, and at night fireflies dance over the swamp — your story is born. Welcome home, little froggy. Here you will be understood, and they will pour you fresh dew.</p>
            </div>
            <img src="http://localhost/AibksWEBkate/FefuAIBKSWEB/frog.webp" alt="фото лягушки" class="hacker-img">
        </div>

        <div class="text-center mt-4">
            <button id="toggleButton" class="btn btn-primary">Open</button>
        </div>

        <div id="extraImage" class="mt-3 text-center" style="display: none;">
            <img class="hacker-img" src="http://localhost/AibksWEBkate/FefuAIBKSWEB/frog2.jpg" alt="скрытое фото">
        </div>

        <div class="mt-5">
            <h2 class="text-center mb-4">Add New Post 
                <?php 
                    $username1 = $_COOKIE['User']; 
                    echo $username1;
                ?>
            </h2>
            <form action="profile.php" id="postForm" class="d-flex flex-column gap-3" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="form-label" for="postTitle">Post Title</label>
                    <input type="text" name="postTitle" class="form-control hacker-input" id="postTitle" placeholder="Enter post Title" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="postContent">Post Content</label>
                    <textarea name="postContent" class="form-control hacker-input" id="postContent" placeholder="Enter post Content" rows="5" required></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label" for="file">Upload image (JPG, PNG, GIF, WEBP only, max 2MB)</label>
                    <input type="file" name="file" class="form-control hacker-input" id="file" accept="image/*">
                </div>
                <button class="btn btn-primary" type="submit" name="submit">Save Post</button>
            </form>
        </div>

        <div class="mt-5">
            <h2 class="text-center mb-4">Posts List</h2>
            <?php
            $sql = "SELECT * FROM posts ORDER BY id DESC";
            $res = mysqli_query($link, $sql);
            
            if (mysqli_num_rows($res) > 0) {
                while ($post = mysqli_fetch_array($res)) {
                    echo "<div class='card mb-3'>";
                    echo "<div class='card-body'>";
                    if ($post['image']) {
                        echo "<img src='http://localhost/AibksWEBkate/FefuAIBKSWEB/upload/" . $post['image'] . "' class='img-fluid mb-2' style='max-width: 200px;'>";
                    }
                    echo "<a href='http://localhost/AibksWEBkate/FefuAIBKSWEB/post.php?id=" . $post["id"] . "'>";
                    echo "<h5 class='card-title'>" . $post['title'] . "</h5>";
                    echo "</a>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<p class='text-center'>No posts yet</p>";
            }
            ?>
        </div>
    </div>
    <script src="http://localhost/AibksWEBkate/FefuAIBKSWEB/js/script.js"></script>
</body>
</html>