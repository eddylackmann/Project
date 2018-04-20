<?php 

?>

<html>
<head>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</head>
<style>
#cardformat{
padding-bottom:10px;
}

</style>
<?php

$KnkLib = new KnkLibrary();
$projects= $KnkLib->Get_projects();

function setgrid($project){
$Knk = new KnkLibrary();
$participant = $Knk->GetParticipantsByProject($project->No);
$participant_list='';
  $title = $project->Main_Title;
  if(strlen($title)> 50){
    $title=substr($project->Main_Title,0,50).' ...';
  }

  $image="https://img.buzzfeed.com/buzzfeed-static/static/2017-06/28/6/asset/buzzfeed-prod-fastlane-03/sub-buzz-12341-1498644607-5.jpg?crop=400%3A600%3B0%2C0&downsize=715:*&output-format=auto&output-quality=auto";
 return '<div class="col-md-3" id="cardformat">
 <div class="card"  style="width: 18rem;">
  
   <div class="card-body">
   <div style="height: 70px; width:100%; Color:#003FBE; border-bottom: 1px black;">
     <h6 class="card-title">'.$title.'</h6>
    </div>  
         <p class="card-text">   
         <b>Author: </b>'.$project->Author_Name.'</br>
         <b>ISBN: </b>'.$project->ISBN_13_Complete.'</br>
         <b>Genre: </b>'.$project->Genre_Description.'</br>       
         </p>
         
      
      <a href="#" class="btn btn-outline-dark btn-xs">Weiterlesen</a>
   </div>
 </div>
</div>';

}


?>
   
<body>
  <div class="row">
    
    <?php
    
      
      if((isset($_GET['Project']))){
            $single_project = $KnkLib->GetProjectById($_GET['Project']);
            $participants = $KnkLib->GetParticipantsByProject($_GET['Project']);
          
            if(!empty($single_project)){
              //print_r($single_project);
              echo '<h3>'.$single_project->Main_Title.'</h3> ';
            
            }

            if(!empty($participants)){
              foreach($participants as $p){
                echo $p->Role_Description.': '.$p->Name.'<br>';
              }
            }
      }else{
            foreach($projects as $project){
              echo setgrid($project);
            }
          }
    
      
    
    ?>
      
  </div>
</body>
</html>