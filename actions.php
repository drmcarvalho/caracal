<?php

$app->action('/api/hello', 'GET', function () use ($app) {	
	$app->json(['versao' => '1.0', 'descricao' => 'Caracal nanoframework']);
});

$app->action('/home', 'GET', function ()use ($app) {
	$app->render('home', ['nome' => 'Gato']);
});

$app->action('/teste', 'GET', function () use ($app) {
	$app->redirect('home');	
});

$app->handlerCodes([404], function () {
	echo 'Nao encontrado';
});
