<?php
	Class UserController{		
				
		function save($user){
            if(!file_exists($this->getFileName($user->getEmail()))){
                $json = json_encode($user);						
		        $fp = fopen ($this->getFileName($user->getEmail()), "w");		
		        fwrite($fp, $json);		
		        fclose($fp);
                
                return true;
            }else
            {
                return false;   
            }            			
		}
        
        function update($user){
            if(file_exists($this->getFileName($user->getEmail()))){
                $json = json_encode($user);						
		        $fp = fopen ("data/".$user->getEmail().".txt", "w");		
		        fwrite($fp, $json);		
		        fclose($fp);
                
                return true;
            }else
            {
                return false;   
            }
        }
        
        function delete($email){
            if(file_exists($this->getFileName($email))){
                return unlink($this->getFileName($email));
            }
            
            return false;
        }
		
		function getByEmail($email){
            if(file_exists($this->getFileName($email))){
                $json = file($this->getFileName($email));				
		        return $json[0];	            			
            }
		}
        
        function getAllUser(){            
            $iterator = new DirectoryIterator('data');
  
            $users = Array();
            foreach ( $iterator as $entry ) {
                if($entry->isDot()) continue; 
                
                $user = file($entry->getPathname())[0];
                
                array_push($users, json_decode($user));                
            }
            
            if(count($users) == 0){
                return null;
            }
            
            return json_encode($users);
        }
        
        private function getFileName($email){
            return "data/".$email.".txt";
        }
	}
?>