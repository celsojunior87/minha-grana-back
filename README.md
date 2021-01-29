# minha grana back

Estrutura

> /var/www/html
>
>maria-amelia-doces
>
>back
>
>front 

- Criar uma pasta www/html/maria-amelia-doces
    >  mkdir /var/www/html/maria-amelia-doces
                                                
- Dentro da pasta criada a cima, clonar o repositório back-end
    > git clone https://github.com/rothink/maria-amelia-doces-back.git back
                                                                 
- Entrar na pasta <strong>back</strong>
    > cd /var/www/html/maria-amelia-doces/back
                  
- Instalar as dependências                  
    > composer install

- No banco de dados, crie um banco com o seguinte comando:
    > create database 
  > 
  maria_amelia_doces;

- Criar um arquivo .env & ajustar user e password
    > sudo cp .env.example .env && sudo chmod 777 -R .env
                          
- Rodar as migrations e seeds                           
    >  php artisan migrate && php artisan migrate:refresh --seed
                                                                              
- Gerar a key da aplicação
    > php artisan key:generate                                                             

- Instalar o passport do Laravel                                                   
    > php artisan passport:install

- Rodar o servidor
    > php artisan serve
