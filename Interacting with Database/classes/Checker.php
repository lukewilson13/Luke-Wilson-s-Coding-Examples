<?php
    class Checker {
        function checkAuthentication() {
            if (!isset($_SESSION['user_email'])) {
                header("Location: login.php");
            }
        }
    }
?>