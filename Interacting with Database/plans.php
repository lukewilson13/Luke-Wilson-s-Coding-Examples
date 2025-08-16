<?php
session_start();
$_PAGENAME = "My plans";
$_USERNAME = "Student name";

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

if(isset($_POST['delete'])) {
    $plan_name = urldecode($_POST['delete']);
    delete_data($c, $plan_name, $user_id);
    header('location: plans.php');
}

function delete_data($c, $e, $u){
    $sql1 = 'SELECT std_myplan_id FROM student_plans where std_myplan_name="'.$e.'" and user_id="'.$u.'"';
    $fetch1 = $c->query($sql1);
    $result1 = $fetch1->fetch_assoc();

    $sql2 = 'DELETE FROM student_program where student_program_plan_id="'.$result1['std_myplan_id'].'"';
    $c->query($sql2);

    $sql3 = 'DELETE FROM student_plans where std_myplan_id="'.$result1['std_myplan_id'].'" and user_id="'.$u.'"';
    $c->query($sql3);
}

?>
<html>
<head>
<style>
    .container {
        width: 80%;
        margin: 50px auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        background-color: #fff;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        font-family: 'Arial', sans-serif;
        font-size: 20px;
    }

    th, td {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 15px;
    }

    th {
        background-color: #4CAF50;
        color: white;
    }

    tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    #create_plan {
        text-align: center;
        margin: 20px auto;
    }

    button {
        padding: 10px 20px;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    #edit {
        background-color: #4fc3f7;
    }

    #delete {
        background-color: #f1807e;
    }

    #create_button {
        background-color: #4CAF50;
    }
</style>
</head>
<body>

<div class="container mt-5">
    <div class="row">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Plan name</th>
                    <th>Created at</th>
                    <th colspan='2'></th>
                </tr>
            </thead>
            <tbody>

<?php
$sql1 = 'SELECT std_myplan_name, created_at FROM student_plans WHERE user_id = "'.$user_id.'";';
$result1 = $c->query($sql1);

while($row = $result1->fetch_assoc()) {
    echo "<tr>";
    echo "<td>".$row['std_myplan_name']."</td>";
    echo "<td>".$row['created_at']."</td>";

    // "Edit" button
    echo "<td><a href='sbuilder.php'><button id='edit' type='button' class='btn btn-primary'>EDIT</button></a></td>";

    // "Delete" button
    echo "<td>
            <form action='plans.php' method='post'>
                <input type='hidden' name='delete' value='".urlencode($row['std_myplan_name'])."'>
                <button id='delete' type='submit' class='btn btn-primary'>DELETE</button>
            </form>
          </td>";
    echo "</tr>";
}
?>

<tr>
    <td colspan='4' id="create_plan">
        <a href="create_plan.php">
            <button id='create_button' type='button' class='btn btn-primary'>CREATE PLAN</button>
        </a>
    </td>
</tr>

</tbody>
</body>
</html>
