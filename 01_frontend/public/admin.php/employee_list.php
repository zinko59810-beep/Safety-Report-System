<?php
session_start();
require_once('../db.php');

$dbh = db_connect();

//管理者のみ使える
if (
    !isset($_SESSION['user_id'])
    || $_SESSION['role'] != 1
) {
    header("Location: ../login.php");
    exit;
}

$stmt = $dbh->query("
    SELECT
        e.emp_id,
        e.emp_name,
        e.emp_position,
        d.dept_name
    FROM employee e

    LEFT JOIN department d
        ON e.dept_id = d.dept_id

    WHERE e.emp_role = 1

    ORDER BY e.emp_role DESC
");

$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<!-- 社員一覧画面 -->
<html lang="ja">
 
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>社員一覧</title>
    <link rel="stylesheet" href="../../styles/reset.css">
    <link rel="stylesheet" href="../../styles/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
 
 
</head>
 
<body>
    <header class="d-flex justify-content-between align-items-center px-4 py-2 shadow">
        <a href="#" class="text-white text-decoration-none fs-2">災害安否報告システム</a>
        <a href="../login.php" class="btn btn-outline-dark btn-sm">ログアウト</a>
    </header>
 
    <main class="main ">
                <section class="">
                    <div class="detail-header text-start mt-4">
                        <h1 class="page-title text-center mb-4">社員一覧</h1>
                        <div class="text-end mb-3">
                    </div>
                
                </section>
                
                
 
             <!-------------- employee テーブル ------------------------------->
                <section class="detail-list container shadow p-5">
                    <!-- <div class="text-end mb-3">
                        <a href="#" class="btn btn-outline-primary" role="button">絞り込み</a>
                    </div> -->
                    <table class=" table p-4 table-bordered table-hover table-striped text-center shadow fs-5">
                    
                    <thead class="text-center  table-light">
                        <tr>
                            <th>社員番号</th>
                            <th>所属部署</th>
                            <th>名前</th>
                            <th>役職</th>
                            <th>詳細</th>
                        </tr>
                    </thead>
               
                <tbody>
                    <?php foreach($employees as $emp){ ?>
                        <tr>

                            <td><?= h($emp["emp_id"]) ?></td>

                            <td><?= h($emp["dept_name"]) ?></td>

                            <td><?= h($emp["emp_name"]) ?></td>

                            <td><?= h($emp["emp_position"]) ?></td>

                            <td>
                                <a href="./employee_detail.php?id=<?= h($emp["emp_id"]) ?>"
                                class="btn btn-primary btn-sm"> 詳細</a>
                            </td>

                        </tr>
                    <?php } ?>
                </tbody>
 
            </table>
           </div>
            <!-------------- employee テーブル ------------------------------->
 
            <!-------------- 戻るボタン -------------------------------------->
            <div class="text-end  mb-1 mt-2" >
            <a href="admin_menu.php" class="btn btn-dark">戻る</a>
            </div>
        </section>
    </main>
</body>
 
</html>