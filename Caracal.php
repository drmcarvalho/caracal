<?php
/*!
 * Caracal micro-framework
 * Version 1.0.0
 *
 * Copyright 2019, Dener Carvalho
 * Released under the MIT license
 */

$url = dirname($_SERVER['SCRIPT_NAME']);
$url = $url === '\\' ? '' : $url;
define('CARACAL_PATH', $url . '/');

require 'Medoo.php';

class Caracal {
	public $database;

	private $routes = array();
	private $codes = array();	

	public function __construct() {
		
	}	

	public function action($path, $method, $callback) {
		if (!isset($this->routes[$path])) {
			$this->routes[$path] = array();
		}		
		$this->routes[$path][strtoupper(trim($method))] = $callback;
	}

	public function handlerCodes(array $codes, $callback) {
		foreach ($codes as $code) {
			$this->codes[$code] = $callback;
		}
	}

	public function render($view, array $data=null) {
		if (!empty($data)) {
			extract($data, EXTR_SKIP);
		}
		extract(['caracalPath' => CARACAL_PATH], EXTR_SKIP);
		$path = 'templates/' . $view . '.php';
		if (!file_exists($path)) {			
			throw new Exception('Template "' .$path . '" nao encontrado.');
		}
		return include $path;
	}

	public function redirect($to, $code=302, $stop=true) {
		$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
 		$pos1 = strpos($path, '/');
 		$pos2 = strpos($path, '/', $pos1 + strlen('/'));
 		header('Location: ' . substr($path, 0, $pos2 + 1) . $to, false, $code);
 		if ($stop) {
  			exit;
 		}
	}

	public function json($data, $option=false, $stop=true) {
		if ($data and is_array($data)) {
			header('Content-Type: application/json');
			echo json_encode($data, $option);
			if ($stop) {
	  			exit;
	 		}
		}
		else {
			throw new Exception('Dados nao fornecido ao parametro data.');
		}
	}

	public function run() {
		//Pega o caminho absoluto da URL e remove o HOST e porta.
	    $requri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

	    //Pega o caminho do script atual.
	    $scriptname = $_SERVER['SCRIPT_NAME'];

	    //Se o caminho do script atual for igual ao da URL_PATH ou index.php significa que o script esta rodando na raiz do apache.
	    if ($requri !== $scriptname && $scriptname !== '/index.php') {
	    	//Remove o index.php do nome do script e remove a barra / final do nome da pasta se necessário.
	        $scriptname = rtrim(dirname($scriptname), '/');

	        //remove da URL atual a parte que representa a pasta a onde esta a LIB e mantém apenas o que é a rota "virutal".
	        $path = substr($requri, strlen($scriptname));

	        //Se retornar false significa que não esta em uma requisição de rota, então torna em raiz.
	        $path = $path === false ? '/' : $path;
	    } 
	    else {
	    	//rota raiz
	        $path = $requri;
	    }

		$method = $_SERVER['REQUEST_METHOD'];
		$callback = null;
		$code = 0;		
		if (isset($this->routes[$path])) {
			if (isset($this->routes[$path][$method])) {
				$callback = $this->routes[$path][$method];				
			}
			else {				
				$code = 405;
			}
		}
		else {			
			$code = 404;
		}
		if ($code) {
			http_response_code($code);
			if (isset($this->codes[$code])) {
				$callback = $this->codes[$code];
			}
		}
		if ($callback) {
			if ($code) {
				$callback($code);
			}
			else {			
				$callback();							
			}
		}
	}
}