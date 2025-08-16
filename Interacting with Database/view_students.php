<?php
session_start();
$_PAGENAME = "View my Students";

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
$advisor_id = $row_id['id'];


$sql1 = 'SELECT advisor_student_id from advisor WHERE advisor_faculty_id = '.$advisor_id.';';
$result1 = $c->query($sql1);
?>

<html>
<head>
<style>
    table {
        font-family: arial, sans-serif;
        border-collapse: collapse;
        width: 100%;
        font-size: 18px;
    }

    td, th {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
    }

    tr:nth-child(even) {
        background-color: #dddddd;
    }
</style>
</head>
<body>

<div class="container mt-5">
  <div class="row">
  <table class="table table-hover">
    <thead>
      <tr>
        <th>Email</th>
        <th>Name</th>
		<th></th>
      </tr>
    </thead>
    <tbody>
<?php
	while($row = $result1->fetch_assoc()) {
        $sql2 = 'SELECT email, name from users WHERE id = "'.$row['advisor_student_id'].'"';
        $result2 = $c->query($sql2);
        while($row = $result2->fetch_assoc()) {
            echo "<td>".$row['email']."</td>";
            echo "<td>".$row['name']."</td>";
        }

        echo "<td><button type='button' class='btn btn-primary'>VIEW PLANS</button></td>";
        echo "</tr>";
	}
?>



