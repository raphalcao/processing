# FIAP - HACKATON - SOFTWARE ARCHITECTURE

<a href="https://youtu.be/c8wwFFnbYAY">
    <img width="100%" alt="01 Captura de tela" src="https://github.com/user-attachments/assets/dc89700a-d777-4754-ac7f-d2c4b73764de">
</a>

## INTEGRANTE

- RM353824 - Raphael

## Passo a passo para inicialização da aplicação

### Se tiver o Make instalado
### OBS: No Windows, abrir o terminal WSL do VSCODE.

Use os commandos: 

    `make start`

Para fazer a limpeza da aplicação, use o comando:

    `make clean`


### Se não tiver o Make instalado

1. Clone o repositório  
   `git clone https://github.com/raphalcao/processing.git`

2. Acesse a pasta do projeto com o terminal 

3. Copie o arquivo `.env.example` para `.env`    
   `cp .env.example .env`

4. Iniciando os containers do Docker.  
   Esse processo pode demorar um pouco na primeira vez que for executado, pois o docker irá baixar as imagens necessárias para a execução dos containers.  
   Execute o comando:    
   `docker-compose up -d`

5. Acesse o container da aplicação com o comando:  
   `docker exec -it php bash`

6. Para instalar as dependências do projeto, execute o comando dentro do container:  
   `composer install`

7. Crie uma chave para a aplicação com o comando:  
   `php artisan key:generate`

8. Para criar as tabelas no banco de dados, execute o comando:  
   `php artisan migrate:fresh`

9. Para popular o banco de dados, execute o comando:  
   `php artisan db:seed`

10. Acesse a aplicação com o endereço  
    [http://localhost:8100](http://localhost:8100)

11. Acesse o Swagger com o endereço  
    [http://localhost:8100/api/documentation](http://localhost:8100/api/documentation)


### Se tiver usando sistema operacional Windows, faça o seguinte passo: (Necessário ter instalado o docker desktop)

No Docker Desktop habilitar o uso com o wsl. 

1. Instalar o Docker Desktop (se não estiver instalado)
   1. Baixe e instale o Docker Desktop.
      Durante a instalação, certifique-se de habilitar a integração com o WSL 2.

2. Habilitar a Integração do Docker com o WSL 2
   1. Abra o Docker Desktop.
   2. Clique no ícone de configurações (⚙️).
   3. Vá até Resources > WSL Integration.
   4. Ative a opção Enable the integration with my default WSL distro.
   5. Certifique-se de selecionar a distribuição que você está usando (ex.: Ubuntu).


No PowerShell:

1. No PowerShell, execute: wsl --install

2. No terminal wsl: 
      sudo apt update
      sudo apt install make

3. Verifique se o Make está funcionando:
   make --version

4. Ir para o diretório que está o arquivo Marketfile. Ex: 
   cd /mnt/c/xampp/htdocs/auth
   Utilizar este trecho cd /mnt/ e adicionar o restante do seu diretório local


## Para remover a aplicação

### Se tiver o Make instalado, use o comando:

    `make clean`

### Se não tiver o Make instalado, siga os passos abaixo:

1. Execute o comando  
   `docker-compose down`
2. **(Recomendado)** excluir a pasta do mysql dentro de ./docker/database/volumes/mysql. Vai poupar espaço.  
   `rm -Rf ./docker/database/volumes/mysql`

3. **OBS** Caso apresente erro de porta via wsl, execute o container do mysql via powerShell do windows.
   docker compose down
   docker compose up -d
