<?php
session_start();
$_PAGENAME = "Assign Advisors";

include 'classes/Checker.php';
include 'classes/Connection.php';
$connect = new Connection();
$c = $connect->make_connection();

include 'header.php';

$Checker = new Checker($c);
$Checker->checkAuthentication();

$sql_id = 'SELECT id from users WHERE email = "'.$_SESSION['user_email'].'";';
$result_id = $c->query($sql_id);

$row_id = $result_id->fetch_assoc();
$user_id = $row_id['id'];

function add_advisor($c, $advisor_id, $student_id, $advisor_type, $errors) {
        $sql1 = 'INSERT INTO advisor
            VALUES ("'.$advisor_id.'", "'.$student_id.'", "'.$advisor_type.'");';
        $c->query($sql1);
}

if (isset($_POST['submit'])) {
//         $advisor_id = $_POST['advisor_id'];
//         $student_id = $_POST['student_id'];
//         $advisor_type = $_POST['advisor_type'];

        $errors = 0;
        $advisor_id = test_input($_POST["advisor_name"], $errors);
        $student_id = test_input($_POST["student_name"], $errors);
        $advisor_type = test_input($_POST["advisor_type"], $errors);

if ($errors == 0) {
     add_advisor($c, $advisor_id, $student_id, $advisor_type, $errors);

     //clears form and variables.
     $course_numberErr = $course_descriptionErr = $course_nameErr = $course_creditErr = "";
     $course_number = $course_description = $course_name = $course_credit = "";
    }
}

function test_input($data, &$errors) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);

    if (empty($data)) {
            $errors++;
    }
    return $data;
}
?>
<head>
<style>
    #content {
        font-family: 'Arial', sans-serif;
        background-color: #f5f5f5;
        text-align: center;
        margin: 0;
        padding: 20px 0 0 0;
        width: 100%;
    }

    form {
        width: 50%;
        margin: 50px auto;
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .plabel {
        font-size: 24px;
        display: block;
        margin-bottom: 5px;
    }

    #advisor_name, #student_name, #advisor_type {
        width: 80%;
        padding: 8px;
        margin: 8px 0;
        display: inline-block;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }

    .date_dropdown {
        width: 80%;
    }

    #submit {
        font-family: 'Arial', sans-serif;
        width: 155px;
        height: 50px;
        margin-top: 20px;
        border: none;
        border-radius: 3px;
        background-color: #4CAF50;
        color: white;
        font-size: 18px;
        cursor: pointer;
    }

    button:hover {
        background-color: #001F3F;
    }

    #labelsdiv {
        width: 80%;
        margin: auto;
    }

    select {
        font-size: 16px;
        margin-left: 20px;
    }
</style>
</head>


<body>
<div id="content">
<form method="POST">
<div id="labelsdiv">
  <label for="advisor_name" class="plabel" class="droplabel">Advisor Name</label>
<select name="advisor_name" id="advisor_name" class="date_dropdown">
<option value="">Please select an advisor</option>
<?php
$sql_advisors = 'SELECT id, name FROM users WHERE role_id = 2';
$result_advisors = $c->query($sql_advisors);
while($row_advisors = $result_advisors->fetch_assoc()) {
    echo '<option value="'.$row_advisors['id'].'">'.$row_advisors['name'].'</option>';
}
?>
</select><br>

<label for="stduent_name" class="plabel" class="droplabel">Student Name</label>
<select name="student_name" id="student_name" class="date_dropdown">
<option value="">Please select a student</option>
<?php
$sql_students = 'SELECT id, name FROM users WHERE role_id = 1';
$result_students = $c->query($sql_students);
while($row_students = $result_students->fetch_assoc()) {
    echo '<option value="'.$row_students['id'].'">'.$row_students['name'].'</option>';
}
?>
</select><br>

<label for="advisor_type" class="plabel" class="droplabel">Advisor Type</label>
<select name="advisor_type" id="advisor_type" class="date_dropdown">
    <option value="Primary">Primary</option>
    <option value="Secondary">Secondary</option>
</select><br>
      <br><br>
      <input id="submit" type="submit" value="Submit" name="submit">
    </form>
</div>
</body>