# Variáveis do projeto (padrão meuprojeto)

CHAVE-SSH = informatica
PROJECT_NAME = meuprojeto
APP_CONTAINER = $(PROJECT_NAME)-app
MYSQL_CONTAINER = $(PROJECT_NAME)-mysql
NGINX_CONTAINER = $(PROJECT_NAME)-nginx

# Credenciais do banco (ajustadas conforme docker compose.yaml)
DB_USER = bancoMySql_user
DB_NAME = bancoMySql_db

.PHONY: setup dev build down clean logs shell mysql status migrate seed fresh clear test

# Configuração inicial completa
setup:
	docker compose down
	docker compose up -d
	docker exec -it $(APP_CONTAINER) composer install
	docker exec -it $(APP_CONTAINER) php artisan key:generate
	docker exec -it $(APP_CONTAINER) php artisan migrate:refresh --seed
	@echo "✅ Setup completo! Acesse: http://localhost:8000"

# Desenvolvimento
dev:
	docker compose up -d --build
	@echo "✅ Containers iniciados! Acesse: http://localhost:8000"

# Up de produção
up:
	docker compose up -d
	@echo "✅✅✅✅✅✅✅✅✅"
	@echo "✅ Build concluído! ✅"
	@echo "✅✅✅✅✅✅✅✅✅"

# Parar containers
down:
	docker compose down
	@echo "✅✅✅✅✅✅✅✅✅✅"
	@echo "✅ Serviço Parado! ✅"
	@echo "✅✅✅✅✅✅✅✅✅✅"

# Limpeza completa
docker-clean:
	docker compose down -v
	docker system prune -f

# Ver logs
logs:
	docker compose logs -f

# Entrar no container da aplicação
shell:
	docker exec -it $(APP_CONTAINER) bash

# Conectar ao MySQL
mysql:
	docker compose exec $(MYSQL_CONTAINER) mysql -u $(DB_USER) -p $(DB_NAME)

# Status dos containers
status:
	docker compose ps

# Executar testes
test:
	docker exec -it $(APP_CONTAINER) php artisan test>testes-unitarios.txt

# Comandos Laravel úteis
migrate:
	docker exec -it $(APP_CONTAINER) php artisan migrate

seed:
	docker exec -it $(APP_CONTAINER) php artisan db:seed

migrate-refresh-seed:
	docker exec -it $(APP_CONTAINER) php artisan migrate:refresh --seed

fresh:
	docker exec -it $(APP_CONTAINER) php artisan migrate:fresh --seed

# Limpar cache Laravel
clear:
	docker exec -it $(APP_CONTAINER) php artisan cache:clear
	docker exec -it $(APP_CONTAINER) php artisan config:clear
	docker exec -it $(APP_CONTAINER) php artisan route:clear
	docker exec -it $(APP_CONTAINER) php artisan optimize:clear
	docker exec -it $(APP_CONTAINER) php artisan optimize

permissao:
	sudo chmod -R 777 -R ./*
	sudo chmod -R 777 -R ./
	sudo chown -R samuel:samuel ./*
	sudo chown -R samuel:samuel ./

git-reset-hard:
	git reset --hard origin/$(git rev-parse --abbrev-ref HEAD) && git clean -fd && git pull

push:
	@MESSAGE="$(filter-out $@,$(MAKECMDGOALS))"; \
	if [ -z "$$MESSAGE" ]; then \
		echo "Erro: Forneça uma mensagem de commit. Ex: make push 'Sua frase com espaços'"; \
		exit 1; \
	fi; \
	git add . && git commit -m "$$MESSAGE" && git push; \
	echo "✅ Push realizado com mensagem: $$MESSAGE"

%:
	@: