<?php
session_start();
require_once('../db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$emp_id = $_SESSION['user_id'];

$dbh = db_connect();

$edit_id = $_GET["id"] ?? "";
$is_edit = $edit_id !== "";

$edit_data = null;

if ($is_edit) {

    $stmt = $dbh->prepare("
        SELECT *
        FROM safety
        WHERE id = ?
        AND emp_id = ?
    ");

    $stmt->execute([$edit_id, $emp_id]);

    $edit_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$edit_data) {
        exit("編集できません");
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $safe_state = $_POST["injury"] ?? "";
    $safe_propriety = $_POST["work"] ?? "";

    // 出社手段
    $means_arr = $_POST["means"] ?? [];

    if (!is_array($means_arr)) {
        $means_arr = [$means_arr];
    }

    $meansOther = trim($_POST["meansOther"] ?? "");

    if ($meansOther !== "") {
        $means_arr[] = $meansOther;
    }

    $means = implode(" , ", $means_arr);

    // コメント
    $comment = $_POST["impressions"] ?? "";

    try {

        $dbh = db_connect();

        if ($is_edit) {

            $stmt = $dbh->prepare("
                    UPDATE safety
                    SET
                        safe_info = 1,
                        safe_state = ?,
                        safe_propriety = ?,
                        means = ?,
                        comment = ?,
                        safe_timestamp = NOW(),
                        safe_work_timestamp = NOW()
                    WHERE id = ?
                    AND emp_id = ?
            ");

    $stmt->execute([
        $safe_state,
        $safe_propriety,
        $means,
        $comment,
        $edit_id,
        $emp_id
    ]);

    $safety_id = $edit_id;

} else {

    $stmt = $dbh->prepare("
        INSERT INTO safety
        (
            emp_id,
            safe_info,
            safe_state,
            safe_propriety,
            means,
            comment,
            safe_timestamp,
            safe_worker_id,
            safe_work_timestamp,
            stamp
        )
        VALUES
        (
            ?, ?, ?, ?, ?, ?, NOW(), ?, NOW(), NOW()
        )
    ");

    $stmt->execute([
        $emp_id,
        1,
        $safe_state,
        $safe_propriety,
        $means,
        $comment,
        $emp_id
    ]);

    $safety_id = $dbh->lastInsertId();
}
        echo "
        <script>
            alert('安否情報を登録しました');
            window.location.href = './safety_detail.php?id={$safety_id}';
        </script>
        ";

        exit;

    } catch (PDOException $e) {

        echo "
        <script>
            alert('登録エラー');
        </script>
        ";
    }
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>安否情報登録</title>

    <link rel="stylesheet" href="../../styles/reset.css">
    <link rel="stylesheet" href="../../styles/style.css">

    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
</head>

<body>

<header class="d-flex justify-content-between align-items-center px-4 py-2 shadow">
    <a href="#" class="text-white text-decoration-none fs-2">災害安否報告システム</a>
    <a href="../logout.php" class="btn btn-outline-dark btn-sm">ログアウト</a>
</header>

<main class="container mt-5">

    <h1 class="text-center fw-bold mb-5">
        以下は可能な範囲でお答えください
    </h1>

    <section class="container bg-white shadow rounded p-5">

        <form method="POST">

            <!-- 怪我 -->
            <div class="mb-5">

                <h4 class="fw-bold mb-3">
                    怪我はされましたか？
                </h4>

                <div class="d-flex gap-4 flex-wrap fs-5">

                    <div class="form-check">
                        <input class="form-check-input"
                            type="radio"
                            name="injury"
                            value="1"
                            checked>

                        <label class="form-check-label">
                            無傷
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input"
                            type="radio"
                            name="injury"
                            value="2">

                        <label class="form-check-label">
                            軽傷
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input"
                            type="radio"
                            name="injury"
                            value="3">

                        <label class="form-check-label">
                            重傷
                        </label>
                    </div>

                </div>
            </div>

            <!-- 出社 -->
            <div class="mb-5">

                <h4 class="fw-bold mb-3">
                    出社することはできますか？
                </h4>

                <div class="d-flex gap-4 flex-wrap fs-5">

                    <div class="form-check">
                        <input class="form-check-input"
                            type="radio"
                            name="work"
                            value="1"
                            checked>

                        <label class="form-check-label">
                            可能
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input"
                            type="radio"
                            name="work"
                            value="2">

                        <label class="form-check-label">
                            不可
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input"
                            type="radio"
                            name="work"
                            value="3">

                        <label class="form-check-label">
                            わからない
                        </label>
                    </div>

                </div>
            </div>

            <!-- 出社手段 -->
            <div class="mb-5">

                <h4 class="fw-bold mb-3">
                    出社手段
                </h4>

                <div class="d-flex gap-4 flex-wrap fs-5 mb-4">

                    <div class="form-check">
                        <input class="form-check-input"
                            type="checkbox"
                            name="means[]"
                            value="電車">

                        <label class="form-check-label">
                            電車
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input"
                            type="checkbox"
                            name="means[]"
                            value="バス">

                        <label class="form-check-label">
                            バス
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input"
                            type="checkbox"
                            name="means[]"
                            value="車">

                        <label class="form-check-label">
                            車
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input"
                            type="checkbox"
                            name="means[]"
                            value="自転車">

                        <label class="form-check-label">
                            自転車
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input"
                            type="checkbox"
                            name="means[]"
                            value="徒歩">

                        <label class="form-check-label">
                            徒歩
                        </label>
                    </div>

                </div>

                <input type="text"
                    name="meansOther"
                    class="form-control"
                    placeholder="その他を入力">

            </div>

            <!-- コメント -->
            <div class="mb-5">

                <h4 class="fw-bold mb-3">
                    コメント
                </h4>

                <textarea
                    name="impressions"
                    rows="6"
                    class="form-control"></textarea>

            </div>

            <!-- ボタン -->
            <div class="text-center">

                <button type="submit"
                    class="btn btn-primary btn-lg px-5">

                    送信

                </button>

            </div>

        </form>

    </section>

</main>

</body>
</html>