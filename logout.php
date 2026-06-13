<?php
if (isset($_COOKIE['User'])) {
    setcookie('User', '', time() - 7200, '/');
}
header('Location: http://localhost/AibksWEBkate/FefuAIBKSWEB/index.php');
exit();
?>