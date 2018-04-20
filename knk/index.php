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
    require_once('lib/config.php'); 
    require_once('lib/NTLMSoapClient.php'); 
    require_once('lib/NTLMStream.php'); 
    // Knk Library Klasse hat alle Nötige Funktionen zum Lesen der Webservices und enthält weitere nötigen Tools.
    class knkLibrary{
        private $config;

        //Konstruktor
        function __constructor(){
            $this->config = "lib/knk_config.ini";
        }

        /******************************* 
                *KONFIGURATION*
        *******************************/
        //Diese Funktion lies die Config datei und gibt es uns als Array zurück
        public function GetConfig(){
            return parse_ini_file("lib/knk_config.ini");       
        }

        //Diese Funktion modifiert die Config Datei
        public function write_php_ini($array, $file){
            $res = array();
            foreach($array as $key => $val)
            {
                if(is_array($val))
                {
                    $res[] = "[$key]";
                    foreach($val as $skey => $sval) $res[] = "$skey = ".(is_numeric($sval) ? $sval : '"'.$sval.'"');
                }
                else $res[] = "$key = ".(is_numeric($val) ? $val : '"'.$val.'"');
            }
            safefilerewrite($file, implode("\r\n", $res));
        }

        public function safefilerewrite($fileName, $dataToSave){
           if ($fp = fopen($fileName, 'w'))
            {
                $startTime = microtime(TRUE);
                do
                {            $canWrite = flock($fp, LOCK_EX);
                   // If lock not obtained sleep for 0 - 100 milliseconds, to avoid collision and CPU load
                   if(!$canWrite) usleep(round(rand(0, 100)*1000));
                } while ((!$canWrite)and((microtime(TRUE)-$startTime) < 5));
        
                //file was locked so now we can store information
                if ($canWrite)
                {            fwrite($fp, $dataToSave);
                    flock($fp, LOCK_UN);
                }
                fclose($fp);
            }
        
        }

       /******************************* 
                *WEBSERVICES*
        *******************************/
        //Funktion holt alle Projekte aus KNK Verlag heraus 
        public function GetProjects(){
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
        public function GetProjectByNo($no){
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
        public function GetParticipantsByProject($projectNo){
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
                $participants = $result->ReadMultiple_Result->knk_Project_Participant;  
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
                        <b>ISBN: </b>'.$project->ISBN_13_Complete.'</br>
                        <b>Genre: </b>'.$project->Genre_Description.'</br> <hr>';
            $card .= $this->SetParticipantList($participants);
            $card .='</div>';

            echo $card;
        }

        //erzeugt Eine Liste mit den Projektbeteiligten
        private function SetParticipantList($participants){
            $participant_list='<h5> Projektbeteiligten </h5>';
            if(!empty($participants)){
                foreach($participants as $participant){
                    $participant_list .= '<b>'.$participant->Role_Description.': </b>'.$participant->Name.'<br>';
                }
            }

            return $participant_list;
        }
        
        //Erzeugt einzelne Projekt Raster
        private function SetProjectGrid($project){
            $title = $project->Main_Title;
            if(strlen($title)> 50){
                $title=substr($project->Main_Title,0,50).' ...';
            }        
            return '<div class="col-md-3" id="cardformat" style="padding-bottom:10px;">
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
                                <form>
                                    <input type="hidden" name="Project"  value="'.$project->No.'"/>
                                    <input type="submit" class="btn btn-outline-dark btn-xs" value="Weiterlesen"/>
                                </form>
                            </div>
                        </div>
                     </div>';
        }

       
    }
?>

