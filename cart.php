<?php
require(__DIR__ . "/partials/nav.php");

if(!is_logged_in()){
    echo "You're not logged in";
    die(header("Location: login.php"));
}
if(isset($_GET['message']) && $_GET['message'] == 'cart_updated') {
    echo "<br> <p>Cart successfully updated!</p>";
    }
if(isset($_GET['message']) && $_GET['message'] == 'negative') {
    echo "<br> <p>Quantity can not be negative!</p>";
    }
if(isset($_GET['message']) && $_GET['message'] == 'cart_cleared') {
    echo "<br> <p>Cart Cleared!</p>";
    }
if(isset($_GET['message']) && $_GET['message'] == 'cart_item_cleared') {
    echo "<br> <p>Cart Item Removed!</p>";
    }
//get user id
$user_data = var_export($_SESSION, true);

$user = $_SESSION['user'];
$user_id = $user['id'];
//echo "ID: " . htmlspecialchars($user['id']) . "</br>";

//find matching row in table
$db = getDB();
?>
<br>
<h2> Cart </h2>
<?php

$stmt = $db->prepare("SELECT Cart.id, Cart.product_id, Cart.desired_quantity, Cart.unit_price, Products.name 
                        FROM Cart 
                        JOIN Products ON Cart.product_id = Products.id 
                        WHERE Cart.user_id = :user_id");

$r = $stmt->execute([':user_id'=> $user_id]);

//print table 
$total_price = 0;
if ($r) {
    try {
        $cart = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if(count($cart) > 0) {
            echo "
                <table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <!--<th>Description</th>-->
                    <th>Price</th>
                    <th>SubTotal</th>
                    <th>Quantity</th>
                </tr>";
            foreach ($cart as $cartItem) {
                $subTotal = $cartItem['unit_price'] * $cartItem['desired_quantity'];
                $total_price += $subTotal;
    
                echo '<tr>';
                echo '<td>' . $cartItem['id'] . '</td>';
                echo '<td><a href="item.php?id=' . $cartItem['id'] . '">' . $cartItem['name'] . '</a></td>';
                //echo '<td><input type="number" name="desired_quantity" value="' . $cartItem['desired_quantity'] . '" min="0"></td>';
                echo '<td>' . $cartItem['unit_price'] . '</td>';
                echo '<td>' . $subTotal . '</td>';
                echo '<td>';
    
                echo '<form method="POST" action="cart_updater.php" style="display:inline;">';
                echo '<input type="hidden" name="product_id" value="' . $cartItem['product_id'] . '">';
                echo '<input type="number" name="desired_quantity" value="' . $cartItem['desired_quantity'] . '" min="0">';
                echo '<button type="submit">Update</button>';
                echo '</form>';
    
                echo '<button onclick="removeCartItem(' . $user_id . ', ' . $cartItem['product_id'] . ')">Remove</button>';
                echo '</td>';
                echo '</tr>';
            }
        }else {
            echo "<br><p>Your cart is empty</p>";
        }

    } catch (Exception $e) {
        echo "There was a problem fetching the cart.";
        echo "<pre>" . var_export($e, true) . "</pre>";
    }
}
?>
</table>
<h1><td> <button onclick="removeAllItems(<?php echo $user_id; ?>)"> Clear Entire Cart </button>  </td></h1>

<?php 
echo "<p> Total Price: " . $total_price . "</p>";
?>

<h1><td> <button onclick="goBack()"> Go Back </button>  </td></h1>


<script>
    function goBack(){
        window.location.href = "shop.php";
    }

    function removeAllItems(id){
        console.log("HELP!!!!!!!!" + id);
        window.location.href = "cart_updater.php?id="+id+"&message=clearCart";
    }

    function removeCartItem(id, product_id){
        console.log(id, product_id);
        window.location.href = "cart_updater.php?id="+id+"&message=removeCartItem&product_id=" + product_id;
    }
</script>

<style>
    h2, p{
        font-family:serif;
        width: 95%;
        margin: 0 auto;
        color:white;
    }

    tr, td, td a{
        font-family:serif;
        padding: 4px 4px;
        margin: 0 auto;
        color:white;
    }

    tr:nth-child(even) {
    background-color: #8c98a6;
    }

    button{ /*update button*/
        color: white;
        text-align: center;
        padding: 4px 5px;
        background-color: #1C3144;
        font-size: 16px;
        font-family:serif;
    }

    input[type="submit"]:hover:not(.active) { /*hovering over add item button*/
        background-color: #162736;
    }

    td button:hover:not(.active) { /*hovering over update button button*/
        background-color: #162736;
    }

    button:hover:not(.active) { /*hovering over update button button*/
        background-color: #162736;
    }

    input[type=text] {
        width: 95%;
        padding: 3px 5px;
        margin: 8px 0;
        box-sizing: border-box;
    }
</style>
