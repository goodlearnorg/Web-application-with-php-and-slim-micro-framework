<?php
	
	require_once 'model/User.class.php';
    require_once 'controller/UserController.class.php';
	
	//require slim and load it
	require 'Slim/Slim.php';
	\Slim\Slim::registerAutoloader();
	
	//create an instance of the SLIM
	$app = new \Slim\Slim(array(
		'cookies.encrypt' => true,
		'templates.path' => 'view'		
	));
	
	// Create Slim session
	$app->add(new \Slim\Middleware\SessionCookie(array(
		'expires' => '10 minutes',
		'path' => '/',
		'domain' => null,
		'secure' => false,
		'httponly' => false,
		'name' => 'rest_api',
		'secret' => 'keysecret',
		'cipher' => MCRYPT_RIJNDAEL_256,
		'cipher_mode' => MCRYPT_MODE_CBC
	)));
	
	//verify if the user is authenticated
	function authenticate(){		
		if(!isset($_SESSION['user'])){
			return false;
		}
		
		return true;
	}
	
	//Intercept all request
	$app->hook('slim.before.dispatch', function() use ($app) {
		//URLs that can be accessed without authentication		
		$exceptions = array('/', '/app/login', '/app/adduser', '/user/add', '/login');		
		if(in_array($app->request->getResourceUri(), $exceptions)){
			return;
		}
	
	   // if user is unauthenticated, it can not access the methods
	   if (!authenticate()) {
		   //HTTP status 401 - Unauthorized
		  $app->render('401.html', array('url' => $app->request->getResourceUri() ));		   
		  $app->response->status(401);
		  $app->stop();
	   }	   
	});	
	
	//Method to do login
	$app->post('/login', function() use($app){
		$email = $app->request->params('email');
		$password = $app->request->params('password');
        
		$userCtrl = new UserController();
        $user = $userCtrl->getByEmail($email); 
        
        if(!$user){
            //HTTP status 404 - User not found
            $app->response->status(404);
			echo "<h1>Email or password incorrect</h2>";			
			return;
        }
		
		$user = json_decode($user);		
		
		if(!password_verify($password, $user->password) ){
			//HTTP status 404 - password is wrong
			$app->response->status(404);
			echo "<h1>Email or password incorrect</h2>";
			return;
		}

		$_SESSION['user'] = $user;
		
		$app->redirect($app->urlfor('index'));
		
	});
	
	$app->get('/logout', function() use($app){
		unset($_SESSION['user']);
		
		$app->redirect($app->urlfor('index'));
	})->name('logout');
	
	//Main method
	$app->get('/', function () use ($app){				
			
		$app->render('home.html', array('userAuthenticated' => authenticate()));
		
	})->name('index');
	
	//Method to add a user
	$app->put('/user/add', function() use ($app){		

		//capture the parameters sent by our form client
		$user = new User();
		$user->setName($app->request->params('name'));		
		$user->setEmail($app->request->params('email'));	
		
		//encrypt the password
		$password = password_hash($app->request->params('password'), PASSWORD_DEFAULT);
		
		$user->setPassword($password);
		
        $userCtrl = new UserController();
		$result = $userCtrl->save($user);
        
        if(!$result){
            //HTTP status 409 - Conflict, this occurs when already exist a user.
            $app->response->status(409);
        }
        
	});

    //Method to update a user
	$app->post('/user/update', function() use ($app){		

		//capture the parameters sent by our form client
		$user = new User();
		$user->setName($app->request->params('name'));		
		$user->setEmail($app->request->params('email'));	
		
		//encrypt the password
		$password = password_hash($app->request->params('password'), PASSWORD_DEFAULT);
		
		$user->setPassword($password);
		
        $userCtrl = new UserController();
		$result = $userCtrl->update($user);
        
        if(!$result){
            //HTTP status 409 - Conflict, this occurs when user was not found.
            $app->response->status(409);
        }
	});

    //Method to delete a user
    $app->delete('/user/delete', function() use ($app){
		$email = $app->request->params('email');
        $userCtrl = new UserController();        
        $result = $userCtrl->delete($email);
        
        if(!$result){
            //HTTP status 500 - Internal server error, this occurs when can't delete a user.
            $app->response->status(500);
			echo "We can't delete user";
        }else{
			echo "User deleted successfully";
			$app->redirect($app->urlfor('logout'));
		}
    });
	
	//Method to get a user
	$app->get('/user', function () use ($app) {
        $email = $app->request->params('email');
        
		$userCtrl = new UserController();
        $user = $userCtrl->getByEmail($email); 
        
        if(!$user){
            //HTTP status 404 - User not found
            $app->response->status(404);
        }
        
		$app->response->setBody($user);
	});

    //Method to get all user
	$app->get('/user/all', function () use ($app) {
		$userCtrl = new UserController();
        $users = $userCtrl->getAllUser();
                       
        if(!$users){
            //HTTP status 404 -  no User found
            $app->response->status(404);
        }
        
		$app->response->setBody($users);		
	});
	
	$app->group('/app', function() use ($app){
	
		$app->map('/adduser', function() use ($app){
			$app->render('addUser.html', array('data'=>$app->render('home.html', array('userAuthenticated'=>authenticate()))));
		})->via('GET', 'POST');
		
		$app->map('/edituser', function() use ($app){
			$app->render('editUser.html', array('data'=>$app->render('home.html', array('userAuthenticated'=>authenticate()))));			
		})->via('GET', 'POST');
		
		$app->get('/login', function() use ($app){
			if(authenticate()){
				$app->redirect($app->urlfor('index'));
			}else{
				$app->render('login.html', array('data'=>$app->render('home.html', array('userAuthenticated'=>authenticate()))));
			}			
		});	
		
		$app->get('/listuser', function() use ($app){
			$app->render('listUser.html', array('data'=>$app->render('home.html', array('userAuthenticated'=>authenticate()))));			
		});
		
	});	
	
	$app->notFound(function () use ($app) {
		$app->render('404.html', array('uriNotFound' => $app->request->getResourceUri()));
	});
	
	//execute our API
	$app->run();
?>