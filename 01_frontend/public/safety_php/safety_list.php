<?php
session_start();
require_once('../db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$dbh = db_connect();

$stmt = $dbh->query("
    SELECT 
        e.emp_id,
        e.emp_name,
        e.emp_position,
        d.dept_name,
        s.id AS safety_id,
        s.safe_timestamp,
        s.safe_info,
        s.safe_state
    FROM employee e

    LEFT JOIN department d
        ON e.dept_id = d.dept_id

    LEFT JOIN safety s
        ON s.id = (
            SELECT id
            FROM safety
            WHERE emp_id = e.emp_id
            ORDER BY safe_timestamp DESC
            LIMIT 1
        )

    ORDER BY e.emp_role DESC
");

$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<!-- 社員安否一覧画面 -->
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
        <a href="../logout.php" class="btn btn-outline-dark btn-sm">ログアウト</a>
    </header>

    <main class="main ">
        <section class="">
            <div class="detail-header text-start mt-4">
                <h1 class="page-title text-center mb-4">社員安否一覧</h1>
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
                        <th class="">所属部署</th>
                        <th>名前</th>
                        <th>役職</th> 
                        <th>投稿日時</th> 
                        <th>本人状態</th>
                        <th>詳細</th>  
                    </tr>
                </thead>
                
                
                <!-- //database から取り出す -->
               
                <tbody>
                <?php foreach($employees as $emp){ ?>
                    <tr>
                        <td><?= h($emp["dept_name"]) ?></td>
                        <td><?= h($emp["emp_name"]) ?></td>
                        <td><?= h($emp["emp_position"]) ?></td>
                        <td><?= h($emp["safe_timestamp"] ?? "-") ?></td>
                        <td>
                            <?php
                            if ($emp["safe_info"] == "0") {
                                echo "無事";
                            }
                            elseif ($emp["safe_state"] == "1") {
                                echo "無傷";
                            }
                            elseif ($emp["safe_state"] == "2") {
                                echo "軽傷";
                            }
                            elseif ($emp["safe_state"] == "3") {
                                echo "重傷";
                            }
                            else {
                                echo "-";
                            }
                            ?>
                        </td>
                        <td>
                            <?php if (!empty($emp["safety_id"])) { ?>
                                <a href="./safety_detail.php?id=<?= h($emp["safety_id"]) ?>"
                                class="btn btn-primary btn-sm">
                                    詳細
                                </a>
                            <?php } else { ?>
                                -
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>

            </table>
           </div> 
            <!-------------- employee テーブル ------------------------------->

            <!-------------- 戻るボタン -------------------------------------->
            <div class="text-start mt-3 mb-1">

                <?php if ($_SESSION["role"] == 1) { ?>

                    <a href="../admin.php/admin_menu.php" class="btn btn-dark">
                        戻る
                    </a>

                <?php } else { ?>

                    <a href="./safe_info.php" class="btn btn-dark">
                        戻る
                    </a>

                <?php } ?>

                </div>
        </section>
    </main>
</body>

</html>