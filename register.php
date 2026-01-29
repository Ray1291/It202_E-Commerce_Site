<?php
require(__DIR__ . "/partials/nav.php");
?>
<form onsubmit="return validate(this)" method="POST">
    <div>
    <br>
        <label for="email">Email</label>
        <br>
        <input type="email" value="<?= (isset($_POST['email'])) ? strip_tags($_POST['email']) : '' ?>" name="email" required />
    </div>
    <div>
        <label for="user">Username</label>
        <br>
        <input type="text" value="<?= (isset($_POST['user'])) ? strip_tags($_POST['user']) : '' ?>" name="user" required />
    </div>
    <div>
        <label for="pw">Password</label>
        <br>
        <input type="password" id="pw" name="password" required minlength="8" />
    </div>
    <div>
        <label for="confirm">Confirm</label>
        <br>
        <input type="password" name="confirm" required minlength="8" />
    </div>
    <br>
    <input type="submit" value="Register" />
</form>
<script>
    function validate(form) {
        //TODO 1: implement JavaScript validation
        //ensure it returns false for an error and true for success

        return true;
    }
</script>

<style>
    form{
        font-family:serif;
        width: 95%;
        margin: 0 auto;
        color:white;
    }
    input[type="submit"]{ /*register button*/
        color: white;
        text-align: center;
        padding: 10px 15px;
        background-color: #1C3144;
        font-size: 16px;
        font-family:serif;
    }

    input[type="submit"]:hover:not(.active) { /*hovering over register button*/
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

    input[type=email] { /*password field*/
        padding: 3px 5px;
        margin: 8px 0;
        box-sizing: border-box;
    }
</style>
<?php
//TODO 2: add PHP Code
if (isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["confirm"])) {
    $email = se($_POST, "email", "", false);
    $password = se($_POST, "password", "", false);
    $username = $_POST['user'];
    $confirm = se(
        $_POST,
        "confirm",
        "",
        false
    );
    //TODO 3
    $hasError = false;
    if (empty($email)) {
        echo "Email must not be empty";
        $hasError = true;
    }
    //sanitize
    $email = sanitize_email($email);
    //validate
    if (!is_valid_email($email)) {
        echo "Invalid email address";
        $hasError = true;
    }
    if (empty($password)) {
        echo "password must not be empty";
        $hasError = true;
    }
    if (empty($confirm)) {
        echo "Confirm password must not be empty";
        $hasError = true;
    }
    if (strlen($password) < 8) {
        echo "Password too short";
        $hasError = true;
    }
    if (
        strlen($password) > 0 && $password !== $confirm
    ) {
        echo "Passwords must match";
        $hasError = true;
    }
    if (checkEmail($email) == true) {
            echo " Email already Used, please try again";
            $hasError = true;
    }
    if (checkUsername($username) == true) {
        echo " Username already Used, please try again";
        $hasError = true;
}
    if (!$hasError) {
        echo "Welcome, $email";
        //TODO 4
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO Users (email, password, username) VALUES(:email, :password, :username)");
        try {
            $stmt->execute([":email" => $email, ":password" => $hash, ":username" => $username]);
            header("Location: login.php?message=registered");
            
        } catch (Exception $e) {
            echo "There was a problem registering";
            "<pre>" . var_export($e, true) . "</pre>";
        }
    }
}
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
?>