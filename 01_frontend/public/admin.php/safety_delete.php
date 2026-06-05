<?php
session_start();
require_once('../db.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: ../login.php");
    exit;
}

$id = $_GET["id"] ?? "";

if ($id === "") {
    exit("IDがありません");
}

$dbh = db_connect();

/*
    safetyから emp_id を取得
*/
$stmt = $dbh->prepare("
    SELECT emp_id
    FROM safety
    WHERE id = ?
");

$stmt->execute([$id]);

$safety = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$safety) {
    exit("データがありません");
}

$emp_id = $safety["emp_id"];

try{

    $dbh->beginTransaction();

    /* safety削除 */
    $stmt = $dbh->prepare("
        DELETE FROM safety
        WHERE emp_id = ?
    ");
    $stmt->execute([$emp_id]);

    /* auth削除 */
    $stmt = $dbh->prepare("
        DELETE FROM auth
        WHERE emp_id = ?
    ");
    $stmt->execute([$emp_id]);

    /* employee削除 */
    $stmt = $dbh->prepare("
        DELETE FROM employee
        WHERE emp_id = ?
    ");
    $stmt->execute([$emp_id]);


    $dbh->commit();

    echo "
    <script>
        alert('削除しました');
        window.location.href = './safety_delete_list.php';
    </script>
    ";
    exit;

} catch (PDOException $e) {
    $dbh->rollBack();
    echo "削除エラー: " . h($e->getMessage());
    exit;
}
?>