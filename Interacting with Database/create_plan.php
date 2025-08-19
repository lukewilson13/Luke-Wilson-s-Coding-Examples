<?php
session_start();
$_PAGENAME = "Create a Plan";

include 'classes/Connection.php';
$connect = new Connection();
$c = $connect->make_connection();

include 'header.php';
include 'classes/Checker.php';

$Checker = new Checker($c);
$Checker->checkAuthentication();

$sql_id = 'SELECT id from users WHERE email = "'.$_SESSION['user_email'].'";';
$result_id = $c->query($sql_id);

$row_id = $result_id->fetch_assoc();
$user_id = $row_id['id'];

function create_plan($c, $plan_name, $start_date, $grad_date, $program_type, $program, $uid, $errors) {
        $sql1 = 'INSERT INTO student_plans
            VALUES (DEFAULT, "'.$plan_name.'", "'.$uid.'", TIMESTAMP(CURRENT_TIMESTAMP), "'.$start_date.'", "'.$grad_date.'");';
        $c->query($sql1);
//         echo "<br/>".$sql1."<br/>";

        $sql2 = 'SELECT std_myplan_id FROM student_plans where std_myplan_name="'.$plan_name.'" and user_id="'.$uid.'";';
        $result2 = $c->query($sql2);
        $row_result2 = $result2->fetch_assoc();
//         echo "<br/>".$sql2."<br/>";

        $sql3 = 'SELECT programs_id FROM programs where programs_name="'.$program.'" and programs_type="'.$program_type.'";';
        $result3 = $c->query($sql3);
        $row_result3 = $result3->fetch_assoc();
//         echo "<br/>".$sql3."<br/>";

        $sql4 = 'INSERT INTO student_program (student_program_plan_id, student_program_program_id, created_at, updated_at
)            VALUES ("'.$row_result2['std_myplan_id'].'", "'.$row_result3['programs_id'].'", TIMESTAMP(CURRENT_TIMESTAMP), TIMESTAMP(CURRENT_TIMESTAMP));';
//         echo "<br/>".$sql4."<br/>";
        $c->query($sql4);
}

if (isset($_POST['submit'])) {
        $plan_name = $_POST['pname'];
        $start_date = $_POST['start_date'];
        $grad_date = $_POST['end_date'];
        $program_type = $_POST['program_type'];
        $program = $_POST['program'];

        $errors = 0;
        $plan_name = test_input($_POST["pname"], $errors);
        $start_date = test_input($_POST["start_date"], $errors);
        $grad_date = test_input($_POST["end_date"], $errors);
        $program_type = test_input($_POST["program_type"], $errors);


if ($_POST["program"] == "Please select program type first") {
    $errors++;
    $programErr = "Program is required";
    echo "<br/>".$errors."<br/>";
} else {
    $program = test_input($_POST["program"], $errors);
}

if ($errors == 0) {
     create_plan($c, $plan_name, $start_date, $grad_date, $program_type, $program, $user_id, $errors);

     //clears form and variables.
     $course_numberErr = $course_descriptionErr = $course_nameErr = $course_creditErr = "";
     $course_number = $course_description = $course_name = $course_credit = "";

     header('Location: plans.php');
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

    #pname, #start_date, #end_date, #program_type, #program {
        width: 80%;
        padding: 8px;
        margin: 8px 0;
        display: inline-block;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }

    .date_dropdown, #program_type, #program {
        width: 80%;
    }

    #submit {
        width: 150px;
        height: 45px;
        margin-top: 20px;
        border: none;
        border-radius: 3px;
        background-color: #4CAF50;
        color: white;
        font-size: 16px;
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
  <label for="pname" class="plabel">Plan name</label>
  <input type="text" id="pname" name="pname"><br>

  <label for="start_date" class="plabel" class="droplabel">Start on</label>
<select name="start_date" id="start_date" class="date_dropdown">
    <option value=""></option>
    <option value="FA2024">FA2023</option>
    <option value="SP2024">SP2024</option>
    <option value="FA2025">FA2024</option>
    <option value="SP2025">SP2025</option>
    <option value="FA2026">FA2025</option>
    <option value="SP2026">SP2026</option>
    <option value="FA2027">FA2026</option>
    <option value="SP2027">SP2027</option>
    <option value="SP2027">FA2027</option>
    <option value="SP2027">SP2028</option>
</select><br>

<label for="end_date" class="plabel" class="droplabel">Graduate by</label>
<select name="end_date" id="end_date" class="date_dropdown">
    <option value=""></option>
    <option value="FA2024">FA2023</option>
    <option value="SP2024">SP2024</option>
    <option value="FA2025">FA2024</option>
    <option value="SP2025">SP2025</option>
    <option value="FA2026">FA2025</option>
    <option value="SP2026">SP2026</option>
    <option value="FA2027">FA2026</option>
    <option value="SP2027">SP2027</option>
    <option value="SP2027">FA2027</option>
    <option value="SP2027">SP2028</option>
</select><br>



    <label for="program_type" class="plabel">Program Type</label>
        <select name="program_type" id="program_type">
        <option value="" selected="selected"></option>
      </select>
      <br><br>
      <label for="program_type" class="plabel">Program</label>
        <select name="program" id="program">
        <option value="" selected="selected">Please select program type first</option>
      </select>
      <br><br>
      <input id="submit" type="submit" value="Submit" name="submit">
    </form>
</div>


<script>
var program_typeObject = {
  "Major": {
    "Accounting": null,
    "Actuarial Science": null,
    "Computer and Information Science": null,
    "Cybersecurity": null
  },"Minor": {
    "Accounting": null,
    "Cybersecurity": null
  },"Concentration": {
    "Business Information Systems": null,
    "Computer Science": null,
    "Web Development": null,
    "Software Development": null
  },
  "Teaching": {
    "Computer Science": null
  }
}

window.onload = function() {
  var program_typeSel = document.getElementById("program_type");
  var programSel = document.getElementById("program");

  for (var x in program_typeObject) {
    program_typeSel.options[program_typeSel.options.length] = new Option(x, x);
  }

  program_typeSel.onchange = function() {
    // Empty programs dropdown
    programSel.length = 1;

    // Display correct values
    for (var y in program_typeObject[this.value]) {
      programSel.options[programSel.options.length] = new Option(y, y);
    }
  }
}

</script>
</body>
