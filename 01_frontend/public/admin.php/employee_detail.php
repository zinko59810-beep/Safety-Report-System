<?php
session_start();
require_once('../db.php');

if (
    !isset($_SESSION['user_id'])
    || $_SESSION['role'] != 1
) {
    header("Location: ../login.php");
    exit;
}

$dbh = db_connect();

$id = $_GET["id"] ?? "";

if ($id === "") {
    exit("IDがありません");
}

$stmt = $dbh->prepare("
    SELECT
        e.emp_id,
        e.emp_name,
        e.emp_tel,
        e.emp_position,
        d.dept_name
    FROM employee e

    LEFT JOIN department d
        ON e.dept_id = d.dept_id

    WHERE e.emp_id = ?
");

$stmt->execute([$id]);

$employee = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$employee) {
    exit("データがありません");
}
?>
<!DOCTYPE html>
<!-- 社員詳細画面 -->
<html lang='ja'>

<head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <title>社員詳細</title>
    <link rel='stylesheet' href='../../styles/style.css'>
    <link rel='stylesheet' href='../../styles/reset.css'>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">

</head>

<body>
    <header class="d-flex justify-content-between align-items-center px-4 py-2 shadow">
        <a href="#" class="text-white text-decoration-none fs-2">災害安否報告システム</a>
        <a href="../logout.php" class="btn btn-outline-dark btn-sm">ログアウト</a>
    </header>
   
    <main class="main ">
        
            <div class="detail-header text-start mt-4">
                <h1 class="page-title mt-5 mb-5 text-center ">社員詳細画面</h1>
            </div>

    <!------------------------ テーブル ---------------------------------->
    <!-- databaseから取り出す -->
  

    <section class="detail-list container shadow p-5">
                <div class="">
                  <div class="">
                      <div class="table-responsive">
                        <table class=" table p-4 table-bordered table-hover table-striped text-center fs-4">
                        <tbody>
                          <tr>
                            <th>所属部署</th>
                            <!-- data から取り出す -->
                            <td><?= h($employee["dept_name"]) ?></td>
                          </tr>
                          <tr>
                            <th>名前</th>
                            <!-- data から取り出す -->
                            <td><?= h($employee["emp_name"]) ?></td>
                          </tr>
                        
                          <tr>
                            <th>連絡先</th>
                            <!-- data から取り出す -->
                            <td><?= h($employee["emp_tel"]) ?></td>
                          </tr>

                          <tr>
                            <th>役職</th>
                            <td><?= h($employee["emp_position"]) ?></td>
                          </tr>
                        
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>

          <div class="text-end mb-0 mt-2">
            <a href="employee_list.php" class="btn btn-dark">社員一覧画面へ戻る</a>
          </div>
        </section>
    </main>
</body>

</html>