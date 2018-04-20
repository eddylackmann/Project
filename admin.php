<div class="container-fluid">
    <h2 style="color:#003FBE;">Knk Business Software AG</h2>
    <h4> Knk Projekt Administration</h4>
    <?php   
        $knk= new KnkLibrary();
        $config= $knk->GetConfig();
    ?>
    <hr>
    <form>
    <div class="row"> 
             <div class="col-md-5">  
                <h5 style="color:#003FBE;"> Benutzereinrichtung</h5>       
                <div class="form-group" >         
                    <input type="text"  style="color:#003FBE;" class="form-control" value="<?php echo  $config['username'];?>" placeholder="Domain\Benutername"><br>
                    <input type="password" style="color:#003FBE;" class="form-control"  value="<?php echo  $config['password'];?>" id="pwd" placeholder="Passwort">
                </div>         
            </div>
            <div class="col-md-1">
            </div>
            <div class="col-md-5">
                <h5 style="color:#003FBE;"> Webserviceseinrichtung (SOAP)</h5>
                <div class="form-group" style="color:#003FBE;">
                    <input type="text" style="color:#003FBE;" class="form-control" placeholder="Projekt Webservices"  value="<?php echo  $config['project'];?>" required><br>
                    <input type="text" style="color:#003FBE;" class="form-control" placeholder= "Projekt Beteiligte Webservices"  value="<?php echo  $config['participants'];?>"required><br>
                    <input type="text" style="color:#003FBE;" class="form-control" placeholder="Projekt Content Webservices" required>
                </div>
            </div>
          
            
        
    </div>

    <div class="row" style="text-align:center;">
         <div class="col-md-12">
            <hr>
            <button type="" class="btn btn-default" style="width:50%; background:#003FBE; color:white;">Speichern</button>
        </div >
    </div>

            
    </form>     
</div>

</body>
</html>