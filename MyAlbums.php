<?php
    include "./Common/Header.php";
    include "./Common/ValidationFunctions.php";
    
    if (!isset($_SESSION['loggedInUser'])) {
        $_SESSION["fromPage"]= "MyAlbums";
        header('Location: index.php');
    }
    
    $user = $_SESSION['loggedInUser'];
    $getAlbums = "SELECT * FROM Album INNER JOIN Accessibility ON Album.Accessibility_Code=Accessibility.AccessibilityCode WHERE OwnerID = ?";
    $getAlbumsCheck = $myPDO->prepare($getAlbums);
    $getAlbumsCheck->execute([($user->getID())]);
        
    if (isset($_POST["delete"])) {
    foreach ($getAlbumsCheck as $row) {
        if (($_POST["delete"]) == $row["Title"]) {
            $deletePictures = "DELETE FROM Picture WHERE AlbumID=?";
            $deletePicturesCheck = $myPDO->prepare($deletePictures);
            $deletePicturesCheck->execute([($row["AlbumID"])]);
            
            $deleteAlbum = "DELETE FROM Album WHERE Album.AlbumID=?";
            $deleteAlbumCheck = $myPDO->prepare($deleteAlbum);
            $deleteAlbumCheck->execute([($row["AlbumID"])]);
        }
    }
}
?> 
<div class="container">
<h1 align="center">My Albums</h1>
    <hr>

<p>Welcome <b><?php echo $_SESSION['loggedInUser']->getName(); ?></b>! (not you? change user <a href="Login.php">here</a>)</p><p align="right"><br><a href="AddAlbums.php">Create New Album</a></p>

<form method="post" class="form-horizontal" action="<?php $_SERVER["PHP_SELF"]; ?>">
   
    <table class="table" name="table">
        <tr>
            <th>Title </th>
            <th>Date Updated </th>
            <th>Number of Pictures </th>
            <th>Accessibility </th>
            <th></th>
        </tr>
        
         <?php
        foreach ($getAlbumsCheck as $row) {
            $getPictures = "SELECT * FROM Picture INNER JOIN Album ON Picture.AlbumID = Album.AlbumID INNER JOIN Accessibility ON Album.Accessibility_Code = Accessibility.AccessibilityCode WHERE Album.OwnerID=?";
            $getPicturesCheck = $myPDO->prepare($getPictures);
            $getPicturesCheck->execute([($user->getID())]);
            
            $getAccess = "SELECT * FROM Accessibility";
            $getAccessCheck = $myPDO->prepare($getAccess);
            $getAccessCheck->execute();
            
           
            print("<tr><td>" . $row["Title"] . "</td>");
            print("<td>" . $row["Date_Updated"] . "</td>");
            print("<td>" .$getPicturesCheck->rowCount() . "</td>");
            print("<td><select>");
            foreach($getAccessCheck as $r){
                print("<option value=".$r['AccessibilityCode'].">".$r['Description']."</option>");
                }
            print("</select></td>");
            printf("<td><button type='submit' class='btn btn-link' name='delete' value=%s onclick=\"return confirm('The album and all its pictures will be deleted')\">Delete</button></td></tr>", $row["Title"]);
        }
        
        ?>
        
    </table>
    
    <div align="right">
        <br>
        <button type="submit" name="saveBtn" class="btn btn-primary">Save Changes</button>

    </div>

</form>
</div>

    <?php
    include "./Common/Footer.php"; 

