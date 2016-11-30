<?php
include './base_url.php';
?>
<form action="<?php echo $baseUrl; ?>api/events/create" method="post" enctype="multipart/form-data">
    api_key <input type="text" name="X-API-KEY" value="qnaSMNnufSemGQran211qnaSMNnufSemGQran2kj"><br>
    
    <!--rid  <input type="text" name="rid" value="5fffbbe35dd4d12211644ee9a5fe3193542f99b9"><br>-->
    user_id  <input type="text" name="userId"><br>
    eventTitle  <input type="text" name="eventTitle"><br>
      description<input type="text" name="description" value=""><br>
      startDate <input type="text" name="startDate" value=""><br>
      startTime <input type="text" name="startTime" value=""><br>
      endDate <input type="text" name="endDate" value=""><br>
      endTime <input type="text" name="endTime" value=""><br>
     <input type="submit" name="submit"><br>

</form>