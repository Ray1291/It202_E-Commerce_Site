<?php
session_start();
session_unset();
session_destroy();
echo "Successfully logged out!";
header("Location: login.php?message=logged_out"); 



?>



