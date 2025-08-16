<?php
session_start();
$_PAGENAME = "Registration";

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
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get data from form
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];
}

function insert_data($conn, $errors, $name, $email, $password, $role) {
    if ($errors == 0) {
        $sql = 'INSERT INTO users VALUES (DEFAULT, "' . $name . '", "' . $email . '", NULL, "' . $password . '", NULL, CURRENT_TIMESTAMP, NULL, "'.$role.'", DEFAULT, "A");';

        if ($conn->query($sql) === TRUE) {
            echo "New record created successfully, You may now ";
            echo "<a href='login.php'>login</a>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
        // define variables and set to empty values
            $nameErr = $passwordErr = $emailErr = $genderErr = $websiteErr = $tosErr = $confirmPasswordErr = $roleErr = "";
            $name = $password = $email = $gender = $comment = $tos = $website = $confirmPassword = $role = "";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script>
    (() => {
        'use strict'

        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        const forms = document.querySelectorAll('.needs-validation')

        // Loop over them and prevent submission
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
    .error {
        color: #FF0000;
        display: inline;
    }

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

    <?php
    $name = $password = $email = $gender = $comment = $tos = $website = $confirmPassword = $role = "";
    $nameErr = $passwordErr = $emailErr = $genderErr = $websiteErr = $tosErr = $confirmPasswordErr = $roleErr = "";

    if (isset($_POST['submit'])) {
        $errors = 0;
        if (empty($_POST["name"])) {
            $errors++;
            $nameErr = "Name is required";
        } else {
            $name = test_input($_POST["name"], $errors);
        }

        if (empty($_POST["email"])) {
            $errors++;
            $emailErr = "Email is required";
        } else {
            $email = test_input($_POST["email"], $errors);

            // check if e-mail address is well-formed
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors++;
                $emailErr = "Invalid email format";
            }
        }

        if (empty($_POST["password"])) {
            $errors++;
            $passwordErr = "Password is required";
        } else {
            $password = test_input($_POST["password"], $errors);
        }

        if (empty($_POST["role"])) {
            $errors++;
            $roleErr = "Role is required";
        } else {
            $role = test_input($_POST["role"], $errors);
        }

        if (empty($_POST["confirmPassword"])) {
            $errors++;
            $confirmPasswordErr = "Confirm password is required";
//             echo '<script>alert("1");</script>';
        } else {
            $confirmPassword = test_input($_POST["confirmPassword"], $errors);
            if ($password !== $confirmPassword) {
//                 echo '<script>alert("3");</script>';
                $errors++;
                $confirmPasswordErr = "Passwords must match";
            } else {
//                 echo '<script>alert("2");</script>';
            }
        }







        if (empty($_POST["gender"])) {
            $errors++;
            $genderErr = "Gender is required";
        } else {
            $gender = test_input($_POST["gender"], $errors);
        }

        if (empty($_POST["tos"])) {
            $errors++;
            $tosErr = "tos is required";
        } else {
            $tos = test_input($_POST["tos"], $errors);
        }


        //echo '<script>alert("test");</script>';
        if ($errors == 0) {
            // This is where data should be actually submitted to the database
            // $sql = "INSERT INTO students (first_name, last_name, email, password)
            //         VALUES ('$firstname', '$lastname', '$email', '$password')";

            // if ($conn->query($sql) === TRUE) {
            //     echo "New record created successfully";
            // } else {
            //     echo "Error: " . $sql . "<br>" . $conn->error;
            // }
        }
    }

    function test_input($data, $errors)
    {
        if ($errors > 0) {


        } else {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }

    }
    if (isset($_POST['submit'])) {
        insert_data($conn, $errors, $name, $email, $password, $role);
    }
    ?>


    <form class="row g-3 needs-validation custom-form" novalidate method="POST">
        <div class="col-md-6">
            <label for="validationCustom02" class="form-label">Name <p class="error">*
                    <?php echo $nameErr; ?>
                </p></label>

            <input type="text" class="form-control" name="name" id="validationCustom02"
                value="<?php echo $name; ?>" required>
            <div class="valid-feedback">
                Looks good!
            </div>
        </div>
        <div class="col-md-6">
            <label for="validationCustomUsername" class="form-label">E-mail <p class="error">*
                    <?php echo $emailErr; ?>
                </p></label>
            <div class="input-group has-validation">
                <input type="text" class="form-control" name="email" id="validationCustomUsername"
                    aria-describedby="inputGroupPrepend" value="<?php echo $email; ?>" required>
                <div class="invalid-feedback">
                    Please choose a username.
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <label for="validationCustom03" class="form-label">Password <p class="error">*
                    <?php echo $passwordErr; ?>
                </p></label>

            <input type="text" class="form-control" name="password" id="validationCustom03"

                value="<?php echo $password; ?>" required>
            <div class="invalid-feedback">
                Please provide a valid password.
            </div>
        </div>
        <!-- <div class="col-md-4">
            <label for="validationCustom04" class="form-label">City</label>
            <select class="form-select" name="state" id="validationCustom04" required>
                <option value="AL">AL</option>
                <option value="AK">AK</option>
                <option value="AZ">AZ</option>
                <option value="AR">AR</option>
                <option value="CA">CA</option>
                <option value="CO">CO</option>
                <option value="CT">CT</option>
                <option value="DE">DE</option>
                <option value="FL">FL</option>
                <option value="GA">GA</option>
                <option value="HI">HI</option>
                <option value="ID">ID</option>
                <option value="IL">IL</option>
                <option value="IN">IN</option>
                <option value="IA">IA</option>
                <option value="KS">KS</option>
                <option value="KY">KY</option>
                <option value="LA">LA</option>
                <option value="ME">ME</option>
                <option value="MD">MD</option>
                <option value="MA">MA</option>
                <option value="MI">MI</option>
                <option value="MN">MN</option>
                <option value="MS">MS</option>
                <option value="MO">MO</option>
                <option value="MT">MT</option>
                <option value="NE">NE</option>
                <option value="NV">NV</option>
                <option value="NH">NH</option>
                <option value="NJ">NJ</option>
                <option value="NM">NM</option>
                <option value="NY">NY</option>
                <option value="NC">NC</option>
                <option value="ND">ND</option>
                <option value="OH">OH</option>
                <option value="OK">OK</option>
                <option value="OR">OR</option>
                <option value="PA">PA</option>
                <option value="RI">RI</option>
                <option value="SC">SC</option>
                <option value="SD">SD</option>
                <option value="TN">TN</option>
                <option value="TX">TX</option>
                <option value="UT">UT</option>
                <option value="VT">VT</option>
                <option value="VA">VA</option>
                <option value="WA">WA</option>
                <option value="WV">WV</option>
                <option value="WI">WI</option>
                <option value="WY">WY</option>
            </select>
    <div class="invalid-feedback">
        Please select a valid state.
    </div>
</div> -->

        <div class="col-md-4">
            <label for="validationCustom05" class="form-label">Confirm Password <p class="error">*
                    <?php echo $confirmPasswordErr; ?>
                </p></label>

            <input type="text" class="form-control" name="confirmPassword" id="validationCustom05"

                value="<?php echo $confirmPassword; ?>" required>
            <div class="invalid-feedback">
                Please provide a valid password
            </div>
        </div>
        <div>
            <input type="radio" name="gender" value="female" <?php if ($gender == "female") {
                echo "checked";
            } ?>>Female
            <input type="radio" name="gender" value="male" <?php if ($gender == "male") {
                echo "checked";
            } ?>>Male
            <span class="error">*
                <?php echo $genderErr; ?>
            </span>

        </div>
        <div>
            <label for="role" class="plabel">Role</label>
                    <select name="role" id="role">
                    <option value="1">Student</option>
                    <option value="2">Advisor</option>
                  </select>

        </div>
        <div class="col-12">
            <div class="form-check">
                <input class="form-check-input" name="tos" type="checkbox" id="invalidCheck" required>
                <label class="form-check-label" for="invalidCheck">
                    Agree to terms and conditions
                </label>
                <p class="error">*
                    <?php echo $tosErr; ?>
                </p>
            </div>
        </div>
        <div class="col-12">
            <button class="btn btn-primary" type="submit" name="submit">Submit</button>

        </div>
    </form>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>
    <?php
    if(isset($_POST['submit']) && $errors = 0) {
    echo "<h2>Your given values are:</h2>";
    echo $firstname;
    echo "<br>";
    echo $lastname;
    echo "<br>";

    echo $email;
    echo "<br>";

    echo $website;
    echo "<br>";

    echo $comment;
    echo "<br>";

    echo $gender;
}
?>
</body>


</html>

<?php
include 'footer.php';
?>