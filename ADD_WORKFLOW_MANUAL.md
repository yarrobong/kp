# Как добавить workflow файл вручную

Токен GitHub не имеет прав для создания workflow файлов. Добавьте файл вручную:

## Способ 1: Через веб-интерфейс GitHub (РЕКОМЕНДУЕТСЯ)

1. Перейдите на https://github.com/yarrobong/kp
2. Нажмите кнопку **Add file** → **Create new file**
3. Введите путь: `.github/workflows/deploy.yml`
4. Скопируйте содержимое ниже и вставьте в файл:
5. Нажмите **Commit new file**

### Содержимое файла `.github/workflows/deploy.yml`:

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
        sshpass -p "$SSH_PASSWORD" ssh -o StrictHostKeyChecking=no root@${{ secrets.SERVER_HOST }} << 'ENDSSH'
          set -e
          
          cd ${{ secrets.DEPLOY_PATH }}
          
          echo "Starting deployment..."
          
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

## Способ 2: Обновить токен GitHub

1. Перейдите на https://github.com/settings/tokens
2. Создайте новый токен с правами:
   - ✅ `repo` (полный доступ к репозиторию)
   - ✅ `workflow` (обновление GitHub Actions workflows)
3. Используйте новый токен для push

## Способ 3: Использовать SSH ключ вместо токена

Настройте SSH ключ для GitHub и используйте SSH URL вместо HTTPS.

---

После добавления workflow файла настройте секреты в GitHub (см. GITHUB_DEPLOY_SETUP.md)

