<?php
ob_start();
//note we need to go up 1 more directory
require(__DIR__ . "/partials/nav.php");

if(isset($_GET['id']) && has_role("Admin")){
    $id = $_GET['id'];
}
else{
    die(header("Location:inventory.php"));
}
?>

</br>

<h2>Update Products</h2>

<?php
$db = getDB();

$stmt = $db->prepare("SELECT * from Products WHERE id = :id");

$r = $stmt->execute([':id'=> $id]);

if($r) {
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    $name = $product['name'];
    $description = $product['description'];
    $category = $product['category'];
    $stock = $product['stock'];
    $unit_price = $product['unit_price'];
    $visibility = $product['visibility'];
}

?>
<form method="GET">
    <input type="hidden" name="id" value="<?php echo $id; ?>" />
    Name: <input type = "text" name = "name" value = "<?php echo $name?>"/>
<br /> 
    Description: <input type = "text" name = "description" value = "<?php echo $description?>"/>
</br />
<label for="category">Category:</label>
    <select name="category" id="category">
        <?php
        $categories = ['Indoor OutDoor Cooling', 'Cranes (any)', 'Mining Trucks', 'Cruise Ships'];
        foreach ($categories as $c) {
            $selected = ($c == $product['category']) ? 'selected' : ''; 
            echo '<option value="' . $c . '" ' . $selected . '>' . $c . '</option>';
        }
        ?>
    </select>
</br />
    Stock: <input type = "text" name = "stock" value = "<?php echo $stock?>"/>
</br />
    Price: <input type = "text" name = "unit_price" value = "<?php echo $unit_price?>"/>
</br />
    Visibility:
    <input type="hidden" name="visibility" value="0">
    <label class="switch" >
        <input type="checkbox" name = "visibility" value="1" <?php echo ($visibility == 1) ? 'checked' : ''; ?>>
        <span class="slider round"></span>
    </label>

</br />
<br>
<input type = "submit" value = "Update Item"/>
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
        $stmt = $db->prepare("UPDATE Products SET name = :name, description = :description, category = :category, 
                            stock = :stock, unit_price = :unit_price, visibility = :visibility WHERE id = :id");
        try {
            $stmt->execute([":name" => $name, 
                            ":description" => $description, 
                            ":category" => $category, 
                            ":stock" => $stock, 
                            ":unit_price" => $unit_price, 
                            ":visibility" => $buttonVisibility,
                            ":id" => $id
                            ]);
            //echo "Successfully registered!";
            die(header("Location: inventory.php"));
        } catch (Exception $e) {
            echo "There was a problem registering (SQL)";
            "<pre>" . var_export($e, true) . "</pre>";
            echo "Error message: " . $e->getMessage() . "<br>"; // Display the error message
            echo "SQL State: " . $e->getCode() . "<br>";
        }
    }
}
?>


<?php
ob_end_flush();
?>

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

    tr{
        font-family:serif;
        margin: 0 auto;
        color:white;
    }

    td{
        font-family:serif;
        margin: 0 auto;
        color:white;
    }

    tr:nth-child(even) {
    background-color: #8c98a6;
    }

    input[type="submit"]{ /*update button*/
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
</style>


