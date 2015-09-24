<?php
	Class User implements JsonSerializable{
		private $name;
		private $email;
		private $password;
		
		function setName($name){
			$this->name = $name;
		}
		
		function getName(){
			return $this->name;
		}
		
		function setEmail($email){
			$this->email = $email;
		}
		
		function getEmail(){
			return $this->email;
		}
		
		function setPassword($password){
			$this->password = $password;
		}
		
		function getPassword(){
			return $this->password;
		}
				
        public function jsonSerialize() {
            return [
                'name' => $this->name,                
                'email' => $this->email,
                'password' => $this->password                            
            ];
        }
            
        
	}
?>