# 🔧 СРОЧНО: Обновите workflow файл на GitHub!

## Проблема

Workflow пытается выполнить `git fetch origin` в директории, которая не является git репозиторием. Нужно обновить workflow файл на GitHub.

## Решение: Обновите workflow вручную

### Шаг 1: Откройте файл для редактирования

Перейдите: **https://github.com/yarrobong/kp/edit/main/.github/workflows/deploy.yml**

### Шаг 2: Замените весь файл на эту версию

Скопируйте весь код ниже и вставьте в редактор GitHub:

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
        if [ -z "${{ secrets.SERVER_HOST }}" ]; then
          echo "ERROR: SERVER_HOST secret is not set!"
          exit 1
        fi
        echo "Adding ${{ secrets.SERVER_HOST }} to known_hosts..."
        ssh-keyscan -H ${{ secrets.SERVER_HOST }} >> ~/.ssh/known_hosts || true
        
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
            # Очищаем директорию если она не пустая
            if [ "$(ls -A)" ]; then
              echo "Directory is not empty. Moving existing files..."
              mkdir -p ../backup_$(date +%Y%m%d_%H%M%S)
              mv * ../backup_$(date +%Y%m%d_%H%M%S)/ 2>/dev/null || true
              mv .* ../backup_$(date +%Y%m%d_%H%M%S)/ 2>/dev/null || true
            fi
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

### Шаг 3: Сохраните изменения

1. Прокрутите вниз страницы
2. В поле "Commit changes" введите: `Fix: add git repository check before fetch`
3. Нажмите **"Commit changes"** (зеленая кнопка)

### Шаг 4: Проверьте работу

После сохранения workflow автоматически запустится. Проверьте:
- https://github.com/yarrobong/kp/actions

Должно работать! ✅

## Что изменилось?

Новая версия workflow:
1. ✅ Проверяет, является ли директория git репозиторием (`if [ ! -d .git ]`)
2. ✅ Если нет - клонирует репозиторий (`git clone`)
3. ✅ Если есть - обновляет код (`git fetch` + `git reset`)
4. ✅ Обрабатывает случай, когда директория не пустая

