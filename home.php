<?php
require(__DIR__ . "/partials/nav.php");
?>
<br>
<h1>Home</h1>
<br>
<?php

if (is_logged_in()) {
    echo "<p> Welcome, " . get_user_email() . "</p>";
} else {
    echo "You're not logged in";
    die(header("Location: login.php"));
}

//echo "<pre>" . var_export($_SESSION, true) . "</pre>";
echo "<pre>" . "</pre>";

$user_data = var_export($_SESSION, true);

$user = $_SESSION['user'];

echo "<p>Email: " . htmlspecialchars($user['email']) . "</p> <br>";
echo "<p>Username: " . htmlspecialchars($user['username']) . "</p> <br>";
echo "<p>ID: " . htmlspecialchars($user['id']) . "</p> <br>";

if (!empty($user['roles'])) {
    echo "<p>Roles: ";
    echo htmlspecialchars($user['roles'][0]['name']);
    echo "</p>";
} else {
    echo "<p>No roles assigned.</p>";
}
?>

<style>
    body p{
        font-family:serif;
        width: 95%;
        margin: 0 auto;
        color:white;
    }
    h1 {
        font-family:serif;
        width: 95%;
        margin: 0 auto;
        color:white;
    }
</style>