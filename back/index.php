<?php 
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require 'vendor/autoload.php';

$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

$config['db']['host']   = "localhost";
$config['db']['user']   = "root";
$config['db']['pass']   = "root";
$config['db']['dbname'] = "blogr";

$app = new \Slim\App(["settings" => $config]);
$container = $app->getContainer();

$container['logger'] = function($c) {
    $logger = new \Monolog\Logger('blogr_logger');
    $file_handler = new \Monolog\Handler\StreamHandler("logs/blogr.log");
    $logger->pushHandler($file_handler);
    return $logger;
};

$container['db'] = function ($c) {
    // echo "hello world ---------------------------------------------------";
    $db = $c['settings']['db'];
    $pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'],
        $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

$app->get('/hello/{name}', function (Request $request, Response $response) {
    $name = $request->getAttribute('name');
    $this->logger->addInfo("hello " . $name);
    $response->getBody()->write("Hello, $name");
    return $response;
});

$app->group('/user', function() use ($app) {
	$app->get('' , function($request, $response, $args) {
		$this->logger->addInfo("url: localhost:8000/user/ method: get");	
		$temp = $this->db->query('select * from User');
		$temp2 = $temp->fetchAll(PDO::FETCH_ASSOC);
		$responseBody = $response->withJson($temp2);
		return $responseBody;
	});
	$app->get('/{id}' , function($request, $response, $args) {
		$this->logger->addInfo("url: localhost:8000/user/" . $args["id"] ." method: get");	
		$temp = $this->db->query('select * from User where id="'.$args["id"].'"');
		$temp2 = $temp->fetchAll(PDO::FETCH_ASSOC);
		if(empty($temp2))
		{
			$responseBody = $response->withStatus(302);
		}
		else
		{
			$responseBody = $response->withJson($temp2);
		}
		return $responseBody;
	});
	$app->post('/' , function($request, $response, $args) {
		// $keys = [];
		$values = [];
		$this->logger->addInfo("url: localhost:8000/user/ method: post");
		$sql = "INSERT INTO User (";
		$temp = $request->getParams();
		foreach( $temp as $key => $value)
		{
			$sql .= " " . $key . ",";
			// array_push($keys, $key);
			array_push($values, $value);
		}
		$sql = substr($sql, 0, -1);
		$sql .= " ) VALUES (";
		for($i = 0; $i < count($values);$i++)
		{
			$sql .= ' "' . $values[$i] . '",'; 
		}
		$sql = substr($sql, 0 , -1);
		$sql .= " );";
		var_dump($sql);
   		//POST or PUT parameters list(varname)
		$temp = $this->db->exec($sql);
		// $temp2 = $temp->fetchAll(PDO::FETCH_ASSOC);
		// var_dump($temp);

	});
	$app->put('/{id}' , function($request, $response, $args) {
		$this->logger->addInfo("url: localhost:8000/user/" . $args["id"]. " method: put");
		$temp = $request->getParams();
		var_dump($temp);	
// $sql = "UPDATE User set";
		// var_dump($request);
		// foreach($args as $key => $value)
		// {
			// var_dump($key);
			// var_dump($value);
		// }
		// $temp = $this->db->query('select * from User where id="'.$args["id"].'"');
		// $temp2 = $temp->fetchAll(PDO::FETCH_ASSOC);
		// var_dump($temp2);
	});
	$app->delete('/{id}' , function($request, $response, $args) {
		$this->logger->addInfo("url: localhost:8000/blog/" . $args["id"] ." method: delete");	
		$temp = $this->db->query('DELETE FROM User where id = "'.$args["id"].'"');
		$temp2 = $temp->fetchAll(PDO::FETCH_ASSOC);
		var_dump($temp2);
	});
});

$app->group('/blog', function() use ($app) {
	$app->get('' , function($request, $response, $args) {
		$this->logger->addInfo("url: localhost:8000/blog/ method: get");	
		$temp = $this->db->query('select * from Blog');
		$temp2 = $temp->fetchAll(PDO::FETCH_ASSOC);
		var_dump($temp2);
	});
	$app->get('/{id}' , function($request, $response, $args) {
		$this->logger->addInfo("url: localhost:8000/blog/" . $args["id"] ." method: get");	
		$temp = $this->db->query('select * from Blog where id="'.$args["id"].'"');
		$temp2 = $temp->fetchAll(PDO::FETCH_ASSOC);
		return $response->withjson($temp2);
		// var_dump($temp2);
	});
	$app->post('/' , function($request, $response, $args) {
		// $keys = [];
		$values = [];
		$this->logger->addInfo("url: localhost:8000/blog/ method: post");
		$sql = "INSERT INTO Blog (";
		$temp = $request->getParams();
		foreach( $temp as $key => $value)
		{
			$sql .= " " . $key . ",";
			// array_push($keys, $key);
			array_push($values, $value);
		}
		$sql = substr($sql, 0, -1);
		$sql .= " ) VALUES (";
		for($i = 0; $i < count($values);$i++)
		{
			$sql .= ' "' . $values[$i] . '",'; 
		}
		$sql = substr($sql, 0 , -1);
		$sql .= " );";
		var_dump($sql);
   		//POST or PUT parameters list(varname)
		$temp = $this->db->exec($sql);
		// $temp2 = $temp->fetchAll(PDO::FETCH_ASSOC);
		// var_dump($temp);
	});
	$app->put('/{id}' , function($request, $response, $args) {
		$this->logger->addInfo("url: localhost:8000/blog/" . $args["id"]. " method: put");	
		$sql = "UPDATE User set";
		// var_dump($request);
		foreach($args as $key => $value)
		{
			var_dump($key);
			var_dump($value);
		}
		// $temp = $this->db->query('select * from User where id="'.$args["id"].'"');
		// $temp2 = $temp->fetchAll(PDO::FETCH_ASSOC);
		// var_dump($temp2);
	});
	$app->delete('/{id}' , function($request, $response, $args) {
		$this->logger->addInfo("url: localhost:8000/blog/" . $args["id"] ." method: delete");	
		$temp = $this->db->query('DELETE FROM Blog where id = "'.$args["id"].'"');
		$temp2 = $temp->fetchAll(PDO::FETCH_ASSOC);
		var_dump($temp2);
	});
});

$app->group('/artical', function() use ($app) {
	$app->get('' , function($request, $response, $args) {
		$this->logger->addInfo("url: localhost:8000/artical/ method: get");	
		$temp = $this->db->query('select * from User');
		$temp2 = $temp->fetchAll(PDO::FETCH_ASSOC);
		return $response->withjson($temp2);
	});
	$app->get('/{id}' , function($request, $response, $args) {
		$this->logger->addInfo("url: localhost:8000/artical/" . $args["id"] ." method: get");	
		$temp = $this->db->query('select * from Artical where id="'.$args["id"].'"');
		$temp2 = $temp->fetchAll(PDO::FETCH_ASSOC);
		return $response->withjson($temp2);
	});
	$app->post('/' , function($request, $response, $args) {
		// $keys = [];
		$values = [];
		$this->logger->addInfo("url: localhost:8000/artical/ method: post");
		$sql = "INSERT INTO Artical (";
		$temp = $request->getParams();
		foreach( $temp as $key => $value)
		{
			$sql .= " " . $key . ",";
			// array_push($keys, $key);
			array_push($values, $value);
		}
		$sql = substr($sql, 0, -1);
		$sql .= " ) VALUES (";
		for($i = 0; $i < count($values);$i++)
		{
			$sql .= ' "' . $values[$i] . '",'; 
		}
		$sql = substr($sql, 0 , -1);
		$sql .= " );";
		var_dump($sql);
   		//POST or PUT parameters list(varname)
		$temp = $this->db->exec($sql);
		// $temp2 = $temp->fetchAll(PDO::FETCH_ASSOC);
		// var_dump($temp);
	});
	$app->put('/{id}' , function($request, $response, $args) {
		$this->logger->addInfo("url: localhost:8000/artical/" . $args["id"]. " method: put");	
		$sql = "UPDATE Artical set";
		// var_dump($request);
		foreach($args as $key => $value)
		{
			var_dump($key);
			var_dump($value);
		}
		// $temp = $this->db->query('select * from User where id="'.$args["id"].'"');
		// $temp2 = $temp->fetchAll(PDO::FETCH_ASSOC);
		// var_dump($temp2);
	});
	$app->delete('/{id}' , function($request, $response, $args) {
		$this->logger->addInfo("url: localhost:8000/artical/" . $args["id"] ." method: delete");	
		$temp = $this->db->query('DELETE FROM Artical where id = "'.$args["id"].'"');
		$temp2 = $temp->fetchAll(PDO::FETCH_ASSOC);
		var_dump($temp2);
	});
});
$app->group('/comment', function() use ($app) {
	$app->get('' , function($request, $response, $args) {
		$this->logger->addInfo("url: localhost:8000/comment/ method: get");	
		$temp = $this->db->query('select * from Comment');
		$temp2 = $temp->fetchAll(PDO::FETCH_ASSOC);
		return $response->withjson($temp2);
	});
	$app->get('/{id}' , function($request, $response, $args) {
		$this->logger->addInfo("url: localhost:8000/comment/" . $args["id"] ." method: get");	
		$temp = $this->db->query('select * from Comment where id="'.$args["id"].'"');
		$temp2 = $temp->fetchAll(PDO::FETCH_ASSOC);
		return $response->withjson($temp2);
	});
	$app->post('/' , function($request, $response, $args) {
		// $keys = [];
		$values = [];
		$this->logger->addInfo("url: localhost:8000/comment/ method: post");
		$sql = "INSERT INTO Comment (";
		$temp = $request->getParams();
		foreach( $temp as $key => $value)
		{
			$sql .= " " . $key . ",";
			// array_push($keys, $key);
			array_push($values, $value);
		}
		$sql = substr($sql, 0, -1);
		$sql .= " ) VALUES (";
		for($i = 0; $i < count($values);$i++)
		{
			$sql .= ' "' . $values[$i] . '",'; 
		}
		$sql = substr($sql, 0 , -1);
		$sql .= " );";
		var_dump($sql);
   		//POST or PUT parameters list(varname)
		$temp = $this->db->exec($sql);
		// $temp2 = $temp->fetchAll(PDO::FETCH_ASSOC);
		// var_dump($temp);
	});
	$app->put('/{id}' , function($request, $response, $args) {
		$this->logger->addInfo("url: localhost:8000/comment/" . $args["id"]. " method: put");	
		$sql = "UPDATE Comment set";
		// var_dump($request);
		foreach($args as $key => $value)
		{
			var_dump($key);
			var_dump($value);
		}
		// $temp = $this->db->query('select * from User where id="'.$args["id"].'"');
		// $temp2 = $temp->fetchAll(PDO::FETCH_ASSOC);
		// var_dump($temp2);
	});
	$app->delete('/{id}' , function($request, $response, $args) {
		$this->logger->addInfo("url: localhost:8000/comment/" . $args["id"] ." method: delete");	
		$temp = $this->db->query('DELETE FROM comment where id = "'.$args["id"].'"');
		$temp2 = $temp->fetchAll(PDO::FETCH_ASSOC);
		var_dump($temp2);
	});
});
$app->group('/category', function() use ($app) {
	$app->get('' , function($request, $response, $args) {
		$this->logger->addInfo("url: localhost:8000/category/ method: get");	
		$temp = $this->db->query('select * from Category');
		$temp2 = $temp->fetchAll(PDO::FETCH_ASSOC);
		return $response->withjson($temp2);
	});
	$app->get('/{id}' , function($request, $response, $args) {
		$this->logger->addInfo("url: localhost:8000/category/" . $args["id"] ." method: get");	
		$temp = $this->db->query('select * from Cateogry where id="'.$args["id"].'"');
		$temp2 = $temp->fetchAll(PDO::FETCH_ASSOC);
		return $response->withjson($temp2);
	});
	$app->post('/' , function($request, $response, $args) {
		// $keys = [];
		$values = [];
		$this->logger->addInfo("url: localhost:8000/category/ method: post");
		$sql = "INSERT INTO Cateogry (";
		$temp = $request->getParams();
		foreach( $temp as $key => $value)
		{
			$sql .= " " . $key . ",";
			// array_push($keys, $key);
			array_push($values, $value);
		}
		$sql = substr($sql, 0, -1);
		$sql .= " ) VALUES (";
		for($i = 0; $i < count($values);$i++)
		{
			$sql .= ' "' . $values[$i] . '",'; 
		}
		$sql = substr($sql, 0 , -1);
		$sql .= " );";
		var_dump($sql);
   		//POST or PUT parameters list(varname)
		$temp = $this->db->exec($sql);
		// $temp2 = $temp->fetchAll(PDO::FETCH_ASSOC);
		// var_dump($temp);
	});
	$app->put('/{id}' , function($request, $response, $args) {
		$this->logger->addInfo("url: localhost:8000/category/" . $args["id"]. " method: put");	
		$sql = "UPDATE Category set";
		// var_dump($request);
		foreach($args as $key => $value)
		{
			var_dump($key);
			var_dump($value);
		}
		// $temp = $this->db->query('select * from User where id="'.$args["id"].'"');
		// $temp2 = $temp->fetchAll(PDO::FETCH_ASSOC);
		// var_dump($temp2);
	});
	$app->delete('/{id}' , function($request, $response, $args) {
		$this->logger->addInfo("url: localhost:8000/cateogry/" . $args["id"] ." method: delete");	
		$temp = $this->db->query('DELETE FROM Category where id = "'.$args["id"].'"');
		$temp2 = $temp->fetchAll(PDO::FETCH_ASSOC);
		var_dump($temp2);
	});
});

$app->group('/v1', function() use ($app) {
	$app->get('/blog/{id}' , function($request, $response, $args) {
		$output;
		$this->logger->addInfo("url: localhost:8000/v1/artical/ method: get");	
		$query = $this->db->query('select * from Blog where id="'.$args["id"].'"');
		$output["blog"]= $query->fetchAll(PDO::FETCH_ASSOC);
		$query = $this->db->query('select * from Artical where blog_id = "'.$args["id"].'"');
		$output["articals"] = $query->fetchAll(PDO::FETCH_ASSOC);
		$responseBody = $response->withJson($output);
		return $responseBody;
	});
	$app->get('/artical/{id}' , function($request, $response, $args) {
		$output;
		$this->logger->addInfo("url: localhost:8000/v1/artical/" . $args["id"] ." method: get");	
		$query = $this->db->query('select * from Artical where id="'.$args["id"].'"');
		$output["blog"]= $query->fetchAll(PDO::FETCH_ASSOC);
		$query = $this->db->query('select * from Comment where artical_id = "'.$args["id"].'"');
		$output["articals"] = $query->fetchAll(PDO::FETCH_ASSOC);
		$responseBody = $response->withJson($output);
		return $responseBody;
	});
});
$app->run();