<?php
session_start();

include 'classes/Checker.php';
include 'classes/Connection.php';
$advisor_email = $_GET['email'];

$connect = new Connection();
$c = $connect->make_connection();

$sql_name = 'SELECT name from users WHERE email = "'.$advisor_email.'";';
$result_name = $c->query($sql_name);

$row_name = $result_name->fetch_assoc();
$advisor_name = $row_name['name'];

$_PAGENAME = "View ".$advisor_name."'s Students";

include 'header.php';

$Checker = new Checker($c);
$Checker->checkAuthentication();

$sql_id = 'SELECT id from users WHERE email = "'.$advisor_email.'";';
$result_id = $c->query($sql_id);

$row_id = $result_id->fetch_assoc();
$advisor_id = $row_id['id'];


$sql1 = 'SELECT advisor_student_id from advisor WHERE advisor_faculty_id = "'.$advisor_id.'";';
$result1 = $c->query($sql1);

if(isset($_GET['afid'])) {
    $advisor_email = $_GET['email'];

    $sql_id = 'SELECT id from users WHERE email = "'.$advisor_email.'";';
    $result_id = $c->query($sql_id);

    $row_id = $result_id->fetch_assoc();
    $advisor_id = $row_id['id'];

    delete_advisor($c, $advisor_id, $_GET['afid']);
    header("location: view_students_privilege.php?email=".$advisor_email);
}

function delete_advisor($c, $e, $s){
    $sql = 'DELETE FROM advisor where advisor_faculty_id="'.$e.'" and advisor_student_id="'.$s.'";';
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
                while($row1 = $result1->fetch_assoc()) {
                    $sql2 = 'SELECT email, name from users WHERE id = "'.$row1['advisor_student_id'].'"';
                    $result2 = $c->query($sql2);
                    while($row2 = $result2->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>".$row2['email']."</td>";
                        echo "<td>".$row2['name']."</td>";
                        echo "<td><button type='button' class='btn btn-primary'>VIEW PLANS</button></td>";
                        echo "<td><a href='view_students_privilege.php?afid=".$row1['advisor_student_id']."&email=".$advisor_email."'><button type='button' class='btn btn-primary'>REMOVE</button></a></td>";
                        echo "</tr>";
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
    </body>
    </html>