<h1 align="center">Projeto PESQUEI (API)</h2>


## Sobre o Pesquei

Esse projeto foi idealizado como parte do processo de desenvolvimento proposto pela disciplina Prática Profissional do curso de Análise e Desenvolvimento de Sistemas da UNINASSAU.

O Pesquei é uma aplicação web para pescadores que frequentam locais de pescaria variados e precisam manter um registro desses locais e assim saber quais são os pontos mais produtivos, além de registrar os peixes que foram capturados com fotos e informações detalhadas.


- Salva os pontos de pesca com coordenadas geográficas;
- Registra os peixes capturados nos pontos selecionados;
- Vincula fotos dos peixes pescados;
- Os locais de pesca registrados são privados e visíveis apenas para o usuário autenticado os criou;
- Informações meteorológicas precisas do local selecionado pelo usuário (apenas Brasil);

## Informações sobre a arquitetura

Rest API desenvolvida utilizando o framework Laravel versão 9.9.0.

Pré-requisitos para essa aplicação:
-	PHP 8.1
-	BCMath PHP Extension
-	Ctype PHP Extension
-	Fileinfo PHP extension
-	JSON PHP Extension
-	Mbstring PHP Extension
-	OpenSSL PHP Extension
-	PDO PHP Extension
-	Tokenizer PHP Extension
-	XML PHP Extension
-	MySQL 8.0
-	Apache2
-	Chave de API do Google Maps
-	Chave de API do OpenWeatherMap


## Instruções de uso

- Ter todos os pré-requisitos resolvidos;
- Criar uma base de dados no MySQL;
- Criar um usuário para a base de dados;
- Clonar o repositório;
- Através de um terminal de comando, acessar o diretório raiz da aplicação e executar o comando: <b>php composer.phar install</b>
- Após a instalação das dependências, edite o arquivo <b>.env.example</b> que está no diretório raiz e preencha as informações do banco de dados, respectivo usuário, chave de API do Google Maps e chave de API do OpenWeatherMap;
- Após a edição, renomeie o arquivo para apenas <b>.env</b>;
- Execute o comando: <b>php artisan key:generate</b>
- Execute o comando: <b>php artisan migrate</b>
- Execute o comando: <b>php artisan db:seed</b>
- Inicie a aplicação com o comando: <b>php artisan serve</b>
