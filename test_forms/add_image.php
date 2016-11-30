<?php
include './base_url.php';
?>
<form action="<?php echo $baseUrl; ?>api/receipts/create_receipt" method="post" enctype="multipart/form-data">
    api_key <input type="text" name="X-API-KEY" value="qnaSMNnufSemGQran211qnaSMNnufSemGQran2kj"><br>

    file  <input type="file" name="receiptFile"><br>

     <input type="submit" name="submit"><br>

</form>