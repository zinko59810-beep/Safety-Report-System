<?php
session_start();
require_once('db.php');

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $emp_id = trim($_POST["emp_id"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($emp_id === "" || $password === "") {
        $error = "入力してください";
    } else {
        try {
            //$dbh ထဲကို Database Connection Object ရောက်လာတာ။
            $dbh = db_connect();
            // $stmt ဆိုတာDatabase က ပြန်ပို့လာတဲ့ result object
            $stmt = $dbh->prepare("
                SELECT e.emp_id, e.emp_name, e.emp_role, a.auth_password
                FROM employee e
                INNER JOIN auth a ON e.emp_id = a.emp_id
                WHERE e.emp_id = ?
            ");
            $stmt->execute([$emp_id]);
            //Data ကို ဘယ်အချိန်ရလဲ?
            $user = $stmt->fetch();
            // var_dump($user);

          
        //   var_dump($hash);

            if (!$user) {
                $error = "社員番号が存在しません";
            } else if (!password_verify($password,$user["auth_password"]) ) {
                $error = "パスワードが違います";
            } else {
                $_SESSION["user_id"] = $user["emp_id"];
                $_SESSION["user_name"] = $user["emp_name"];
                $_SESSION["role"] = $user["emp_role"];

                

                
                if ($user["emp_role"] == 1) {
                    header("Location: admin.php/admin_menu.php");
                } else {
                    header("Location: safety_php/safe_info.php");
                }
                exit;
            }

        } catch (PDOException $e) {
            $error = "DBエラー";
        }
    }
}
?>
<!DOCTYPE html>
<!-- ログイン画面 -->
<html lang='ja'>

<head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <title>ログイン</title>
    <link rel='stylesheet' href='../styles/style.css'>
    <link rel='stylesheet' href='../styles/reset.css'>
    <!-- <link rel="stylesheet" href="../styles/login.css"> -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


 
</head>
 
<body>
    <header class="d-flex justify-content-between align-items-center px-4 py-2 shadow">
        <a href="#" class="text-white text-decoration-none fs-2">災害安否報告システム</a>
        <!-- ログイン画面以外にはログアウトボタンを設置します -->
    </header>
 
    <main>
        <section class="vh-100" style="background-color: #eee;">
          <div class="container h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
              <div class="col-lg-12 col-xl-11">
                <div class="card text-black" style="border-radius: 25px;">
                  <div class="card-body p-md-5">
                    <div class="row justify-content-center">
                      <div class="col-md-10 col-lg-6 col-xl-5 order-2 order-lg-1">

                        <p class="text-center h1 fw-bold mb-5 mx-1 mx-md-4 mt-4">Sign up</p>

                     <form method="POST" action="login.php">
                        <!-- 社員番号 -->

                          <div class="d-flex flex-row align-items-center mb-4">
                            <i class="fas fa-user fa-lg me-3 fa-fw"></i>
                            <div data-mdb-input-init class="form-outline flex-fill mb-0">
                              <input type="text" name="emp_id" placeholder="社員番号" class="form-control mb-3" autocomplete="new-password">
                              <!-- <label class="form-label" for="email">Your Email</label> -->
                            </div>
                          </div>

                          <!-- passsword -->
                          <div class="d-flex flex-row align-items-center mb-4">
                            <i class="fas fa-lock fa-lg me-3 fa-fw"></i>
                            <div data-mdb-input-init class="form-outline flex-fill mb-0">
                            <input type="password" name="password" placeholder="パスワード" class="form-control mb-3" autocomplete="new-password">

                            
                            </div>
                          </div>
                          

                        <!-- 2 column grid layout for inline styling -->
                        <div class="row mb-4">
                            <div class="col d-flex justify-content-center">
                            <!-- Checkbox -->
                            <!-- <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="form2Example31" checked />
                                <label class="form-check-label" for="form2Example31"> Remember me </label>
                            </div> -->
                            </div>

                            <!-- <div class="col">
                            
                            <a href="#!">Forgot password?</a>
                            </div> -->
                        </div>

                        <!-- register -->
                         <?php if ($error !== ""): ?>
                             <p class="text-danger text-center">
                                <?= h($error) ?>
                            </p>
                        <?php endif; ?>

                          <div class="d-flex justify-content-center mx-4 mb-3 mb-lg-4">
                            <button type="submit" class="btn btn-primary btn-lg">Sign In</button>
                          </div>
                          <!-- register -->

                        <!-- Register buttons -->
                        <div class="text-center">
                            <p>Not a member? <a href="./admin.php/employee_register.php">Register</a></p>
                            <p>or sign up with:</p>

                            
                            <button  type="button" data-mdb-button-init data-mdb-ripple-init class="btn btn-link btn-floating mx-1">
                                <i class="fab fa-facebook-f"></i>
                            </button>

                            <button  type="button" data-mdb-button-init data-mdb-ripple-init class="btn btn-link btn-floating mx-1">
                                <i class="fab fa-google"></i>
                            </button>

                            <button  type="button" data-mdb-button-init data-mdb-ripple-init class="btn btn-link btn-floating mx-1">
                                <i class="fab fa-twitter"></i>
                            </button>

                            <button  type="button" data-mdb-button-init data-mdb-ripple-init class="btn btn-link btn-floating mx-1">
                                <i class="fab fa-github"></i>
                            </button>
                        </div>
                        </form>
                      </div>   
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
        </main>

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