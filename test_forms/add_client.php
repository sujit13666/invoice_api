<?php
include './base_url.php';
?>
<form action="<?php echo $baseUrl; ?>api/clients/create" method="post" enctype="multipart/form-data">
    api_key <input type="text" name="X-API-KEY" value="qnaSMNnufSemGQran211qnaSMNnufSemGQran2kj"><br>
    
    <!--rid  <input type="text" name="rid" value="5fffbbe35dd4d12211644ee9a5fe3193542f99b9"><br>-->
    user_id  <input type="text" name="userId"><br>
    businessName  <input type="text" name="businessName"><br>
    email  <input type="text" name="email" value="mahmudbd1000@gmail.com"><br>
    billing address  <input type="text" name="billingAddress" value="20/3 Mohakhali, Dhaka"><br>
    contactName  <input type="text" name="contactName" value="Bulbul Mahmud Nito"><br>
    contactBusinessPhone  <input type="text" name="contactBusinessPhone" value="0169552585"><br>
    contactMobile  <input type="text" name="contactMobile" value="0169552585"><br>
    contactEmail  <input type="text" name="contactEmail" value="mahmudbd@cc.com"><br>
    Fax  <input type="text" name="fax" value="545865485458"><br>
    Pager  <input type="text" name="pager" value="545865485458"><br>
    website  <input type="text" name="website" value="www.website.com"><br>
    Shipping name  <input type="text" name="shippingName" value=""><br>
     shipping address <input type="text" name="shippingAddress" value=""><br>
     Terms <input type="text" name="terms" value=""><br>
      territory<input type="text" name="territory" value=""><br>
      tax no.<input type="text" name="taxNumber" value=""><br>
      Other<input type="text" name="other" value=""><br>
      Note<input type="text" name="note" value=""><br>
     Map Address <input type="text" name="mapAddress" value=""><br>
      latitude <input type="text" name="latitude" value=""><br>
      longitude <input type="text" name="longitude" value=""><br>
     <input type="submit" name="submit"><br>

</form>