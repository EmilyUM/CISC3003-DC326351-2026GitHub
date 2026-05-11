<?php
function postValue($key) {
    $value = $_POST[$key] ?? "";
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>CISC3003 Suggested Exercise 09</title>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <script src="js/misc.js"></script>
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<?php include 'header.inc.php'; ?>
    
<main>
    <div class="results">
    
    <table>
      <caption class="results__caption">Art Work Saved</caption>
      <tr>
        <td class="results__label">Title</td>    
        <td class="results__value"><?php echo postValue("title"); ?></td> 
      </tr>
      <tr>
        <td class="results__label">Description</td>    
        <td class="results__value"><?php echo postValue("description"); ?></td> 
      </tr>
      <tr>
        <td class="results__label">Genre</td>    
        <td class="results__value"><?php echo postValue("genre"); ?></td> 
      </tr>
      <tr>
        <td class="results__label">Subject</td>    
        <td class="results__value"><?php echo postValue("subject"); ?></td> 
      </tr>
      <tr>
        <td class="results__label">Medium</td>    
        <td class="results__value"><?php echo postValue("medium"); ?></td> 
      </tr>   
      <tr>
        <td class="results__label">Year</td>    
        <td class="results__value"><?php echo postValue("year"); ?></td> 
      </tr>  
      <tr>
        <td class="results__label">Museum</td>    
        <td class="results__value"><?php echo postValue("museum"); ?></td> 
      </tr>          
    </table>
    
    </div>
</main>       
</body>
</html>
