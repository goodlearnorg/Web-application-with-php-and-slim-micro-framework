<?php
	Class User{
		private $name;
		private $email;
		private $age;
		
		public function __construct($name, $email, $age){
			$this->name = $name;
			$this->email = $email;
			$this->age = $age;
		}
		
		public function show(){
			var_dump($this);
			echo "Your name is <b>{$this->name}</b> <br>
				  Your email is <b>{$this->email}</b><br>
				  You are <b>{$this->age}</b> years old";
		}
		
		public function changeName($newName){
			$this->name = $newName;
		}
	}
?>