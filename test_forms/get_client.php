<?php
include './base_url.php';
?>
<form action="<?php echo $baseUrl; ?>api/clients/get" method="post" enctype="multipart/form-data">
    api_key <input type="text" name="X-API-KEY" value="qnaSMNnufSemGQran211qnaSMNnufSemGQran2kj"><br>
    
    <!--rid  <input type="text" name="rid" value="5fffbbe35dd4d12211644ee9a5fe3193542f99b9"><br>-->
    user_id  <input type="text" name="user_id"><br>
     <input type="submit" name="submit"><br>

</form>