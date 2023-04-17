<?php

function db_connect() {

    // Establishes location of database
    $host = "localhost";
    $user = "root";
    $pass = "";
    $data = "gameworld";

    // Makes a connection
    $mysqli = new mysqli($host, $user, $pass, $data);

    if($mysqli->connect_errno) // In case of an error
    {
        die("Connection " . $data . " failed");
    }

    return $mysqli;

}

// Gets the navbar items from the database
function getNavbar() {
    $db = db_connect();

    // Gets all information from page table
    $sql = "SELECT * FROM nav";

    // Executes the query with the connection
    $resource = $db->query($sql) or die($db->error);

    // Puts information into array
    $nav = $resource->fetch_all(MYSQLI_ASSOC);

    // Closes connection
    $db->close();

    return $nav;
}

function displayNavbar() {
    $pages = getNavbar();

    ?>
        <header>
            <div class="logo"><a href="index.php">GameWorld</a></div>
            <nav class="navigation">
                <ul>
                    <?php foreach($pages as $page) {?>
                        <li><a href="<?php echo $page["navDir"]?>"><?php echo $page["navName"];?></a></li>
                    <?php } ?>
                </ul>
            </nav>

        </header>
  <?php

}

function getCategories() {

    $db = db_connect();

    // Gets all information from page table
    $sql = "SELECT * FROM categories";

    // Executes the query with the connection
    $resource = $db->query($sql) or die($db->error);

    // Puts information into array
    $cat = $resource->fetch_all(MYSQLI_ASSOC);

    // Closes connection
    $db->close();

    return $cat;

}

function displayCategories() {

    $categories = getCategories();

    ?>
    <div class="category-container">

<?php
    foreach ($categories as $category) {
        ?>

    <div class="category">
        <img src="<?php echo $category['catImg']; ?>" alt="<?php echo $category['catName']; ?>">
        <h2><?php echo $category['catName']; ?></h2>
        <a href="products.php?catId=<?php echo $category["catId"]?>" class="btn">Shop <?php echo $category['catName']; ?></a>
    </div>
        <?php } ?>
    </div>

    </div>

<?php
}

function getProducts($catId) {

        $db = db_connect();

        // Gets all information from page table
        $sql = "SELECT * FROM products WHERE catId=" . $catId;

        // Executes the query with the connection
        $resource = $db->query($sql) or die($db->error);

        // Puts information into array
        $product = $resource->fetch_all(MYSQLI_ASSOC);

        // Closes connection
        $db->close();

        return $product;


}

function displayProducts($catId) {

    $products = getProducts($catId);

    foreach($products as $product) {
    ?>
    <div class="product">
                    <div class="product-image">
                        <img src="<?php echo $product["prodImg"]?>" class="image-size"/>
                    </div>
                    <div class="product-details">
                      <h1><?php echo $product["prodName"]?></h1>
                      <p class="price"><?php echo $product["prodPrice"]?></p>
                        <a href="products.php?prodId=<?php echo $product["prodId"]?>">goagkfeg</a>

                        </form>
                    </div>
                  </div>

        <?php
    }
}

function getProduct($prodId) {

        $db = db_connect();

        // Gets all information from page table
        $sql = "SELECT * FROM products WHERE prodId=" . $prodId;

        // Executes the query with the connection
        $resource = $db->query($sql) or die($db->error);

        // Puts information into array
        $product = $resource->fetch_all(MYSQLI_ASSOC);

        // Closes connection
        $db->close();

        return $product;



}

function displayProduct($prodId) {

    $product = getProduct($prodId);

    ?>
        <div class="product-page-container">
    <div class="left-container">
        <img src="<?php echo $product[0]["prodImg"]?>" alt="Product Image">
    </div>
    <div class="right-container">
        <h1 class="product-name"><?php echo $product[0]["prodName"]?></h1>
        <p class="product-price"><?php echo $product[0]["prodPrice"]?></p>
        <p class="product-description"><?php echo $product[0]["prodDescription"]?></p>
        <p><?php echo $product[0]["prodStock"]?> left</p>
        <form class="orderForm" action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">
            <input type="number" name="quantity" min="1" value="1" class="form-control" />
            <button name="productId" id="show" class="add-to-cart" value="<?php echo $product[0]['prodId'];?>">Order Now</button>
            <input type="hidden" name="addToCart" />
        </form>    </div>
</div>

    <?php
}

