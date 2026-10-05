<?php

/* =========================
   ERROR DISPLAY
========================= */

error_reporting(E_ALL);
ini_set('display_errors', 1);


/* =========================
   DATABASE CONNECTION
========================= */

$host = "localhost";
$username = "root";
$password = "";
$database = "trading_app";

$conn = mysqli_connect(
    $host,
    $username,
    $password,
    $database
);

if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}


/* =========================
   SESSION
========================= */

session_start();

$message = "";
$messageType = "";


/* =========================
   LOGOUT
========================= */

if (isset($_GET["logout"])) {

    session_destroy();

    header("Location: index.php");
    exit();
}


/* =========================
   REGISTER
========================= */

if (isset($_POST["register"])) {

    $name = trim($_POST["name"]);
    $email = trim($_POST["register_email"]);
    $password = $_POST["register_password"];

    if ($name == "" || $email == "" || $password == "") {

        $message = "Please fill all fields.";
        $messageType = "error";

    } else {

        $check = mysqli_prepare(
            $conn,
            "SELECT id FROM users WHERE email = ?"
        );

        mysqli_stmt_bind_param(
            $check,
            "s",
            $email
        );

        mysqli_stmt_execute($check);

        $result = mysqli_stmt_get_result($check);

        if (mysqli_num_rows($result) > 0) {

            $message = "Email already registered!";
            $messageType = "error";

        } else {

            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO users (name,email,password)
                 VALUES (?,?,?)"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "sss",
                $name,
                $email,
                $hashedPassword
            );

            if (mysqli_stmt_execute($stmt)) {

                $message = "Account created successfully! Please login.";
                $messageType = "success";

            } else {

                $message = "Registration failed.";
                $messageType = "error";
            }
        }
    }
}


/* =========================
   LOGIN
========================= */

if (isset($_POST["login"])) {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if ($email == "" || $password == "") {

        $message = "Please enter email and password.";
        $messageType = "error";

    } else {

        $stmt = mysqli_prepare(
            $conn,
            "SELECT * FROM users WHERE email = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "s",
            $email
        );

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 1) {

            $user = mysqli_fetch_assoc($result);

            if (
                password_verify(
                    $password,
                    $user["password"]
                )
            ) {

                $_SESSION["user_id"] = $user["id"];
                $_SESSION["user_name"] = $user["name"];
                $_SESSION["user_email"] = $user["email"];

                $message = "Login successful!";
                $messageType = "success";

            } else {

                $message = "Wrong password!";
                $messageType = "error";
            }

        } else {

            $message = "Account not found!";
            $messageType = "error";
        }
    }
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>TradeX - Trading Platform</title>


<style>

/* =========================
   RESET
========================= */

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}


/* =========================
   BODY
========================= */

body {

    min-height: 100vh;

    background:
        linear-gradient(
            135deg,
            #061b2d,
            #0b3552
        );

    display: flex;

    justify-content: center;

    align-items: center;

    padding: 30px;
}


/* =========================
   MAIN CONTAINER
========================= */

.container {

    width: 1100px;

    min-height: 650px;

    background: white;

    border-radius: 20px;

    overflow: hidden;

    display: flex;

    box-shadow:
        0 20px 60px
        rgba(0,0,0,0.3);
}


/* =========================
   LEFT SIDE
========================= */

.left {

    width: 50%;

    padding: 60px;

    background:
        linear-gradient(
            135deg,
            #062039,
            #0c4567
        );

    color: white;
}


.logo {

    font-size: 32px;

    font-weight: bold;

    margin-bottom: 80px;
}


.logo span {

    color: #20d486;
}


.left h1 {

    font-size: 45px;

    line-height: 1.2;

    margin-bottom: 20px;
}


.left p {

    color: #c9d7e2;

    line-height: 1.7;

    font-size: 16px;
}


/* =========================
   MARKET CARDS
========================= */

.market {

    display: flex;

    gap: 15px;

    margin-top: 50px;
}


.market-card {

    background:
        rgba(255,255,255,0.08);

    padding: 20px;

    border-radius: 12px;

    width: 180px;
}


.market-card span {

    color: #c9d7e2;

    font-size: 13px;
}


.market-card h3 {

    margin: 8px 0;

    font-size: 20px;
}


.green {

    color: #20d486;
}


/* =========================
   RIGHT SIDE
========================= */

.right {

    width: 50%;

    padding: 50px;

    display: flex;

    justify-content: center;

    align-items: center;
}


.box {

    width: 390px;
}


