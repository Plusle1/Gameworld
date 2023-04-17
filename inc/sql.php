<?php

// Import a JetBrains Library
use JetBrains\PhpStorm\NoReturn;

function db_connect()
{

    // Establishes location of database
    $host = "localhost";
    $user = "root";
    $pass = "";
    $data = "gameworld";

    // Makes a connection
    $mysqli = new mysqli($host, $user, $pass, $data);

    if ($mysqli->connect_errno) // In case of an error
    {
        die("Connection " . $data . " failed");
    }

    return $mysqli;

}

// Gets the navbar items from the database
function getNavbar()
{
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

function displayNavbar(): void
{
    $pages = getNavbar();

    ?>
    <nav class="navigation">
        <div class="logo">
            <a href="index.php">GameWorld</a>
        </div>
        <ul>
            <?php foreach ( $pages as $page ) { ?>
                <li><a href="<?php echo $page[ "navDir" ] ?>"><?php echo $page[ "navName" ]; ?></a></li>
            <?php } ?>
        </ul>
    </nav>


    <?php

}

function getCategories()
{

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

function displayCategories(): void
{

    $categories = getCategories();

    ?>
    <div class="category-container">

        <?php
        foreach ( $categories as $category ) {
            ?>

            <div class="category">
                <img src="<?php echo $category[ 'catImg' ]; ?>" alt="<?php echo $category[ 'catName' ]; ?>">
                <h2><?php echo $category[ 'catName' ]; ?></h2>
                <a href="products.php?catId=<?php echo $category[ "catId" ] ?>"
                   class="btn"><?php echo $category[ 'catName' ]; ?></a>
            </div>
        <?php } ?>
    </div>

    </div>

    <?php
}

function getProducts($catId)
{

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

function displayProducts($catId): void
{

    $products = getProducts($catId);

    foreach ( $products as $product ) {
        ?>
        <div class="product">
            <div class="product-image">
                <img src="<?php echo $product[ "prodImg" ] ?>" class="image-size"/>
            </div>
            <div class="product-details">
                <h1><?php echo $product[ "prodName" ] ?></h1>
                <p class="price"><?php echo $product[ "prodPrice" ] ?></p>
                <a class="btn" href="products.php?prodId=<?php echo $product[ "prodId" ] ?>">Go!</a>
            </div>
        </div>

        <?php
    }
}

function getProduct($prodId)
{

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

function displayProduct($prodId): void
{

    $product = getProduct($prodId);

    ?>
    <div class="product-page-container">
        <div class="left-container">
            <img src="<?php echo $product[ 0 ][ "prodImg" ] ?>" alt="Product Image">
        </div>
        <div class="right-container">
            <h1 class="product-name"><?php echo $product[ 0 ][ "prodName" ] ?></h1>
            <p class="product-price"><?php echo $product[ 0 ][ "prodPrice" ] ?></p>
            <p class="product-description"><?php echo $product[ 0 ][ "prodDescription" ] ?></p>
            <p><?php echo $product[ 0 ][ "prodStock" ] ?> left</p>
            <form class="orderForm" action="<?php $_SERVER[ 'PHP_SELF' ]; ?>" method="POST">
                <input type="number" name="quantity" min="1" value="1" class="form-control" />
                <button name="productId" class="add-to-cart" value="<?php echo $product[ 0 ][ 'prodId' ]; ?>">
                    Order Now
                    </button>
                <input type="hidden" name="addToCart"/>
            </form>
        </div>
    </div>

    <?php
}

// function to initialize some session keys
function init(): void
{

    // set some session indexes
    if (!isset($_SESSION[ 'cart' ])) {
        // index for the shopping cart
        // index cart is an empty array
        $_SESSION[ 'cart' ] = [];
    }
}

// handle post request (if any)
function handlePost(): void
{

    // check if there is something posted with a post request
    if ($_SERVER[ 'REQUEST_METHOD' ] == "POST") {
        // quick dump to see what is posted
//        dd($_POST);
        // only for adding items to shopping cart
        if (isset($_POST[ 'quantity' ])) {
            // add posted productId as key and posted quantity as its value to session key cart
            $_SESSION[ 'cart' ][ $_POST[ 'productId' ] ] = $_POST[ 'quantity' ];

        } // only to delete items in the session
        else if (isset($_POST[ 'emptyCart' ])) {
            // set the index cart as empty array again
            $_SESSION[ 'cart' ] = [];
        }
        else if (isset($_POST[ "storeData" ])) {
            insertCartIntoDatabase($_SESSION[ 'cart' ]);
//            } else {
//                echo "There has been an unexpected error!";
        }
    }
}


// simple debug to print arrays nicely
function dd($var, $exit = false): void
{
    echo "<pre>";
    var_dump($var);
    echo "</pre>";
    if ($exit) {
        die("End of dump");
    }
}

function getProductNameById($prodId): array
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
function displayShoppingCart(): void
{
    // when no items in shopping cart
    // display some message
    if (isset($_SESSION[ 'cart' ]) && count($_SESSION[ 'cart' ]) == 0) {
        echo "No items in your shopping cart";
    }
    else {
        // when there are some items in the session
        // loop through all items an print each on of them
        if (isset($_SESSION[ 'cart' ]) && count($_SESSION[ 'cart' ]) > 0) {
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
            foreach ( $_SESSION[ 'cart' ] as $productId => $quantity ) {
                $product = getProductNameById($productId);

                ?>
                <tr>
                    <td class="table-cart-items"><?php echo $product[ 0 ][ "prodId" ] ?></td>
                    <td class="table-cart-items"><?php echo $product[ 0 ][ "prodName" ] ?></td>
                    <td class="table-cart-items"><?php echo $product[ 0 ][ "prodPrice" ] ?></td>
                    <td class="table-cart-items"><?php echo $quantity ?></td>
                </tr>


                <?php
            }
        }
        ?>

        </table>
        <!-- form to store items in shopping cart to database -->
        <form method="post" action="<?php echo $_SERVER[ 'PHP_SELF' ]; ?>">
            <button name="storeData" class="add-to-cart" value="submit">Buy Product(s)</button>
            <br/>
            <input type="submit" name="emptyCart" value="Empty Cart"/>
        </form>
        <?php
    }
}

function getForumCats()
{
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

function displayForumCategories(): void
{
    $cats = getForumCats();
    ?>


    <div id="cat-container">

        <div id="left-container">
            <h2>Forum Posts</h2>
            <?php
            // Checks the url for several things. Does things when theres something set
            if (isset($_GET[ "postId" ])) {
                displayItem();
            }
            else if (isset($_GET[ "category" ])) {
                insertPostIntoDatabase();
            }
            else if (isset($_GET[ "posts" ])) {
                insertCommentIntoDatabase();
            }

            else {
                displayItems($_GET[ "catId" ]);

            } ?>
            
            <form>
                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" required>
                <label for="name">Title:</label>
                <input type="text" id="name" name="name" required>
                <label for="message">Message:</label>
                <textarea id="message" name="message" rows="5" required></textarea>
                <?php if(isset($_GET["catId"])) { ?>
                <div class="div-cats"><label for="category">Category:</label>
                    <select id="category" name="category" required>
                        <?php
                            foreach ( $cats as $cat ) { ?>
                            <option value="<?php echo $cat[ "forumId" ]; ?>"><?php echo $cat[ "forumName" ]; ?></option>
                        <?php } ?>
                    </select>
                </div>

                    <?php } else if(isset($_GET["postId"])) {?>
                    <div class="div-posts"><label for="posts">Comment in:</label>
                            <select id="posts" name="posts" required>
                            <option value="<?php echo $_GET[ "postId" ]; ?>"><?php echo $_GET[ "postId" ]; ?></option>
                        </select>
                    </div>
                        <?php } ?>
                <input type="submit" value="Submit">
            </form>
        </div>

        <div id="right-container">
            <h2>Categories</h2>
            <?php foreach ( $cats as $cat ) { ?>
                <a href="forums.php?catId=<?php echo $cat[ "forumId" ] ?>"
                   class="category-link"><?php echo $cat[ "forumName" ] ?></a>
            <?php } ?>
        </div>

    </div>



<?php }


function getItems($forumId)
{
    $db = db_connect();

    // Gets all information from page table
    $sql = "SELECT * FROM posts WHERE forumId=" . $forumId;

    // Executes the query with the connection
    $resource = $db->query($sql) or die($db->error);

    // Puts information into array
    $posts = $resource->fetch_all(MYSQLI_ASSOC);

    // Closes connection
    $db->close();

    return $posts;

}

function displayItems($catId): void
{

    $items = getItems($catId);

    foreach ( $items as $item ) {
        ?>
        <article>
            <h3><?php echo $item[ "postName" ]; ?></h3>
            <h4><?php echo $item["postEmail"]?> | <?php echo $item["postDate"]?></h4>
            <p><?php echo substr($item[ "postDescription" ], 0,200);; ?></p>
        </article>
        <a href="forums.php?postId=<?php echo $item[ "postId" ]; ?>">
            <button class="btn">Read More</button>
        </a>
        <?php
    }
}

function displayItem(): void
{
    $item = getItemNameById($_GET[ "postId" ])
    ?>

    <article class="article">
        <h3><?php echo $item[ 0 ][ "postName" ]; ?></h3>
        <h4><?php echo $item[0]["postEmail"]?> | <?php echo $item[0]["postDate"]?></h4>
        <p><?php echo $item[ 0 ][ "postDescription" ] ?></p>
    </article>

    <?php
    displayComment();
}

function getItemNameById($postId): array
{
    $db = db_connect();

    // Prepare the SQL query
    $sql = "SELECT * FROM posts WHERE postId = ?;";
    $stmt = $db->prepare($sql);

    // Bind the product ID to the prepared statement
    $stmt->bind_param('i', $postId);

    // Execute the prepared statement
    $stmt->execute();

    // Get the result and fetch it as an associative array
    $result = $stmt->get_result();
    $post = $result->fetch_all(MYSQLI_ASSOC);

    // Close the prepared statement and the connection
    $stmt->close();
    $db->close();

    return $post;
}

#[NoReturn] function insertPostIntoDatabase(): void
{
    // Gets the information from the url
    $email = $_GET[ "email" ];
    $message = $_GET[ "message" ];
    $category = $_GET[ 'category' ];
    $name = $_GET[ 'name' ];
    $date = date("Y-m-d");

    $db = db_connect();


    $sql = "INSERT INTO posts (postname, postdescription, postemail, postdate, forumid) VALUES (?, ?, ?, ?, ?);";
    $stmt = $db->prepare($sql);

    $stmt->bind_param("sssss", $name, $message, $email, $date, $category);
    $stmt->execute();


}

#[NoReturn] function insertCartIntoDatabase($cart): void
{

    $db = db_connect();
    
    
    foreach ($cart as $productId => $quanity) {
        $sql = "INSERT INTO cart (cartItem, cartQuantity) VALUES (?, ?);";
        $stmt = $db->prepare($sql);

        $stmt->bind_param("ii", $productId, $quanity);
        $stmt->execute();
    }
}

#[NoReturn] function insertCommentIntoDatabase(): void
{
    $email = $_GET[ "email" ];
    $message = $_GET[ "message" ];
    $date = date("Y-m-d");
    $post = $_GET[ "posts" ];
    $name = $_GET[ 'name' ];


    $db = db_connect();


    $sql = "INSERT INTO comments (comName, comDescription, comEmail, comDate, postId) VALUES (?, ?, ?, ?, ?);";
    $stmt = $db->prepare($sql);

    $stmt->bind_param("sssss", $name, $message, $email, $date, $post);
    $stmt->execute();


}

function getComments($id): array {
    $db = db_connect();

    // Gets all information from page table
    $sql = "SELECT * FROM comments WHERE postId=" . $id;

    // Executes the query with the connection
    $resource = $db->query($sql) or die($db->error);

    // Puts information into array
    $comments = $resource->fetch_all(MYSQLI_ASSOC);

    // Closes connection
    $db->close();

    return $comments;
}

function displayComment(): void
{
    $comments = getComments($_GET["postId"]);
?>
    <div class="comments">
        <h3 class="comments-header">Comments:</h3>
            <?php
                foreach ($comments as $comment) {?>

                    <article class="comments">
                            <h3><?php echo $comment[ "comName" ]; ?></h3>
                            <h4 class="author-description"><?php echo $comment["comEmail"] ?> | <?php echo $comment["comDate"]?></h4>
                            <p><?php echo $comment[ "comDescription" ] ?></p>
                    </article>
    <?php }
                ?>
    </div>
        <?php
}


