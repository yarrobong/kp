# 🔴 ТОЧНАЯ ИНСТРУКЦИЯ: Как исправить workflow

## Проблема

В логах видно, что workflow выполняет `git fetch origin` БЕЗ проверки, является ли директория git репозиторием. Это старая версия workflow на GitHub.

## Решение: Замените блок "Deploy to server"

### Шаг 1: Откройте файл

Перейдите: **https://github.com/yarrobong/kp/edit/main/.github/workflows/deploy.yml**

### Шаг 2: Найдите блок "Deploy to server"

Найдите строку:
```yaml
    - name: Deploy to server
```

### Шаг 3: Замените ВЕСЬ блок до строки с `ENDSSH`

Найдите блок, который начинается с:
```yaml
    - name: Deploy to server
      env:
        SSH_PASSWORD: ${{ secrets.SERVER_PASSWORD }}
      run: |
```

И замените ВСЁ до строки `ENDSSH` на этот код:

```yaml
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
          
          # ⚠️ ВАЖНО: Проверяем, является ли директория git репозиторием
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

### Шаг 4: Сохраните

1. Прокрутите вниз страницы
2. В поле "Commit changes" введите: `Fix: add git repository check`
3. Нажмите **"Commit changes"**

### Шаг 5: Проверьте

После сохранения автоматически запустится новый workflow. Проверьте:
- https://github.com/yarrobong/kp/actions

В логах должно появиться:
```
Starting deployment...
Not a git repository. Cloning...
```

Вместо ошибки `fatal: not a git repository`.

## Ключевое изменение

**БЫЛО (старая версия - НЕПРАВИЛЬНО):**
```bash
cd "${{ secrets.DEPLOY_PATH }}"
echo "Starting deployment..."
git fetch origin  # ❌ Ошибка, если нет .git
```

**СТАЛО (новая версия - ПРАВИЛЬНО):**
```bash
cd "${{ secrets.DEPLOY_PATH }}"
echo "Starting deployment..."

# ✅ Проверяем наличие .git
if [ ! -d .git ]; then
  echo "Not a git repository. Cloning..."
  git clone https://github.com/yarrobong/kp.git .
else
  echo "Git repository found. Updating..."
  git fetch origin
  git reset --hard origin/main
fi
```

