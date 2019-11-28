# O que é o Caracal?

Caracal é um simples e extensivo nano-framework Action Based que permite você desenvolver pequenas aplicações Web e APIs RESTful de maneira rapida e fácil. Ele possui o [Medoo](https://medoo.in/) como um database framework para gerenciar e manipular o banco de dados.

# License

Caracal é licenciado sobre a licença MIT.

# Instalação

1. Baixe os arquivos `Caracal.php` e `Medoo.php` e crie a pasta template no diretório root no qual vai conter seus templates/views.

2. Configure seu servidor Apache criando o arquivo  `.htaccess` com o seguinte conteúdo:

```
<IfModule mod_negotiation.c>
    Options -MultiViews
</IfModule>

IndexIgnore *

RewriteEngine On

# Check file or folders exists
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f

# Redirect all urls to index.php if no exits files/folder
RewriteRule ^ index.php [L]
```

3. Crie seu arquivo `app.php`.

Inclua o framework e defina uma instancia para ele:
```php
require_once 'Caracal.php';

$app = new Caracal();
```
Ou se você preferir pode passar o array com as opções do seu banco de dados que o Caracal vai setar a instancia do Medoo internamente:
```php
$app = new Caracal([
	'database_type' => 'mysql',
	'database_name' => 'minhabase',
	'server' => 'localhost',
	'username' => 'meuusuario',
	'password' => 'minhasenha'
]);
```
Veja na [documentação do Medoo](https://medoo.in/api/new) como conecta-lo com os SGBDs.

Crie seu arquivo de ações (rotas):
```php
require_once 'actions.php';
```

Dentro do arquivo `actions.php` defina sua ação (rota) atribuinto uma função para manipular a requisição HTTP:
```php
$app->action('/hello', 'GET', function() {
	echo 'Ola mundo!';
});
```

Inclua o php `actions.php` no arquivo `app.php`:
```php
require_once 'actions.php';
```

Finalmente, no arquivo `index.php` inclua o php `app.php` e inicie o framework:
```php
require_once 'app.php';

$app->run();
```

# Roteamento

O roteamento é feito mapeando uma URL para um callback na função `action()` do framework.
```php
$app->action('/hello', 'GET', function () {
	echo 'Ola mundo!';
});
```

## Views

Para retornar uma view use a função `render()` como no exemplo:
```php
$app->action('/home', 'GET', function () use ($app) {
	$app->render('home', ['variavel' => 'Caracal']);
});
```

O Caracal vai buscar pelo template especificado na pasta `templates` através do nome do primeiro argumento.

## Json

Respostas em json podem ser retornadas através da função `json()` como no exemplo:
```php
$app->action('/api/hello', 'GET', function () use ($app) {	
	$app->json(['versao' => '1.0', 'descricao' => 'Caracal nanoframework']);
});
```

# Redirecionamento

O Redirecionamento é feito pela função `redirect()` onde o parametro `to` é para onde vai ser redirecionado, por padrão é definido o código 302 para o redirecionamento, através do parametro `stop` pode encerrar o a ação ou continuar o processamento.
```php
$app->action('/teste', 'GET', function () use ($app) {
	$app->redirect('home');	
});
```

# Banco de dados

Para usar a instancia do Medoo utilize a propriedade `database` do framework:
```php
$app->action('/fornecedores', 'GET', function () use ($app) { 
	$fornecedores = $app->database->select('fornecedores', ['nome']);
	$app->json($fornecedores);
});
```
