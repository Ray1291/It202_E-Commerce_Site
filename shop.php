<?php
require(__DIR__ . "/partials/nav.php");

//RJL
//11/29/2024

if(has_role('Admin')){//public shop button
    echo '<button onclick="adminshop()"> Admin Shop </button>';
}


?>
<br>
<h2>SHOP</h2>

<form method="GET">
    <!-- Filter: -->
    </br />
    <label class="switch" >
        Indoor/OutDoor Cooling
        <input type="checkbox" name = "cooling">
        <span class="slider round"></span>
    </label>
</br />
    <label class="switch" >
        Cranes (any)
        <input type="checkbox" name = "cranes">
        <span class="slider round"></span>
    </label>
</br />
    <label class="switch" >
        Mining Trucks
        <input type="checkbox" name = "miningTrucks">
        <span class="slider round"></span>
    </label>
</br />
    <label class="switch" >
        Cruise Ships
        <input type="checkbox" name = "cruiseShips">
        <span class="slider round"></span>
    </label>
</br />
<label for="limit">Limit: </label>
    <select name="limit" id="limit">
    <option value="10" selected> Default (10) </option>
        <?php
            for($i = 1; $i < 50; $i++){
                echo $i;
                echo "<option value= ". $i .">". $i ."</option>";
            }
        ?>
    </select>
        </br/>
<label for="unit_price">Price:</label>
    <select name="unit_price" id="unit_price">
        <option value= "Highest to Lowest" <?php  if(isset($_GET['unit_price']) && $_GET['unit_price'] == "Highest to Lowest") echo " selected"; ?> > Highest to Lowest</option>
        <option value= "Lowest to Highest" <?php  if(isset($_GET['unit_price'] )&& $_GET['unit_price'] == "Lowest to Highest") echo " selected"; ?>> Lowest to Highest</option>
        <option value= "No Order" <?php  if(isset($_GET['unit_price']) && $_GET['unit_price'] == "No Order") echo " selected"; ?>> No Order</option>
    </select>
    Search by name: <input type = "text" name = "name" />
</br />
<input type = "submit" value = "Filter"/>
</form>

<table>
  <tr>
    <th>ID</th>
    <th>Name</th>
    <!--<th>Description</th>-->
    <th>Category</th>
    <!--<th>Stock</th>-->
    <th>Price</th>
  </tr>

<?php

$filters = [];

if(isset($_GET['cooling'])) $filters[] = "category = 'Indoor Outdoor Cooling'";
if(isset($_GET['cranes'])) $filters[] = "category = 'Cranes (any)'";
if(isset($_GET['miningTrucks'])) $filters[] = "category = 'Mining Trucks'";
if(isset($_GET['cruiseShips'])) $filters[] = "category = 'Cruise Ships'";
if(isset($_GET['name'])) {
    $name = $_GET['name'];
}
$order_direction = "";
if(isset($_GET['unit_price'])) {
    $order_direction = $_GET['unit_price'];
}
if(isset($_GET['limit'])) {
    $limit = $_GET['limit'];
}


$db = getDB();
try {
    //Construct $const part 
    $const = "SELECT * FROM Products";

    //Construct $where part
    if(!empty($filters)){
        $where = implode(' OR ', $filters);
        $where = " WHERE (" . $where . ") AND name LIKE '" . $name . "%'";

    } else{
        $where = "";
        if(!empty($name)){
            $where = " WHERE name LIKE '" . $name . "%'";
        }
    }

    //construct order statement
    $order = "";
    if($order_direction == "Lowest to Highest") {
        $order = " ORDER BY unit_price ASC "; //l to h
    }
    if($order_direction == "Highest to Lowest"){
        $order = " ORDER BY unit_price DESC "; //h to l
    }
    if($order_direction == "No Order") {
        $order = " ";
    }

    //construct limit amount
    $limits = "";
    if(!empty($limit)) {
        $limits = " LIMIT " .$limit;
    }
    else{
        $limit = " LIMIT 10";
    }

    $query = $const . $where . $order . $limits;
    //echo $query;
    $stmt = $db->prepare($query);
    $r = $stmt->execute();
    
    if ($r) {
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach($products as $product){
            if($product['visibility'] == 1) {
                echo '<tr>';
                echo '<td>' . $product['id'].'</td>';
                echo '<td><br><a href="item.php?id=' . $product['id'] . '"/> ' . $product['name'].'</a> </td>';
                //echo '<td>' . $product['description'].'</td>';
                echo '<td>' . $product['category'].'</td>';
                //echo '<td>' . $product['stock'].'</td>';
                echo '<td>' . $product['unit_price'].'</td>';
                if(has_role('Admin')){
                    echo '<td> <button onclick="updateCar('.  $product['id'] .')"> Update </button>  </td>';
                }
                echo '</tr>';
            }
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
        window.location.href = "shop_updater.php?id="+id + "";
    }

    function adminshop(){
        window.location.href = "adminshop.php";
    }

</script>


<style>
    form{
        font-family:serif;
        width: 95%;
        margin: 0 auto;
        color:white;
    }
    h2{
        font-family:serif;
        width: 95%;
        margin: 0 auto;
        color:white;
    }

    tr, td, td a{
        font-family:serif;
        margin: 0 auto;
        padding: 3px 4px;
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
    
    td button, button{ /*update button*/
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
