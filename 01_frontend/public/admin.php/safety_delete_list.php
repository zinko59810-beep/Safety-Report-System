<?php
session_start();
require_once('../db.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: ../login.php");
    exit;
}

$dbh = db_connect();

$stmt = $dbh->query("
    SELECT
        s.id,
        e.emp_name,
        d.dept_name,
        s.safe_timestamp,
        s.safe_info,
        s.safe_state
    FROM safety s
    LEFT JOIN employee e ON s.emp_id = e.emp_id
    LEFT JOIN department d ON e.dept_id = d.dept_id
    ORDER BY s.safe_timestamp DESC
");

$safeties = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>安否情報削除</title>
    <link rel="stylesheet" href="../../styles/reset.css">
    <link rel="stylesheet" href="../../styles/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
</head>

<body>
<header class="d-flex justify-content-between align-items-center px-4 py-2 shadow">
    <a href="#" class="text-white text-decoration-none fs-2">災害安否報告システム</a>
    <a href="../logout.php" class="btn btn-outline-dark btn-sm">ログアウト</a>
</header>

<main class="main">
    <h1 class="page-title text-center mt-4 mb-4">安否情報削除画面</h1>

    <section class="container shadow p-5">
        <table class="table table-bordered table-hover table-striped text-center shadow fs-5">
            <thead class="table-light">
                <tr>
                    <th>所属部署</th>
                    <th>名前</th>
                    <th>投稿日時</th>
                    <th>本人状態</th>
                    <th>削除</th>
                </tr>
            </thead>

            <tbody>
            <?php foreach ($safeties as $safe) { ?>
                <tr>
                    <td><?= h($safe["dept_name"]) ?></td>
                    <td><?= h($safe["emp_name"]) ?></td>
                    <td><?= h($safe["safe_timestamp"]) ?></td>
                    <td>
                        <?php
                        if ($safe["safe_info"] == "0") {
                            echo "無事";
                        } elseif ($safe["safe_state"] == "1") {
                            echo "無傷";
                        } elseif ($safe["safe_state"] == "2") {
                            echo "軽傷";
                        } elseif ($safe["safe_state"] == "3") {
                            echo "重傷";
                        } else {
                            echo "-";
                        }
                        ?>
                    </td>
                    <td>
                        <a href="./safety_delete.php?id=<?= h($safe["id"]) ?>"
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('削除しますか？')">
                            削除
                        </a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>

        <div class="text-end mt-3">
            <a href="./admin_menu.php" class="btn btn-dark">戻る</a>
        </div>
    </section>
</main>
</body>
</html>