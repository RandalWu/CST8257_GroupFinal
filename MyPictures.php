<?php
    include "./Common/Header.php";
    include "./Common/ValidationFunctions.php";
    
    if (!isset($_SESSION['loggedInUser'])) {
        $_SESSION["fromPage"]= "MyPictures";
        header('Location: index.php');
    }
    
  ?>
<h1 align="center">My Pictures</h1>

<form method="post" class="form-horizontal" action="<?php $_SERVER["PHP_SELF"]; ?>"
    
    
</form>

<?php  include "./Common/Footer.php"; 
