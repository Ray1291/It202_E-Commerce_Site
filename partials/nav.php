<?php
session_start();
//Note: this is to resolve cookie issues with port numbers
$domain = $_SERVER["HTTP_HOST"];
if (strpos($domain, ":")) {
    $domain = explode(":", $domain)[0];
}
$localWorks = true; //some people have issues with localhost for the cookie params
//if you're one of those people make this false

//this is an extra condition added to "resolve" the localhost issue for the session cookie
if (($localWorks && $domain == "localhost") || $domain != "localhost") {
    session_set_cookie_params([
        "lifetime" => 60 * 60,
        "path" => "/Project",
        //"domain" => $_SERVER["HTTP_HOST"] || "localhost",
        "domain" => $domain,
        "secure" => true,
        "httponly" => true,
        "samesite" => "lax"
    ]);
}

require_once(__DIR__ . "/../lib/functions.php");

?>
<nav>
    <ul>
        <?php if (is_logged_in()) : ?>
            <li><a href="home.php">Home</a></li>
            <li><a href="profile.php">Profile</a></li>
        <?php endif; ?>
        <?php if (!is_logged_in()) : ?>
            <li><a href="login.php">Login</a></li>
            <li><a href="register.php">Register</a></li>
        <?php endif; ?>
        <li><a href="shop.php">Shop</a></li>
        <?php if (is_logged_in()) : ?>
            <li><a href="cart.php">Cart</a></li>
        <?php endif; ?>
        <?php if (has_role("Admin")) : ?>
            <li><a href="inventory.php">Inventory</a></li>
        <?php endif; ?>
        <?php if (has_role("Admin")) : ?>
            <li><a href="list_roles.php">List Roles</a></li>
        <?php endif; ?>
        <?php if (is_logged_in()) : ?>
            <li><a href="logout.php">Logout</a></li>
        <?php endif; ?>
            <li><a href="cars.php">Cars</a></li>
        
    </ul>
</nav>


<style>
ul {/*nav bar styling*/
  list-style-type: none;
  margin: 0;
  padding: 0;
  overflow: hidden;
  background-color: #1C3144;
}

li { 
  float: left;
}

li a { /*text boxes*/
  display: block;
  color: white;
  text-align: center;
  padding: 14px 16px;
  text-decoration: none;
}

li a:hover:not(.active) { /*hover button*/ 
  background-color: #162736;
}


body { /*website background */
        background-color: #606E7C;
    }

</style>