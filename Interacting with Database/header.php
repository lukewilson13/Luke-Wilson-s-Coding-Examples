<?php

if (isset($_SESSION['user_email'])) {
    $sql_name = 'SELECT name from users WHERE email = "'.$_SESSION['user_email'].'";';
    $result_name = $c->query($sql_name);

    $row_name = $result_name->fetch_assoc();
    $user_name = $row_name['name'];

}
?>

<head>
    <title>My Schedule Builder</title>

    <style>
        #header {
            margin: -8px;
            background-color: white;

        }

        #std_name {
            float: right;
            margin-right: 30px;
            margin-top: 6px;
        }

        #pagename {
            margin-top: -89px;
            margin-left: 225px;
            padding-bottom: 1px;
        }


        #hr_head {
            border-color: teal;
            border-width: 2px;
            border-style: solid;
        }


        /* DROPDOWN STYLE */
        .navbar {
            float: right;
            margin-right: 10px;
            margin-top: 8px;
            overflow: hidden;
            background-color: white;
            font-family: Arial, Helvetica, sans-serif;
        }

        .navbar a {
            float: left;
            font-size: 16px;
            color: white;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
        }

        .dropdown {
            float: left;
            overflow: hidden;
        }

        .dropdown .dropbtn {
            cursor: pointer;
            font-size: 16px;
            border: none;
            outline: none;
            color: white;
            margin: 14px 16px;
            background-color: inherit;
            font-family: inherit;
            padding: 0;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
            z-index: 1;
            margin-left: -75px;
        }

        .dropdown-content a {
            float: none;
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            text-align: right;
        }

        .dropdown-content a:hover {
            background-color: #ddd;
        }

        .show {
            display: block;
        }

    </style>
</head>

<body>
    <div id="header">
        <!-- Messiah Logo -->
        <img src="./images/mu_logo.png" alt="messiah_logo" Width="200" Height="100">


        <!-- Nav Bar -->
        <div class="navbar">
          <div class="dropdown">
          <button class="dropbtn" onclick="myFunction()">
            <!-- <i class="fa fa-caret-down"></i> -->
            <img src="./images/dropdown.png" class="buttonimage" alt="dropdown" Width="50" Height="50">

          </button>
<?php
    if (isset($_SESSION['user_email'])) {
       $sql_role = 'SELECT role_id from users WHERE email = "'.$_SESSION['user_email'].'";';
       $result_role = $c->query($sql_role);

       $row_role = $result_role->fetch_assoc();
       $user_role = $row_role['role_id'];
    }
?>
          <div class="dropdown-content" id="myDropdown">
          <?php if ($user_role == "1") {
          ?>
            <a href="plans.php">My Plans</a>
            <a href="create_plan.php">Create a Plan</a>
            <a href="sbuilder.php">Schedule Builder</a>
            <a href="account.php">Edit Account</a>
            <a href="logout.php">Logout</a>
          <?php
          }
          if ($user_role == "2") {
          ?>
            <a href="view_students.php">View my Students</a>
            <a href="view_advisors.php">View Advisors</a>
            <a href="add_advisors.php">Assign Advisors</a>
            <a href="course_creator.php">Course Creator</a>
            <a href="Adminpagemajor.php">Course Editor</a>
            <a href="logout.php">Logout</a>


          <?php
          }
          if(isset($_SESSION['user_email']) == null) {
                echo "<a href='#'>Sign In</a>";
          }
          ?>
          </div>
          </div>
        </div>

        <!-- User Name -->
        <div id="std_name">
            <?php
                if (isset($user_name)) {
                echo "<h1>".$user_name."</h1>";
                }
            ?>
        </div>

        <!-- Name of Page -->
        <div id="pagename">
            <?= "<h1>".$_PAGENAME."</h1>"?>
        </div>



        <script>
        /* When the user clicks on the button,
        toggle between hiding and showing the dropdown content */
        function myFunction() {
          document.getElementById("myDropdown").classList.toggle("show");
        }

        // Close the dropdown if the user clicks outside of it
        window.onclick = function(e) {
          if (!e.target.matches('.buttonimage')) {
          var myDropdown = document.getElementById("myDropdown");
            if (myDropdown.classList.contains('show')) {
              myDropdown.classList.remove('show');
            }
          }
        }
        </script>

        <hr id="hr_head" style="border-style: solid;">
    </div>