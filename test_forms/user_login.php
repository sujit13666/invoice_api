<?php
include './base_url.php';
?>
<form action="<?php echo $baseUrl; ?>api/auth/login" method="post" enctype="multipart/form-data">
    api_key <input type="text" name="X-API-KEY" value="qnaSMNnufSemGQran211qnaSMNnufSemGQran2kj"><br>
    email_address  <input type="text" name="email_address" value=""><br>
    password  <input type="text" name="password" value="password"><br>
    <input type="submit" name="submit"><br>

</form>