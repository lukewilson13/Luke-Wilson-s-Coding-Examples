<?php
    session_start();
    $_PAGENAME = "Login";

    include 'classes/Connection.php';
    $connect = new Connection();
    $c = $connect->make_connection();

    include 'header.php';

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "semplanner";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check conn
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Check if the login form is submitted
    if (isset($_POST['login'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // SQL query to check if the email and password matct
        $sql = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
        $result = $conn->query($sql);

        if ($result->num_rows == 1) {
            // Login successful
            $user = $result->fetch_assoc();
            $_SESSION['user_email'] = $user['email']; // Set the user's ID in the session

            $sql_role = 'SELECT role_id from users WHERE email = "'.$_SESSION['user_email'].'";';
            $result_role = $c->query($sql_role);

            $row_role = $result_role->fetch_assoc();
            $user_role = $row_role['role_id'];

            if ($user_role == "1") {
                header ('Location: plans.php'); // Redirect to the plans page after login
                exit;
            }
            if ($user_role == "2") {
                header('Location: view_students.php'); // Redirect to the view_students plans page after login
                exit;
            }

        } else {
            $error_message = "Invalid email or password. Please try again.";
        }
    }

    // Close the connection to the database
    $conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7Rxnatzjc@dS1G1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script>
    (() => {
        'use strict'

        const forms = document.querySelectorAll('.needs-validation')

        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }

                form.classList.add('was-validated')
            }, false)
        })
    })()
    </script>

    <style>
        .custom-form {
            margin-left: 20px;
            width: 50%;
        }

        .form-title {
            text-align: center;
            font-size: 32px;
            font-weight: bold;
            padding-top: 15px;
        }

        #create_account {
            background-color: #32CD32;
            border-color: #008000;
        }

        #forgot_password {
            background-color: #f1807e;
            border-color: pink;
        }

        hr {
            opacity: 100;
            width: 100%;
        }

        #hr_head {
            margin-top: 12px;
        }

        #header img {
            margin-bottom: 4px;
        }

        #dropdown {
            margin-bottom: -10px;
        }

        #navbar {
            margin-bottom: -6px;
        }

        .dropdown .dropbtn {
            margin-bottom: 0px;
        }

        #pagename h1 {
            margin-top: 16px;
        }

    </style>

</head>

<body>

    <div class="form-title"></div>

    <form class="row g-3 needs-validation custom-form" novalidate method="POST">
        <div class="col-md-6">
            <label for="validationCustomUsername" class="form-label">E-mail</label>
            <div class="input-group has-validation">
                <input type="text" class="form-control" name="email" id="validationCustomUsername" required>
                <div class="invalid-feedback">
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <label for="validationCustom03" class="form-label">Password</label>
            <input type="password" class="form-control" name="password" id="validationCustom03" required>
            <div class="invalid-feedback">
            </div>
        </div>
        <div class="col-12">
            <button class="btn btn-primary" type="submit" name="login">Login</button>
        </div><div class="col-12">
            <a href="registration.php" class="btn btn-primary" name="create" id="create_account">Create account</a>
        </div>
        </div><div class="col-12">
            <a href="forgotPassword.php" class="btn btn-primary" name="create" id="forgot_password">Forgot Password</a>
        </div>
    </form>
    <?php if (isset($error_message)) { ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $error_message; ?>
        </div>
    <?php } ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>

</body>

</html>


<?php
    include 'footer.php';
?>