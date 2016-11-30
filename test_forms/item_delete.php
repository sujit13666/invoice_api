<?php
include './base_url.php';
?>
<form action="<?php echo $baseUrl; ?>api/items/delete" method="post" enctype="multipart/form-data">
    api_key <input type="text" name="X-API-KEY" value="qnaSMNnufSemGQran211qnaSMNnufSemGQran2kj"><br>
    
    <!--rid  <input type="text" name="rid" value="5fffbbe35dd4d12211644ee9a5fe3193542f99b9"><br>-->
    user_id  <input type="text" name="userId"><br>
    item id  <input type="text" name="itemId[]"><br>
    item id  <input type="text" name="itemId[]"><br>
     <input type="submit" name="submit"><br>

</form>