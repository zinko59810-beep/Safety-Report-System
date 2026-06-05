
<?php
session_start();
require_once('../db.php');

//  管理者だけ使えるようにする
// if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
//     header("Location: ../login.php");
//     exit;
// }

$message = "";


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $emp_id   = trim($_POST["emp_id"] ?? "");
    $emp_name = trim($_POST["emp_name"] ?? "");
    $emp_tel  = trim($_POST["emp_tel"] ?? "");
    $dept_id  = $_POST["dept_id"] ?? "";
    $emp_role = $_POST["emp_role"] ?? "0";
    $password = $_POST["password"] ?? "";
    $repeat   = $_POST["repeatpassword"] ?? "";

    if ($emp_role == 1) {
        $emp_position = "管理者";
    } else {
        $emp_position = "一般";
    }

    if ($emp_id === "" || $emp_name === "" || $dept_id === "" || $password === "") {
        $message = "全部入力してください";
    } elseif ($password !== $repeat) {
        $message = "パスワードが一致しません";
    } else {
        try {
            $dbh = db_connect();
            $dbh->beginTransaction();

            // employee
            $stmt = $dbh->prepare("
                INSERT INTO employee
                (emp_id, emp_name, emp_tel, emp_role,emp_position, dept_id, emp_worker_id, emp_work_timestamp)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ");
           $stmt->execute([
                            $emp_id,
                            $emp_name,
                            $emp_tel,
                            $emp_role,
                            $emp_position,
                            $dept_id,
                            null
                        ]);

            // auth
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $dbh->prepare("
                INSERT INTO auth
                (emp_id, auth_password, auth_worker_id, auth_work_timestamp)
                VALUES (?, ?, ?, NOW())
            ");
            $stmt->execute([$emp_id, $hash, null]);

            $dbh->commit();

                echo "
                <script>
                alert('登録成功！');
                window.location.href = '../login.php';
                </script>
                ";

                exit;

        } catch (PDOException $e) {
            $dbh->rollBack();
            $message = "登録エラー";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>社員登録</title>
    <link rel="stylesheet" href="">
    <link rel="stylesheet" href="../../styles/reset.css">
    <link rel="stylesheet" href="../../styles/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


</head>
<body>
  <header class="d-flex justify-content-between align-items-center px-4 py-2 shadow">
    <a href="#" class="text-white text-decoration-none fs-2">災害安否報告システム</a>
    <a href="../logout.php" class="btn btn-outline-dark btn-sm">ログアウト</a>
  </header>
 
 
    <main>
        <section class="vh-100 gradient-custom">
  <div class="container py-5 h-100">
    <div class="row justify-content-center align-items-center h-100">
      <div class="col-12 col-lg-9 col-xl-7">

        <div class="card shadow-lg" style="border-radius: 15px;">
          <div class="card-body p-4 p-md-5">

            <h3 class="mb-4 pb-2 pb-md-0 mb-md-5 text-center">
              社員登録
            </h3>

            <form method="POST">

              <!-- 社員番号 -->
              <div class="row">
                <div class="col-md-6 mb-4">

                  <div class="form-outline">
                    <input type="text"
                      name="emp_id"
                      class="form-control form-control-lg"
                      placeholder="社員番号"
                      autocomplete="new-emp_id">
                  </div>

                </div>

                <!-- 名前 -->
                <div class="col-md-6 mb-4">

                  <div class="form-outline">
                    <input type="text"
                      name="emp_name"
                      class="form-control form-control-lg"
                      placeholder="名前">
                  </div>

                </div>
              </div>

              <!-- 電話番号 -->
              <div class="row">
                <div class="col-md-12 mb-4">

                  <div class="form-outline">
                    <input type="text"
                      name="emp_tel"
                      class="form-control form-control-lg"
                      placeholder="電話番号">
                  </div>

                </div>
              </div>

              <!-- 部署 -->
              <div class="row">
                <div class="col-md-6 mb-4">

                  <select name="dept_id" class="form-select form-select-lg">
                    <option value="00001">総務課</option>
                    <option value="00002">営業課</option>
                    <option value="00003">人事課</option>
                    <option value="00004">経理課</option>
                    <option value="00005">企画課</option>
                    <option value="00006">システム保守課</option>
                    <option value="00007">情報システム課</option>
                  </select>

                </div>

                <!-- 権限 -->
                <div class="col-md-6 mb-4">

                  <select name="emp_role" class="form-select form-select-lg">
                    <option value="0">一般</option>
                    <option value="1">管理者</option>
                  </select>

                </div>
              </div>

              <!-- パスワード -->
              <div class="row">
                <div class="col-md-6 mb-4">

                  <div class="form-outline">
                    <input type="password"
                      name="password"
                      class="form-control form-control-lg"
                      placeholder="パスワード"
                      autocomplete="new-password">
                  </div>

                </div>

                <!-- 確認パスワード -->
                <div class="col-md-6 mb-4">

                  <div class="form-outline">
                    <input type="password"
                      name="repeatpassword"
                      class="form-control form-control-lg"
                      placeholder="確認パスワード">
                  </div>

                </div>
              </div>

              <!-- 登録ボタン -->
              <div class="mt-4 pt-2 text-center">
                <button type="submit" class="btn btn-primary btn-lg px-5" >
                  登録
                </button>
              </div>

            </form>

            <?php if ($message !== ""): ?>
              <script>
                alert("<?= h($message) ?>");
              </script>
            <?php endif; ?>

          </div>
        </div>

      </div>
    </div>
  </div>
</section>
    </main>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



<!-- Enter押す → 次のinputへ移動 -->
        <script>
                document.addEventListener("DOMContentLoaded", () => {
                  const inputs = document.querySelectorAll("input");

                  inputs.forEach((input, index) => {
                    input.addEventListener("keydown", function(e) {
                      if (e.key === "Enter") {
                        e.preventDefault();

                        const next = inputs[index + 1];

                        if (next) {
                          next.focus(); // 次のボックスへ
                        }
                      }
                    });
                  });
                });
        </script>    
  </body>
</html>