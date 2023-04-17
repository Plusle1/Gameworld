<?php
include('./inc/htmlBasics.php');
include('./inc/sql.php');

session_start();
init();
htmlHead();
displayNavbar();

?>


<header>
  <div class="heading">
    <h1>GameWorld</h1>
    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum eget purus in est vulputate venenatis. Nulla
      vel elit id elit tincidunt fringilla. Ut facilisis ullamcorper mauris, id bibendum ipsum finibus vel. Praesent
      elementum arcu ac nisi euismod, vitae tincidunt nibh ultrices. Nullam sed sem luctus, vehicula odio vel, dictum
      magna. Integer sagittis nunc vitae augue sollicitudin ornare. Duis vehicula eros vel neque aliquam faucibus.</p>
    <a href="products.php" class="btn btn-hero">Learn More</a>
  </div>
</header>
