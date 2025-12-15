# Исправление workflow и диагностика проблем

## Проблема

Workflow запускается, но падает с ошибками. Нужно проверить логи и исправить проблемы.

## Шаг 1: Проверьте логи последнего запуска

1. Откройте: https://github.com/yarrobong/kp/actions
2. Кликните на последний запуск (например, "Deploy to Server #7")
3. Откройте шаг "Deploy to server"
4. Посмотрите логи - там будет указана точная ошибка

## Шаг 2: Обновите workflow файл на GitHub

Токен не может обновить workflow автоматически. Обновите вручную:

1. Откройте: https://github.com/yarrobong/kp/blob/main/.github/workflows/deploy.yml
2. Нажмите кнопку **Edit** (карандаш)
3. Замените содержимое на улучшенную версию ниже
4. Нажмите **Commit changes**

### Улучшенная версия workflow:

```yaml
name: Deploy to Server

on:
  push:
    branches:
      - main

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
    - name: Checkout code
      uses: actions/checkout@v3
      
    - name: Install sshpass
      run: |
        sudo apt-get update
        sudo apt-get install -y sshpass
        
    - name: Setup SSH known hosts
      run: |
        mkdir -p ~/.ssh
        ssh-keyscan -H ${{ secrets.SERVER_HOST }} >> ~/.ssh/known_hosts
        
    - name: Deploy to server
      env:
        SSH_PASSWORD: ${{ secrets.SERVER_PASSWORD }}
      run: |
        echo "Checking secrets..."
        if [ -z "${{ secrets.SERVER_HOST }}" ]; then
          echo "ERROR: SERVER_HOST secret is not set"
          exit 1
        fi
        if [ -z "${{ secrets.SERVER_PASSWORD }}" ]; then
          echo "ERROR: SERVER_PASSWORD secret is not set"
          exit 1
        fi
        if [ -z "${{ secrets.DEPLOY_PATH }}" ]; then
          echo "ERROR: DEPLOY_PATH secret is not set"
          exit 1
        fi
        
        echo "Connecting to server ${{ secrets.SERVER_HOST }}..."
        sshpass -p "$SSH_PASSWORD" ssh -o StrictHostKeyChecking=no -o ConnectTimeout=10 root@${{ secrets.SERVER_HOST }} << 'ENDSSH'
          set -e
          
          echo "Current directory: $(pwd)"
          echo "Deploy path: ${{ secrets.DEPLOY_PATH }}"
          
          # Проверяем существование директории
          if [ ! -d "${{ secrets.DEPLOY_PATH }}" ]; then
            echo "Creating directory ${{ secrets.DEPLOY_PATH }}..."
            mkdir -p "${{ secrets.DEPLOY_PATH }}"
          fi
          
          cd "${{ secrets.DEPLOY_PATH }}"
          echo "Changed to: $(pwd)"
          
          # Проверяем наличие git
          if ! command -v git &> /dev/null; then
            echo "ERROR: git is not installed on the server"
            exit 1
          fi
          
          echo "Starting deployment..."
          
          # Если это не git репозиторий, клонируем
          if [ ! -d .git ]; then
            echo "Not a git repository. Cloning..."
            git clone https://github.com/yarrobong/kp.git .
          else
            echo "Git repository found. Updating..."
            # Сохраняем .env если существует
            if [ -f .env ]; then
              cp .env .env.backup
            fi
            
            # Получаем последние изменения из GitHub
            git fetch origin
            git reset --hard origin/main
            
            # Восстанавливаем .env
            if [ -f .env.backup ]; then
              mv .env.backup .env
            fi
          fi
          
          # Устанавливаем зависимости если нужно
          if [ -f composer.json ] && command -v composer &> /dev/null; then
            echo "Installing Composer dependencies..."
            composer install --no-dev --optimize-autoloader --no-interaction || true
          fi
          
          # Устанавливаем права доступа
          echo "Setting permissions..."
          chmod -R 755 storage bootstrap/cache 2>/dev/null || true
          chmod 644 .env 2>/dev/null || true
          
          # Очищаем кеш если есть
          rm -rf storage/framework/cache/* storage/framework/views/* 2>/dev/null || true
          
          echo "Deployment completed successfully!"
        ENDSSH
```

## Шаг 3: Проверьте секреты

Убедитесь, что все секреты настроены:
- https://github.com/yarrobong/kp/settings/secrets/actions

Нужны секреты:
- `SERVER_HOST` = `178.209.127.17`
- `SERVER_PASSWORD` = `vJq*s-YNG5XQt7`
- `DEPLOY_PATH` = `/var/www/html`

## Шаг 4: Проверьте сервер

Подключитесь к серверу и проверьте:

```bash
ssh root@178.209.127.17

# Проверьте git
git --version

# Проверьте директорию
ls -la /var/www/html

# Если директории нет, создайте
mkdir -p /var/www/html
```

## Шаг 5: Тестовый запуск

После обновления workflow сделайте тестовый коммит:

```bash
git add .
git commit -m "Test deployment after workflow update"
git push origin main
```

Затем проверьте статус: https://github.com/yarrobong/kp/actions

## Частые ошибки и решения

### Ошибка: "Secret not found"
→ Проверьте, что все секреты добавлены в GitHub

### Ошибка: "Connection refused" или "Permission denied"
→ Проверьте доступность сервера: `ssh root@178.209.127.17`
→ Проверьте правильность пароля

### Ошибка: "git: command not found"
→ Установите git на сервере: `yum install git` или `apt-get install git`

### Ошибка: "No such file or directory"
→ Создайте директорию на сервере: `mkdir -p /var/www/html`