.box h2 {

    font-size: 32px;

    color: #17202a;

    margin-bottom: 8px;
}


.subtitle {

    color: #777;

    margin-bottom: 25px;
}


/* =========================
   MESSAGE
========================= */

.message {

    padding: 12px;

    border-radius: 8px;

    margin-bottom: 18px;

    font-size: 14px;
}


.success {

    background: #eafaf2;

    color: #118653;
}


.error {

    background: #fff0f0;

    color: #d33;
}


/* =========================
   FORM
========================= */

.form-group {

    margin-bottom: 17px;
}


label {

    display: block;

    margin-bottom: 7px;

    font-size: 14px;

    font-weight: bold;

    color: #333;
}


input {

    width: 100%;

    padding: 14px;

    border: 1px solid #ddd;

    border-radius: 8px;

    outline: none;

    font-size: 14px;
}


input:focus {

    border-color: #16a36b;
}


/* =========================
   PASSWORD
========================= */

.password-container {

    position: relative;
}


.password-container input {

    padding-right: 60px;
}


.show-password {

    position: absolute;

    right: 10px;

    top: 12px;

    border: none;

    background: none;

    color: #159d67;

    cursor: pointer;
}


/* =========================
   LOGIN BUTTON
========================= */

.login-btn {

    width: 100%;

    padding: 14px;

    border: none;

    border-radius: 8px;

    background: #159d67;

    color: white;

    font-size: 16px;

    font-weight: bold;

    cursor: pointer;

    margin-top: 5px;
}


.login-btn:hover {

    background: #107b50;
}


/* =========================
   SWITCH
========================= */

.switch {

    text-align: center;

    margin-top: 20px;

    color: #777;

    font-size: 14px;
}


.switch span {

    color: #159d67;

    font-weight: bold;

    cursor: pointer;
}


/* =========================
   REGISTER
========================= */

#registerForm {

    display: none;
}


/* =========================
   TRADE
========================= */

.trade {

    margin-top: 25px;

    padding-top: 20px;

    border-top: 1px solid #eee;
}


.trade-title {

    text-align: center;

    color: #888;

    font-size: 13px;

    margin-bottom: 12px;
}


.trade-buttons {

    display: flex;

    gap: 12px;
}


.buy,
.sell {

    width: 50%;

    padding: 13px;

    border: none;

    border-radius: 8px;

    color: white;

    font-weight: bold;

    font-size: 15px;

    cursor: pointer;
}


.buy {

    background: #16a36b;
}


.sell {

    background: #e34b4b;
}


.buy:hover {

    background: #108653;
}


.sell:hover {

    background: #c93737;
}


/* =========================
   DASHBOARD
========================= */

.dashboard {

    text-align: center;
}


.dashboard h2 {

    margin-bottom: 10px;
}


.balance {

    background: #f5f8fa;

    padding: 20px;

    border-radius: 12px;

    margin: 20px 0;
}


.balance h3 {

    font-size: 28px;

    color: #159d67;
}


.dashboard-buttons {

    display: flex;

    gap: 12px;
}


.logout {

    display: inline-block;

    margin-top: 20px;

    color: #e34b4b;

    text-decoration: none;

    font-size: 14px;
}


/* =========================
   RESPONSIVE
========================= */

@media(max-width:800px) {

    .container {

        flex-direction: column;

        width: 100%;
    }

    .left,
    .right {

        width: 100%;
    }

    .left {

        padding: 40px;
    }

    .left h1 {

        font-size: 35px;
    }

    .logo {

        margin-bottom: 40px;
    }

    .right {

        padding: 40px 20px;
    }
}

</style>

</head>


<body>


<div class="container">


<!-- =========================
     LEFT
========================= -->

<div class="left">

    <div class="logo">
        Trade<span>X</span>
    </div>


    <h1>

        Trade Smart.<br>

        Invest Better.

    </h1>


    <p>

        Simple, fast and powerful
        trading platform for your
        investment journey.

    </p>


    <div class="market">


        <div class="market-card">

            <span>NIFTY 50</span>

            <h3>25,620.50</h3>

            <small class="green">
                +1.24%
            </small>

        </div>


        <div class="market-card">

            <span>SENSEX</span>

            <h3>83,920.15</h3>

            <small class="green">
                +0.87%
            </small>

        </div>


    </div>

</div>



<!-- =========================
     RIGHT
========================= -->

<div class="right">


<div class="box">


<?php if ($message != ""): ?>

