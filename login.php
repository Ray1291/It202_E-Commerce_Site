<?php
require(__DIR__ . "/partials/nav.php");
?>
<form onsubmit="return validate(this)" method="POST">
    <div>

        <br>
        <label for="email">Email or Username</label>
        <br>
        <input type="text" name="email" required />
    </div>
    <br>
    <br>
    <div>
        <label for="pw">Password</label>
        <br>
        <input type="password" id="pw" name="password" required minlength="8" />
    </div>
    <br>
    <input type="submit" value="Login" />
</form>
<script>
    function validate(form) {
        //TODO 1: implement JavaScript validation
        //ensure it returns false for an error and true for success

         return true;
    }
</script>


<?php
//TODO 2: add PHP Code
if(isset($_GET['message']) && $_GET['message'] == 'logged_out') {
    echo "<br> <p>Successfully Logged OUT!</p>";
    }
if(isset($_GET['message']) && $_GET['message'] == 'registered') {
    echo "Successfully Registered";
    }
if (isset($_POST["email"]) && isset($_POST["password"])) {
    $email = se($_POST, "email", "", false);
    $password = se($_POST, "password", "", false);

    //TODO 3
    $hasError = false;
    if (empty($email)) {
        echo "Email must not be empty";
        $hasError = true;
    }
    //sanitize
    $email = sanitize_email($email);
    //validate
    if (empty($email)) {
        echo "Invalid email address";
        $hasError = true;
    }
    if (empty($password)) {
        echo "password must not be empty";
        $hasError = true;
    }
    if (strlen($password) < 8) {
        echo "Password too short";
        $hasError = true;
    }
    if (!$hasError) {
        //TODO 4
        $db = getDB();
        $stmt = $db->prepare("SELECT email, password, username, id from Users where email = :email OR username = :email");
        try {
            $r = $stmt->execute([":email" => $email]);
            if ($r) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($user) {
                    $hash = $user["password"];
                    unset($user["password"]);
                    if (password_verify($password, $hash)) {
                        echo "Weclome $email";
                        $_SESSION["user"] = $user;
                        try {
                            //lookup potential roles
                            $stmt = $db->prepare("SELECT Roles.name FROM Roles 
                        JOIN UserRoles on Roles.id = UserRoles.role_id 
                        where UserRoles.user_id = :user_id and Roles.is_active = 1 and UserRoles.is_active = 1");
                            $stmt->execute([":user_id" => $user["id"]]);
                            $roles = $stmt->fetchAll(PDO::FETCH_ASSOC); //fetch all since we'll want multiple
                        } catch (Exception $e) {
                            error_log(var_export($e, true));
                        }
                        //save roles or empty array
                        if (isset($roles)) {
                            $_SESSION["user"]["roles"] = $roles; //at least 1 role
                        } else {
                            $_SESSION["user"]["roles"] = []; //no roles
                        }
                        die(header("Location: home.php"));
                    } else {
                        echo "Invalid password";
                    }
                } else {
                    echo "Email not found";
                }
            }
        } catch (Exception $e) {
            echo "<pre>" . var_export($e, true) . "</pre>";
        }
    }
}
?>

<style>
    form{
        font-family:serif;
        width: 95%;
        margin: 0 auto;
        color:white;
    }
    p {
        font-family:serif;
        width: 95%;
        margin: 0 auto;
        color:white;
    }
    input[type="submit"]{ /*login button*/
        color: white;
        text-align: center;
        padding: 10px 15px;
        background-color: #1C3144;
        font-size: 16px;
        font-family:serif;
    }

    input[type="submit"]:hover:not(.active) { /*hovering over login button*/
        background-color: #162736;
    }

    input[type=text] { /*email/username field*/
        padding: 3px 5px;
        margin: 8px 0;
        box-sizing: border-box;
    }

    input[type=password] { /*password field*/
        padding: 3px 5px;
        margin: 8px 0;
        box-sizing: border-box;
    }
</style>