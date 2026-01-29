<?php
require(__DIR__ . "/partials/nav.php");

if(isset($_GET['id'])){
    $id = $_GET['id'];
}
else{
    die(header("Location:shop.php"));
}
echo " <h1><td> <button onclick='goBack()'> Go Back </button>  </td></h1>";
$db = getDB();

$stmt = $db->prepare("SELECT * from Products WHERE id = :id");

$r = $stmt->execute([':id'=> $id]);

if ($r) {
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo '<h2>' . $products[0]['name'].'</h2>';
}


    ?>


<br>
<table>
    <tr>
        <th>ID</th>
        <th>Description</th>
        <th>Category</th>
        <th>Stock</th>
        <th>Price</th>
    </tr>
    <tr>
        <td><?php echo $products[0]['id']; ?></td>
        <td><?php echo $products[0]['description']; ?></td>
        <td><?php echo $products[0]['category']; ?></td>
        <td><?php echo $products[0]['stock']; ?></td>
        <td><?php echo $products[0]['unit_price']; ?></td>
    </tr>
</table>

<form method="POST">
<label for="quantity">Quantity: </label>
    <select name="quantity" id="quantity">
        <?php
            for($i = 1; $i < 50; $i++){
                echo $i;
                if($i == 1) {
                    echo "<option value= '". $i ."'selected>". $i ."</option>";
                    continue;
                }
                echo "<option value= ". $i .">". $i ."</option>";
            }
        ?>
    </select><br><br><br><p>
        <?php
        if(is_logged_in()){
            echo '<input type = "submit" value = "Add to Cart"/></p>';
        }
        else {
            echo '<p> Please login in order to add to the cart</p>';
        }
        ?>
</form>
<?php
/* ADDDING ITEMS TO CART */
$desired_quantity = 0;

if(isset($_POST['quantity']) && is_logged_in()) { //getting quantity
    $desired_quantity = $_POST['quantity'];
    $user_data = var_export($_SESSION, true);
    
    $user = $_SESSION['user'];
        
    $has_error = false;
    echo $has_error; 
    
    try{
        if($products[0]["stock"] < $desired_quantity) {
            echo "<p>Stock less than quantity selected<p>";
            $has_error = true;
        }
        
        $product_id = $products[0]['id']; //product id
        $user_id = $user['id']; //user id
        $unit_price = $products[0]['unit_price']; //unit price
        
        // echo $product_id . "</br/>"; 
        // echo $user_id . "</br/>";
        // echo $desired_quantity . "</br/>";
        // echo $unit_price . "</br/>";
            
        if(!$has_error){
            $db = getDB();

            $query = "INSERT INTO Cart (product_id, user_id, desired_quantity, unit_price) 
                                VALUES(:product_id, :user_id, :desired_quantity, :unit_price)";

            $stmt = $db->prepare($query);
        
            $stmt->execute([":product_id" => $product_id, 
                            ":user_id" => $user_id, 
                            ":desired_quantity" => $desired_quantity, 
                            ":unit_price" => $unit_price
                            ]);
            echo "<h2>Successfully added to cart!</h2>";
        }

    } catch (Exception $e) {
        echo "There was an error adding to the cart";
        "<pre>" . var_export($e, true) . "</pre>";
    }    
} 

//rjl
//12/2/2024

if(has_role('Admin')){ //update button
    echo '<br> <p><button onclick="updateCar('.  $products[0]['id'] .')"> Update </button>  </>';
}

?>



<script>
    function updateCar(id){
        console.log(id);
        window.location.href = "inventory_updater.php?id="+id + "";
    }
    function goBack(){
        window.location.href = "shop.php";
    }
</script>

<style>
    tr{
        font-family:serif;
        width: 95%;
        margin: 0 auto;
        color:white;
    }

    h2, label, p{
        font-family:serif;
        width: 95%;
        margin: 0 auto;
        color:white;
    }

    button,input[type="submit"]{ /*go back  button */
        color: white;
        text-align: center;
        padding: 4px 5px;
        
        background-color: #1C3144;
        font-size: 16px;
        font-family:serif;
    }
</style>