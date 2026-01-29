<?php
require_once(__DIR__ . "/partials/nav.php");
if (!is_logged_in()) {
    die(header("Location: login.php"));
}
?>
<?php

function checkEmail($email) { 
    $db = getDB();

    $stmt = $db->prepare("SELECT email FROM Users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $retrievedEmail = $stmt->fetchColumn();
    if($retrievedEmail == $email){
        return true;
    }
    else{
        return false;
    }
}
function checkUsername($username) { 
    $db = getDB();

    $stmt = $db->prepare("SELECT username FROM Users WHERE username = :username");
    $stmt->execute([':username' => $username]);
    $retrievedName = $stmt->fetchColumn();
    if($retrievedName == $username){
        return true;
    }
    else{
        return false;
    }
}

if (isset($_POST["save"])) {
    $email = se($_POST, "email", null, false);
    $username = se($_POST, "username", null, false);

    $params = [":email" => $email, ":username" => $username, ":id" => get_user_id()];
    $db = getDB();
    $stmt = $db->prepare("UPDATE Users set email = :email, username = :username where id = :id");
    $hasError = false;

    $userCurrentUsername = $_SESSION['user']['username'];
    $userCurrentEmail = $_SESSION['user']['email'];

    if (checkEmail($email)  && $email !== $userCurrentEmail) {
        echo " Email already Used, please try again";
        $hasError = true;
    }
    if (checkUsername($username) && $username !== $userCurrentUsername) {
        echo " Username already Used, please try again";
        $hasError = true;
    }
    if(!$hasError){
        try {
            $stmt->execute($params);
            echo "Profile saved";
        } catch (Exception $e) {
            if ($e->errorInfo[1] === 1062) {
                //https://www.php.net/manual/en/function.preg-match.php
                preg_match("/Users.(\w+)/", $e->errorInfo[2], $matches);
                if (isset($matches[1])) {
                    echo "The chosen " . $matches[1] . " is not available.";
                } else {
                    //TODO come up with a nice error message
                    echo "<pre>" . var_export($e->errorInfo, true) . "</pre>";
                }
            } else {
                //TODO come up with a nice error message
                echo "<pre>" . var_export($e->errorInfo, true) . "</pre>";
            }
        }
    }

    
    
    //select fresh data from table
    $stmt = $db->prepare("SELECT id, email, username from Users where id = :id LIMIT 1");
    try {
        $stmt->execute([":id" => get_user_id()]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            //$_SESSION["user"] = $user;
            $_SESSION["user"]["email"] = $user["email"];
            $_SESSION["user"]["username"] = $user["username"];
        } else {
            echo "User doesn't exist";
        }
    } catch (Exception $e) {
        echo "An unexpected error occurred, please try again";
        //echo "<pre>" . var_export($e->errorInfo, true) . "</pre>";
    }


    //check/update password
    $current_password = se($_POST, "currentPassword", null, false);
    $new_password = se($_POST, "newPassword", null, false);
    $confirm_password = se($_POST, "confirmPassword", null, false);
    if (!empty($current_password) && !empty($new_password) && !empty($confirm_password)) {
        if ($new_password === $confirm_password) {
            //TODO validate current
            $stmt = $db->prepare("SELECT password from Users where id = :id");
            try {
                $stmt->execute([":id" => get_user_id()]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if (isset($result["password"])) {
                    if (password_verify($current_password, $result["password"])) {
                        $query = "UPDATE Users set password = :password where id = :id";
                        $stmt = $db->prepare($query);
                        $stmt->execute([
                            ":id" => get_user_id(),
                            ":password" => password_hash($new_password, PASSWORD_BCRYPT)
                        ]);

                        echo "Password reset";
                    } else {
                        echo "Current password is invalid";
                    }
                }
            } catch (Exception $e) {
                echo "<pre>" . var_export($e->errorInfo, true) . "</pre>";
            }
        } else {
            echo"New passwords don't match";
        }
    }
}

?>


<?php
$email = get_user_email();
$username = get_username();
?>
<form method="POST" onsubmit="return validate(this);">
    <div class="mb-3">
        <br>
        <label for="email">Email</label>
        <br>
        <input type="email" name="email" id="email" value="<?php se($email); ?>" />
    </div>
    <div class="mb-3">
        <label for="username">Username</label>
        <br>
        <input type="text" name="username" id="username" value="<?php se($username); ?>" />
    </div>
    <!-- DO NOT PRELOAD PASSWORD -->
    <div>Password Reset</div>
    <br>
    <div class="mb-3">
        <label for="cp">Current Password</label>
        <br>
        <input type="password" name="currentPassword" id="cp" />
    </div>
    <div class="mb-3">
        <label for="np">New Password</label>
        <br>
        <input type="password" name="newPassword" id="np" />
    </div>
    <div class="mb-3">
        <label for="conp">Confirm Password</label>
        <br>
        <input type="password" name="confirmPassword" id="conp" />
    </div>
    <input type="submit" value="Update Profile" name="save" />
</form>

<script>
    function validate(form) {
        let pw = form.newPassword.value;
        let con = form.confirmPassword.value;
        let isValid = true;
        //TODO add other client side validation....

        //example of using flash via javascript
        //find the flash container, create a new element, appendChild
        if (pw !== con) {
            //find the container
            let flash = document.getElementById("flash");
            //create a div (or whatever wrapper we want)
            let outerDiv = document.createElement("div");
            outerDiv.className = "row justify-content-center";
            let innerDiv = document.createElement("div");

            //apply the CSS (these are bootstrap classes which we'll learn later)
            innerDiv.className = "alert alert-warning";
            //set the content
            innerDiv.innerText = "Password and Confirm password must match";

            outerDiv.appendChild(innerDiv);
            //add the element to the DOM (if we don't it merely exists in memory)
            flash.appendChild(outerDiv);
            isValid = false;
        }
        return isValid;
    }
</script>

<style>
    div[text = "Password Reset"]{
        font-size:20px;
    }
    form{
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

    input[type=email] { /*email/username field*/
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