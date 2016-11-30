<?php
include './base_url.php';
?>
<form action="<?php echo $baseUrl; ?>api/auth/register" method="post" enctype="multipart/form-data">
    api_key <input type="text" name="X-API-KEY" value="qnaSMNnufSemGQran211qnaSMNnufSemGQran2kj"><br>
    email  <input type="text" name="email" value="mahmudbd1000@gmail.com"><br>
    password  <input type="text" name="password" value="123"><br>
    userName  <input type="text" name="userName" value="mahmudbd1000"><br>
    <!--lastname  <input type="text" name="lastname" value="Mahmud"><br>-->
    <input type="submit" name="submit"><br>

</form>