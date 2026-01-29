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
?>

</br>
<h2>Inventory</h2>


<form method="GET">
    Name: <input type = "text" name = "name"/>
<br /> 
    Description: <input type = "text" name = "description"/>
</br />
<label for="category">Category:</label>
    <select name="category" id="category">
        <?php
        $categories = ['Indoor OutDoor Cooling', 'Cranes (any)', 'Mining Trucks', 'Cruise Ships'];
        foreach ($categories as $category) {
            echo '<option value="' . $category . '">' . $category . '</option>';
        }
        ?>
    </select>
</br />
    Stock: <input type = "text" name = "stock"/>
</br />
    Price: <input type = "text" name = "unit_price"/>
</br />
    Visibility:
    <label class="switch" >
        <input type="checkbox" name = "visibility">
        <span class="slider round"></span>
    </label>

</br />
</br />

<input type = "submit" value="Add Item"/>
</form>

<?php
if (isset($_GET["name"]) && isset($_GET["description"]) && isset($_GET["category"]) && isset($_GET["stock"]) && isset($_GET["unit_price"]) && isset($_GET["visibility"])) {
    $name = $_GET['name'];
    $description = $_GET['description'];
    $category = $_GET['category'];
    $stock = $_GET['stock'];
    $unit_price = $_GET['unit_price'];

    if(isset($_GET['visibility']) && $_GET['visibility'] == '1') {
        $buttonVisibility = 1;
    } else {
        $buttonVisibility = 0;
    }

    // echo $name. "<br>";
    // echo $description. "<br>";
    // echo $category. "<br>";
    // echo $stock. "<br>";
    // echo $unit_price. "<br>";

    $has_error = false;

    if(!is_numeric($unit_price) || $unit_price <= 0) {
        echo "Price must be more than 0.00";
        $has_error = true;
    }

    if(!is_numeric($stock) || $stock < 0) {
        echo "Stock must be a positive number";
        $has_error = true;
    }

    if(!$has_error) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO Products (name, description, category, stock, unit_price, visibility) 
                                  VALUES(:name, :description, :category, :stock, :unit_price, :visibility)");
        try {
            $stmt->execute([":name" => $name, 
                            ":description" => $description, 
                            ":category" => $category, 
                            ":stock" => $stock, 
                            ":unit_price" => $unit_price, 
                            ":visibility" => $buttonVisibility
                            ]);
            echo "Successfully registered!";
        } catch (Exception $e) {
            echo "There was a problem registering (SQL)";
            "<pre>" . var_export($e, true) . "</pre>";
            echo "Error message: " . $e->getMessage() . "<br>"; // Display the error message
            echo "SQL State: " . $e->getCode() . "<br>";
        }
    }
}
?>


<table>
  <tr>
    <th>ID</th>
    <th>Name</th>
    <th>Description</th>
    <th>Category</th>
    <th>Stock</th>
    <th>Price</th>
    <th>Visibility</th>
  </tr>

<?php

$db = getDB();
$stmt = $db->prepare("SELECT name, description, category, stock, unit_price, id, visibility from Products");
//rjl
//11/30/2024
try {
    $r = $stmt->execute();
    if ($r) {
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach($products as $product){
            echo '<tr>';
            echo '<td>' . $product['id'].'</td>';
            echo '<td>' . $product['name'].'</td>';
            echo '<td>' . $product['description'].'</td>';
            echo '<td>' . $product['category'].'</td>';
            echo '<td>' . $product['stock'].'</td>';
            echo '<td>' . $product['unit_price'].'</td>';
            echo '<td>' . $product['visibility'].'</td>';
            if(has_role('Admin')){ //update button
                echo '<td> <button onclick="updateCar('.  $product['id'] .')"> Update </button>  </td>';
            }

            
            echo '</tr>';
        }
    }
} catch (Exception $e) {
    echo "There was a problem registering (TABLE)";
    "<pre>" . var_export($e, true) . "</pre>";
}

?>
</table>

<script>

    function updateCar(id){
        console.log(id);
        window.location.href = "inventory_updater.php?id="+id;
    }

</script>

<style>
    form{
        font-family:serif;
        width: 95%;
        margin: 0 auto;
        color:white;
    }
    h2 {
        font-family:serif;
        width: 95%;
        margin: 0 auto;
        color:white;
    }

    tr, td{
        font-family:serif;
        padding: 3px 4px;
        margin: 0 auto;
        color:white;
    }


    tr:nth-child(even) {
    background-color: #8c98a6;
    }

    input[type="submit"]{ /*add item button*/
        color: white;
        text-align: center;
        padding: 10px 15px;
        background-color: #1C3144;
        font-size: 16px;
        font-family:serif;
    }
    
    td button{ /*update button*/
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

    input[type=text] {
        padding: 3px 5px;
        margin: 8px 0;
        box-sizing: border-box;
    }
</style>


