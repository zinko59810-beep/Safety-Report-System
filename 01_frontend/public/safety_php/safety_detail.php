<?php
session_start();
require_once('../db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}


$dbh = db_connect();

$id = $_GET['id'] ?? '';

if ($id === '') {
    echo "IDがありません";
    exit;
}

$stmt = $dbh->prepare("
    SELECT *
    FROM safety
    WHERE id = ?
");

$stmt->execute([$id]);

$safety = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$safety) {
    echo "データがありません";
    exit;
}

?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>安否情報詳細</title>

    <link rel="stylesheet" href="../../styles/reset.css">
    <link rel="stylesheet" href="../../styles/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
</head>

<body>

<header class="d-flex justify-content-between align-items-center px-4 py-2 shadow">
    <a href="#" class="text-white text-decoration-none fs-2">災害安否報告システム</a>
    <a href="../logout.php" class="btn btn-outline-dark btn-sm">ログアウト</a>
</header>

<main class="container mt-5">

    <h1 class="text-center fw-bold mb-5">
        安否情報詳細
    </h1>

    <section class="container bg-white shadow rounded p-5">

    
<div class="table-responsive">
    <table class=" table p-4 table-bordered table-hover table-striped text-center fs-4">
    <!-- <table class="table table-bordered text-center align-middle w-75 mx-auto"> -->
        <tbody>
            <tr>
                <th>社員番号</th>
                <td><?= h($safety["emp_id"]) ?></td>
            </tr>

            <tr>
                <th>投稿日時</th>
                <td><?= h($safety["safe_timestamp"]) ?></td>
            </tr>

            <tr>
                <th>本人状態</th>
                <td>
                    <?php
                    if ($safety["safe_state"] == "1") echo "無傷";
                    elseif ($safety["safe_state"] == "2") echo "軽傷";
                    elseif ($safety["safe_state"] == "3") echo "重傷";
                    else echo "無事";
                    ?>
                </td>
            </tr>

            <tr>
                <th>出社状況</th>
                <td>
                    <?php
                    if ($safety["safe_propriety"] == "1") echo "可能";
                    elseif ($safety["safe_propriety"] == "2") echo "不可";
                    elseif ($safety["safe_propriety"] == "3") echo "わからない";
                    else echo "-";
                    ?>
                </td>
            </tr>

           <tr>
              <th>出社手段</th>
              <td><?= h($safety["means"]) ?></td>
          </tr>

          <tr>
              <th>コメント</th>
              <td><?= nl2br(h($safety["comment"])) ?></td>
          </tr>
        </tbody>
    </table>
</div>
        <?php if ($safety["emp_id"] == $_SESSION["user_id"]) { ?>
            <div class="text-end mt-3">

                <a href="./safety_register.php?id=<?= h($safety["id"]) ?>" class="btn btn-primary">
                    修正
                </a>
            </div>    
        <?php } ?>

    <div class="text-start mt-1">
        <a href="./safety_list.php" class="btn btn-dark">
            社員安否一覧へ戻る
        </a>
    </div>

</section>

</main>

</body>
</html>