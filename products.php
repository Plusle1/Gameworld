<?php
include('./inc/htmlBasics.php');
include('./inc/sql.php');

session_start();
init();
handlePost();

htmlHead();
displayNavbar();


?>

  <!--main section--------------->
  <section class="main">
    <div class="product-container">

        <?php
        if (isset($_GET['catId'])) {

            displayProducts($_GET["catId"]);



        } else if (isset($_GET["prodId"])) {


            displayProduct($_GET["prodId"]);

        } else {
            displayCategories();
        } ?>


  </section>

</body>
</html

