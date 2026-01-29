<?php
require(__DIR__ . "/partials/nav.php");

if (!is_logged_in() ) {
    echo "You're not logged in";
    die(header("Location: login.php"));
}

if (!has_role("Admin")) {
    echo "You're do not have permission to view this site";
    die(header("Location: shop.php"));
}

if(isset($_GET['message']) && $_GET['message'] == 'clearCart') {
    if (isset($_GET['id'])) {
        $user_id = $_GET['id']; 
        //echo "User ID: " . $usid; 
        clearCart($user_id);
    }
}
if(isset($_GET['message']) && $_GET['message'] == 'removeCartItem') {
    if (isset($_GET['id']) && isset($_GET['product_id'])) {
        $user_id = $_GET['id'];
        $product_id = $_GET['product_id']; 
        echo "User ID: " . $user_id; 
        removeCartItem($user_id, $product_id);
    }
}

//rjl
//12/12/2024
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = getDB();

    $product_id = $_POST['product_id'];
    $desired_quantity = $_POST['desired_quantity'];
    try {
        if($desired_quantity == 0){
            $stmt = $db->prepare("DELETE FROM Cart WHERE product_id = :product_id");
            $stmt->execute([':product_id' => $product_id]);
            die(header("Location: cart.php?message=cart_updated"));
        }
        elseif($desired_quantity < 0){
            die(header("Location: cart.php?message=negative"));
        }

        $stmt = $db->prepare("UPDATE Cart SET desired_quantity = :desired_quantity WHERE product_id = :product_id");
        $stmt->execute([':desired_quantity' => $desired_quantity, ':product_id' => $product_id]);

        die(header("Location: cart.php?message=cart_updated"));
    } catch (Exception $e) {
        echo "Error updating quantity.";
        echo "<pre>" . var_export($e, true) . "</pre>";
    }
}


function clearCart($user_id){
    $db = getDB();

    try {

        $stmt = $db->prepare("DELETE FROM Cart WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $user_id]);

        die(header("Location: cart.php?message=cart_cleared"));
    } catch (Exception $e) {
        echo "Error updating quantity.";
        echo "<pre>" . var_export($e, true) . "</pre>";
    }
}

function removeCartItem($user_id, $product_id){
    $db = getDB();

    try {

        $stmt = $db->prepare("DELETE FROM Cart WHERE user_id = :user_id && product_id = :product_id");
        $stmt->execute([':user_id' => $user_id, ':product_id' => $product_id]);

        die(header("Location: cart.php?message=cart_item_cleared"));
    } catch (Exception $e) {
        echo "Error updating quantity.";
        echo "<pre>" . var_export($e, true) . "</pre>";
    }
}
?>