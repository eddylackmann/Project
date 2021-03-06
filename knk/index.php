<?php
/*  
    Entwickler: Eddy Lackmann
    Kürzel: ELA 
    Firma: knk Business Software AG
    Webseite: https://knkverlagssoftware.com/
    Lizenz: knk Business Software AG
    Datum:05.04.2018
*/

    //Import All Libraries
    require_once('lib/ini.php'); 
    require_once('lib/config.php'); 
    require_once('lib/NTLMSoapClient.php'); 
    require_once('lib/NTLMStream.php'); 

    // Knk Library Klasse hat alle Nötige Funktionen zum Lesen der Webservices und enthält weitere nötigen Tools.
    class knkLibrary{
        private $confg;

        //Konstruktor
        function __constructor(){
           
        }

        /******************************* 
                *KONFIGURATION*
        *******************************/
        //Diese Funktion lies die Config datei und gibt es uns als Array zurück
        public function GetConfig(){
            return parse_ini_file("lib/knk_config.ini");       
        }
        //Diese Funktion aktualisiert 
        public function UpdateConfig($user,$password,$project_link,$participant_link){
           // $this->confg->read();      
            $this->config_set("user","username",$user); 
            $this->config_set("user","password",var_dump($passwort)); 

            //Webservices aktualisieren
            $this->config_set("webservices","project", $project_link); 
            $this->config_set("webservices","participants",$participant_link); 
        }
        
        ////Diese Funktion modifiert die Config Datei
        private function config_set($section, $key, $value) {
            $config_data = parse_ini_file("lib/knk_config.ini",TRUE);
            $config_data[$section][$key] = $value;
            $new_content = '';
            foreach ($config_data as $section => $section_content) {
                $section_content = array_map(function($value, $key) {
                    return "$key=$value";
                }, array_values($section_content), array_keys($section_content));
                $section_content = implode("\n", $section_content);
                $new_content .= "[$section]\n$section_content\n";
            }
            file_put_contents(plugin_dir_path( __FILE__ ).'\lib\knk_config.ini', $new_content);
        }

       /******************************* 
                *WEBSERVICES*
        *******************************/

        //Funktion holt alle Projekte aus KNK Verlag heraus 
        private function GetProjects(){
            stream_wrapper_unregister('http'); 
            stream_wrapper_register('http', 'NTLMStream') or die("Failed to register protocol");
            $link = $this->GetConfig()['project'];
            if( !$link==''){
                $WS = new NTLMSoapClient($link);
                $result = $WS->ReadMultiple(); 
                $projects =  $result->ReadMultiple_Result->Knk_Project; 
                return $projects;          
            }else{
                return NULL;
            }
           
        }

        ////Funktion holt Projekt mit bestimmte ID aus KNK Verlag heraus 
        private function GetProjectByNo($no){
            stream_wrapper_unregister('http'); 
            stream_wrapper_register('http', 'NTLMStream') or die("Failed to register protocol");
            $link = $this->GetConfig()['project'];
            if( !$link==''){
                $params = array("filter" => array( 
                    array("Field" => "No", 
                    "Criteria" => $no)), 
                    "setSize" => 1
                    ); 
                $WS = new NTLMSoapClient($link);
                $result = $WS->ReadMultiple($params); 
                $projects =  $result->ReadMultiple_Result->Knk_Project; 
                return $projects;          
            }else{
                return NULL;
            }
        }

        //Funktion holt alle Projektbeteiligten aus KNK Verlag heraus 
        private function GetParticipantsByProject($projectNo){
            stream_wrapper_unregister('http'); 
            stream_wrapper_register('http', 'NTLMStream') or die("Failed to register protocol");
            $link = $this->GetConfig()['participants'];   
            if( !$link==''){
                $params = array("filter" => array( 
                    array("Field" => "Project_No", 
                    "Criteria" => $projectNo)), 
                    "setSize" => 0
                    ); 
                $WS = new NTLMSoapClient($link);
                $result = $WS->ReadMultiple($params); 
                $participants = $result->ReadMultiple_Result->Knk_Project_Participant;  
                return $participants;          
            }else{
                return NULL;
            }
        }

        /******************************* 
                    *Content*
        *******************************/
        //Diese Funktion erzeugt eine Raster mit Alle Projekte für die Front End der Plugin
        public function ProjectsGrid(){
            $grid ='';
            $projects = $this->GetProjects();
            if(!empty($projects)){
                foreach($projects as $project){
                    $grid .= $this->SetProjectGrid($project); 
                }
            }
            echo $grid;
        }
        
        //DieseFunktion erzeugt eine Projektkarte mit weiteren Informationen
        public function ProjectCard($no){
            $card ='<div class="col-md-12">';
            $project = $this->GetProjectByNo($no);
            $participants =  $this->GetParticipantsByProject($no);
            $card .= '<h3>'.$project->Main_Title.'</h3>';
            $card .= '  <b>Author: </b>'.$project->Author_Name.'</br>
                        <b>Art: </b>'.$project->Item_Type_Code.' ('.$project->Item_Type_Description.')</br>
                        <b>ISBN: </b>'.$project->ISBN_13_Complete.'</br>
                        <b>Genre: </b>'.$project->Genre_Description.'</br>
                        <b>Erscheinungstermin: </b>'. date("d.m.y",strtotime($project->Planned_Publication_Date)).'</br>
                      
                         <hr>';
            $card .= $this->SetParticipantList($participants);
            $card .='</div>';

            echo $card;
        }

        //erzeugt Eine Liste mit den Projektbeteiligten
        private function SetParticipantList($participants){
            $participant_list='<h5> Projektbeteiligten </h5>';
            $participant_list.= '<table class="table">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Role</th>
                <th scope="col">Name</th>
              </tr>
            </thead>
            <tbody>   
            ';
            if(!empty($participants)){
                $count=1;
                foreach($participants as $participant){
                    //$participant_list .= '<b>'.$participant->Role_Description.': </b>'.$participant->Name.'<br>';
                    $participant_list .='<tr>
                                            <th scope="row">'.$count.'</th>
                                            <td><b>'.$participant->Role_Description.'</b></td>
                                            <td>'.$participant->Name.'</td>
                                        </tr>';
                    $count=$count + 1;
                }
            }

            $participant_list .= '</tbody>
                                </table>';
            return $participant_list;
        }
        
        //Erzeugt einzelne Projekt Raster
        private function SetProjectGrid($project){
            $title = $project->Main_Title;
            if(strlen($title)> 50){
                $title=substr($project->Main_Title,0,50).' ...';
            }        
            return '<div class="col-md-3" id="cardformat" style="padding-bottom:10px; ">
                        <div class="card"  style="width: 100%; ">
                            <div class="card-body">
                                <div style="height: 150px; width:100%; Color:#003FBE; background:#F5F5F5; text-align:center; padding:auto; border-bottom: 1px  black;">
                                    <h6 class="card-title">'.$title.'</h6>
                                </div>  
                                <div style="height: 180px;>
                                <p class="card-text">   
                                    <br>
                                    <b>Author: </b>'.$project->Author_Name.'</br>
                                    <b>ISBN: </b>'.$project->ISBN_13_Complete.'</br>
                                    <b>Genre: </b>'.$project->Genre_Description.'</br>    
                                    <b>Art: </b>'.$project->Item_Type_Description.'</br>   
                                </p>
                                </div>
                                <form>
                                    <input type="hidden" name="Project"  value="'.$project->No.'"/>
                                    <input type="submit" class="btn btn-primary btn-xs" value="Weiterlesen"/>
                                </form>
                            </div>
                        </div>
                     </div>';
        }

       
    }
?>

