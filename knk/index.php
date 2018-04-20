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

        }

        //Private Funtion der beim Initialisieren der Klasse Ausgeführt wird
        private function init(){

        }

        //Configurations Datei
        public function GetConfig(){
            return parse_ini_file("lib/knk_config.ini");       
        }

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

        //funKtion zum Auslesen der Webservices
        public function Get_projects(){
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

        public function GetProjectById($id){
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


        public function Read_Services($url){
            // we unregister the current HTTP wrapper 
            
            //print_r($project);


            //$data_array = json_decode(implode($result['ReadMultiple_Result']),TRUE);
            //$result = $data_array['value'];

            //foreach($result  as $customer) { 
            //  echo $customer['Main_Title'].'<br>'; 
            //} 


            return $data;
        }

        Public function Read_Service_With_Parameter(){

        } 

        /****************************** 
                * DB FUNKTIONEN*
        *******************************/

        //Knk DB Löschen

        public function create_db(){
            
        }

        public function delete_db(){

        }

        //DB Infos Aktualisieren
        public function update_db($username,$passwort,$project_WS_link,$Participants_WS_link,$Content_WS_Link){

        }

        //DB Infos 
        public function getDBInformation(){

        }






    }
?>

