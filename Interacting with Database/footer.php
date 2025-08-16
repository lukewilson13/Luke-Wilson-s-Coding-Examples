<?php
     function print_copy_right() {
         $year = date('Y');
         $message = '&copy; '. $year;
         return $message;
     }
?>


    <link rel="stylesheet" href="footer.css">
    <footer>
        <hr id="hr_foot">
           <h3>My Schedule Builder | Database Group <?=print_copy_right(); ?></h3>
    </footer>

    </body>
</html>
