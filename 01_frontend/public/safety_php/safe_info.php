<?php
session_start();
require_once('../db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$emp_id = $_SESSION['user_id'];

if (isset($_GET['safe_info'])) {
    $safe_info = $_GET['safe_info'];

    try {
        $dbh = db_connect();

        $stmt = $dbh->prepare("
            SELECT id
            FROM safety
            WHERE emp_id = ?
            ORDER BY safe_timestamp DESC
            LIMIT 1
        ");

        $stmt->execute([$emp_id]);
        $latest = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($latest) {
            $stmt = $dbh->prepare("
                UPDATE safety
                SET
                    safe_info = ?,
                    safe_state = NULL,
                    safe_propriety = NULL,
                    means = NULL,
                    comment = NULL,
                    safe_timestamp = NOW(),
                    safe_worker_id = ?,
                    safe_work_timestamp = NOW()
                WHERE id = ?
                AND emp_id = ?
            ");

            $stmt->execute([
                $safe_info,
                $emp_id,
                $latest["id"],
                $emp_id
            ]);

            $safety_id = $latest["id"];

        } else {
            $stmt = $dbh->prepare("
                INSERT INTO safety
                (
                    emp_id,
                    safe_info,
                    safe_timestamp,
                    safe_state,
                    safe_propriety,
                    safe_worker_id,
                    safe_work_timestamp
                )
                VALUES (?, ?, NOW(), ?, ?, ?, NOW())
            ");

            $stmt->execute([
                $emp_id,
                $safe_info,
                null,
                null,
                $emp_id
            ]);

            $safety_id = $dbh->lastInsertId();
}

        echo "<script>
                alert('安否情報を登録しました');
                window.location.href = './safety_detail.php?id={$safety_id}';
            </script>";
        exit;

    } catch (PDOException $e) {
        echo "<script>alert('登録エラー');</script>";
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
    <link rel="stylesheet" href="../../styles/safety_info.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
</head>

<body>
<header class="d-flex justify-content-between align-items-center px-4 py-2 shadow">
    <a href="#" class="text-white text-decoration-none fs-2">災害安否報告システム</a>
    <a href="../logout.php" class="btn btn-outline-dark btn-sm">ログアウト</a>
</header>

<main class="main d-flex flex-column justify-content-center align-items-center ">
    <h1 class="page-title text-center mb-5 mt-4">安否状況をお知らせください</h1>

    <section class="safety-card container shadow p-5 bg-white text-center mt-4">
        <div class="safety-btn-area">
            <a href="./safe_info.php?safe_info=0" class="safety-btn safe-btn">
                ○　無事
            </a>

            <a href="./safety_register.php?safe_info=1" class="safety-btn unsafe-btn">
                ×　問題あり
            </a>
        </div>
    </section>
</main>

</body>
</html>
