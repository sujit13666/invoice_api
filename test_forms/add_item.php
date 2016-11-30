<?php
include './base_url.php';
?>
<form action="<?php echo $baseUrl; ?>api/items/create" method="post" enctype="multipart/form-data">
    api_key <input type="text" name="X-API-KEY" value="qnaSMNnufSemGQran211qnaSMNnufSemGQran2kj"><br>
    
    <!--rid  <input type="text" name="rid" value="5fffbbe35dd4d12211644ee9a5fe3193542f99b9"><br>-->
    user_id  <input type="text" name="userId"><br>
      itemName<input type="text" name="itemName" value=""><br>
      description .<input type="text" name="description" value=""><br>
      rate<input type="text" name="rate" value=""><br>
      cost<input type="text" name="cost" value=""><br>
    tags <input type="text" name="tags" value=""><br>
      taxable <input type="text" name="taxable" value=""><br>
     <input type="submit" name="submit"><br>

</form>