<div class="message <?php echo $messageType; ?>">

    <?php echo $message; ?>

</div>

<?php endif; ?>



<?php if (isset($_SESSION["user_id"])): ?>


<!-- =========================
     DASHBOARD
========================= -->

<div class="dashboard">

    <h2>
        Welcome,
        <?php echo htmlspecialchars($_SESSION["user_name"]); ?>
    </h2>

    <p class="subtitle">
        Your trading dashboard
    </p>


    <div class="balance">

        <p>Available Balance</p>

        <h3>
            ₹1,00,000
        </h3>

    </div>


    <div class="dashboard-buttons">

        <button
            class="buy"
            onclick="buyStock()"
        >
            ▲ BUY
        </button>


        <button
            class="sell"
            onclick="sellStock()"
        >
            ▼ SELL
        </button>

    </div>


    <a
        href="?logout=1"
        class="logout"
    >
        Logout
    </a>

</div>



<?php else: ?>


<!-- =========================
     LOGIN FORM
========================= -->

<div id="loginForm">

    <h2>
        Welcome Back
    </h2>

    <p class="subtitle">
        Login to your trading account
    </p>


    <form method="POST">


        <div class="form-group">

            <label>
                Email
            </label>

            <input
                type="email"
                name="email"
                placeholder="Enter your email"
                required
            >

        </div>



        <div class="form-group">

            <label>
                Password
            </label>


            <div class="password-container">

                <input
                    type="password"
                    id="loginPassword"
                    name="password"
                    placeholder="Enter your password"
                    required
                >


                <button
                    type="button"
                    class="show-password"
                    onclick="togglePassword('loginPassword', this)"
                >
                    Show
                </button>

            </div>

        </div>



        <button
            type="submit"
            name="login"
            class="login-btn"
        >
            Login
        </button>

    </form>


    <div class="switch">

        Don't have an account?

        <span onclick="showRegister()">
            Create Account
        </span>

    </div>


    <!-- BUY SELL -->

    <div class="trade">

        <p class="trade-title">
            Quick Trade
        </p>


        <div class="trade-buttons">

            <button
                class="buy"
                onclick="buyStock()"
            >
                ▲ BUY
            </button>


            <button
                class="sell"
                onclick="sellStock()"
            >
                ▼ SELL
            </button>

        </div>

    </div>

</div>



<!-- =========================
     REGISTER FORM
========================= -->

<div id="registerForm">

    <h2>
        Create Account
    </h2>

    <p class="subtitle">
        Start your trading journey
    </p>


    <form method="POST">


        <div class="form-group">

            <label>
                Full Name
            </label>

            <input
                type="text"
                name="name"
                placeholder="Enter your name"
                required
            >

        </div>



        <div class="form-group">

            <label>
                Email
            </label>

            <input
                type="email"
                name="register_email"
                placeholder="Enter your email"
                required
            >

        </div>



        <div class="form-group">

            <label>
                Password
            </label>

            <div class="password-container">

                <input
                    type="password"
                    id="registerPassword"
                    name="register_password"
                    placeholder="Create password"
                    required
                >

                <button
                    type="button"
                    class="show-password"
                    onclick="togglePassword('registerPassword', this)"
                >
                    Show
                </button>

            </div>

        </div>



        <button
            type="submit"
            name="register"
            class="login-btn"
        >
            Create Account
        </button>

    </form>


    <div class="switch">

        Already have an account?

        <span onclick="showLogin()">
            Login
        </span>

    </div>

</div>


<?php endif; ?>


</div>

</div>

</div>



<script>

/* =========================
   SHOW LOGIN
========================= */

function showLogin() {

    document.getElementById("loginForm").style.display = "block";

    document.getElementById("registerForm").style.display = "none";

}


/* =========================
   SHOW REGISTER
========================= */

function showRegister() {

    document.getElementById("loginForm").style.display = "none";

    document.getElementById("registerForm").style.display = "block";

}


/* =========================
   PASSWORD
========================= */

function togglePassword(id, button) {

    const input = document.getElementById(id);

    if (input.type === "password") {

        input.type = "text";

        button.innerText = "Hide";

    } else {

        input.type = "password";

        button.innerText = "Show";

    }

}


/* =========================
   BUY
========================= */

function buyStock() {

    alert(
        "BUY selected!\n\nLogin is required to place an order."
    );

}


/* =========================
   SELL
========================= */

function sellStock() {

    alert(
        "SELL selected!\n\nLogin is required to place an order."
    );

}

</script>


</body>

</html>