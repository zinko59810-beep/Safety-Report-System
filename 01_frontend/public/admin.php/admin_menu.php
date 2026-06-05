<?php
session_start();

// header("Cache-Control: no-cache, no-store, must-revalidate");
// header("Pragma: no-cache");
// header("Expires: 0");

if (
    !isset($_SESSION['user_id'])
    || $_SESSION['role'] != 1
) {
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<!-- 管理者用メニュー画面 -->
<html lang='ja'>

<head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <title>管理者用メニュー</title>
    <link rel='stylesheet' href='../../styles/style.css'>
    <link rel='stylesheet' href='../../styles/reset.css'>
    <link rel='stylesheet' href='../../styles/admin_menu.css'>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
    <header class="d-flex justify-content-between align-items-center px-4 py-2 shadow">
        <a href="#" class="text-white text-decoration-none fs-2">災害安否報告システム</a>
        <a href="../logout.php" class="btn btn-outline-dark btn-sm">ログアウト</a>
    </header>
    
   <main class="container">
        <section>
            <div class="d-flex justify-content-center my-5">
                <h1 class="page-title mt-5 mb-5 text-center">管理者用メニュー画面</h1>
            </div>
        </section>
        
        <!-- button -->
        <section class="d-flex justify-content-center mt-5" >
            <!-- pc/phone用 画面 -->
            <div class="d-flex flex-column flex-md-row gap-4">

                <a href="../safety_php/safety_list.php"
                class="menu-btn btn btn-primary shadow fs-5 w-100 w-md-auto"><i class="bi bi-pencil-square me-2"></i>
                安否登録画面
                </a>

                <a href="../admin.php/employee_list.php"
                class="menu-btn btn btn-primary shadow fs-5 w-100"><i class="bi bi-people me-2"></i>
                社員一覧画面
                </a>

                <a href="../admin.php/safety_delete_list.php"
                class="menu-btn btn btn-danger shadow fs-5 w-100"><i class="bi bi-trash me-2"></i>
                安否情報削除画面
                </a>
            </div>
            <!-- button -->
            </div>
        </section>
        
    </main>
</body> 

</html>