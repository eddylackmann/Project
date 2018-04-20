
<?php
$KnkLib = new KnkLibrary();
?>
   
  <div class="row"> 
    <?php
      if((isset($_GET['Project']))){  
        if($_GET['Project'] ==' '){
          $KnkLib->ProjectsGrid();
        }else{
          $KnkLib->ProjectCard($_GET['Project']);   
        }   
      }else{
        $KnkLib->ProjectsGrid();
      }  
    ?>
      
  </div>