// function to initialize some session keys
function init()
{

    // set some session indexes
    if(!isset($_SESSION['cart']))
    {
        // index for the shopping cart
        // index cart is an empty array
        $_SESSION['cart'] = [];
    }
}

// handle post request (if any)
function handlePost()
{
    // check if there is something posted with a post request
    if($_SERVER['REQUEST_METHOD'] == "POST")
    {
        // quick dump to see what is posted
//        dd($_POST);
        // only for adding items to shopping cart
        if(isset($_POST['quantity']))
        {
            // add posted productId as key and posted quantity as its value to session key cart
            $_SESSION['cart'][$_POST['productId']] = $_POST['quantity'];
        }
        // only to delete items in the session
        if(isset($_POST['emptyCart']))
        {
            // set the index cart as empty array again
            $_SESSION['cart'] = [];
        }
        // TO DO: INSERT CART CONTENTS INTO SOME DATABASE TABLE
    }
}


// simple debug to print arrays nicely
function dd($var, $exit = false)
{
    echo "<pre>";
    var_dump($var);
    echo "</pre>";
    if ($exit) {
        die("End of dump");
    }
}

function getProductNameById($prodId)
{
    $db = db_connect();

    // Prepare the SQL query
    $sql = "SELECT * FROM products WHERE prodId = ?;";
    $stmt = $db->prepare($sql);

    // Bind the product ID to the prepared statement
    $stmt->bind_param('i', $prodId);

    // Execute the prepared statement
    $stmt->execute();

    // Get the result and fetch it as an associative array
    $result = $stmt->get_result();
    $product = $result->fetch_all(MYSQLI_ASSOC);

    // Close the prepared statement and the connection
    $stmt->close();
    $db->close();

    return $product;
}

// display the shopping cart
function displayShoppingCart()
{
    // when no items in shopping cart
    // display some message
    if(isset($_SESSION['cart']) && count($_SESSION['cart']) == 0)
    {
        echo "No items in your shopping cart";
    }
    else
    {
        // when there are some items in the session
        // loop through all items an print each on of them
        if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0)
        {
            ?>
            <table class="table-cart">
                <tr>
                    <th class="table-cart-items">Id</th>
                    <th class="table-cart-items">Name</th>
                    <th class="table-cart-items">Price</th>
                    <th class="table-cart-items">Amount</th>
                </tr>
                <?php

            // show contents of shopping cart
            foreach ($_SESSION['cart'] as $productId => $quantity) {
                $product = getProductNameById($productId);

                ?>
                    <tr>
                        <td class="table-cart-items"><?php echo $product[0]["prodId"]?></td>
                        <td class="table-cart-items"><?php echo $product[0]["prodName"]?></td>
                        <td class="table-cart-items"><?php echo $product[0]["prodPrice"]?></td>
                        <td class="table-cart-items"><?php echo $quantity ?></td>
                    </tr>



                    <?php
            }
        }
            ?>

            </table>
            <!-- form to store items in shopping cart to database -->
            <!-- the varaible $_SERVER['PHP_SELF'] is the name of current script -->
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <button name="storeData" class="buy-products">Buy Product(s)</button>
            </form>
            <br />
            <!-- empty cart button (form) -->
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                <input type="submit" name="emptyCart" value="Empty Cart" />
            </form>
            <?php
        }
}

function getForumCats() {
    $db = db_connect();

    // Gets all information from page table
    $sql = "SELECT * FROM forums";

    // Executes the query with the connection
    $resource = $db->query($sql) or die($db->error);

    // Puts information into array
    $forums = $resource->fetch_all(MYSQLI_ASSOC);

    // Closes connection
    $db->close();

    return $forums;
}


