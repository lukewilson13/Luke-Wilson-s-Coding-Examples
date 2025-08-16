<?php
session_start();
$_PAGENAME = "View Advisors";

include 'classes/Connection.php';
$connect = new Connection();
$c = $connect->make_connection();

include 'header.php';
include 'classes/Checker.php';

$Checker = new Checker($c);
$Checker->checkAuthentication();

$advisor_id = "mark@messiah.edu";
$sql1 = 'SELECT DISTINCT advisor_faculty_id from advisor;';
$result1 = $c->query($sql1);

if(isset($_GET['afid'])) {
    delete_advisor($c, $_GET['afid']);
    header('location: view_advisors.php');
}

function delete_advisor($c, $e){
	$sql = "DELETE FROM advisors where advisor_faculty_id=".$e.";";
	$c->query($sql);
}
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

    #add_advisor {
        margin: auto;
        text-align: center;
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
		<th></th>
      </tr>
    </thead>
    <tbody>
<?php
    while($row1 = $result1->fetch_assoc()) {
        $sql2 = 'SELECT email, name from users WHERE id = '.$row1['advisor_faculty_id'].';';
        $result2 = $c->query($sql2);

        while($row2 = $result2->fetch_assoc()) {
            echo "<tr>";
            echo "<td>".$row2['email']."</td>";
            echo "<td>".$row2['name']."</td>";
            echo "<td><a href='view_students_privilege.php?email=".$row2['email']."' class='btn btn-primary'><button type='button' class='btn btn-primary'>VIEW STUDENTS</button></a></td>";
            echo "<td><a href='?afid=".$row1['advisor_faculty_id']."'><button type='button' class='btn btn-primary'>REMOVE</button></a></td>";
            echo "</tr>";
        }
    }
?>

    <tr>
        <td colspan="4"><a href="add_advisors.php"><button type='button' id="add_advisor">ASSIGN ADVISOR</button></a></td>
    </tr>



