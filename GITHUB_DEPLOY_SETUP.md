# Настройка автоматического деплоя с GitHub на сервер

## Шаг 1: Первоначальная настройка на сервере

Подключитесь к серверу по SSH и выполните:

```bash
ssh root@178.209.127.17
```

Затем выполните скрипт настройки (укажите путь, куда деплоить проект):

```bash
# Если проект должен быть в /var/www/html
bash <(curl -s https://raw.githubusercontent.com/yarrobong/kp/main/deploy.sh) /var/www/html

# Или если в другой директории, например /home/www/site
bash <(curl -s https://raw.githubusercontent.com/yarrobong/kp/main/deploy.sh) /home/www/site
```

**Или вручную:**

```bash
cd /var/www/html  # или ваш путь
git clone https://github.com/yarrobong/kp.git .
composer install --no-dev --optimize-autoloader
chmod -R 755 storage bootstrap/cache
```

## Шаг 2: Настройка GitHub Secrets

1. Перейдите в репозиторий на GitHub: https://github.com/yarrobong/kp
2. Откройте **Settings** → **Secrets and variables** → **Actions**
3. Нажмите **New repository secret** и добавьте следующие секреты:

### SERVER_HOST
```
178.209.127.17
```

### SERVER_PASSWORD
```
vJq*s-YNG5XQt7
```

### DEPLOY_PATH
```
/var/www/html
```
*(Или путь, куда вы установили проект на сервере)*

## Шаг 3: Проверка работы

После настройки секретов:

1. Сделайте любой коммит и push в ветку `main`:
```bash
git add .
git commit -m "Test deployment"
git push origin main
```

2. Перейдите в раздел **Actions** на GitHub
3. Вы увидите запущенный workflow "Deploy to Server"
4. После успешного выполнения код будет автоматически обновлен на сервере

## Альтернативный вариант: Использование SSH ключа (более безопасно)

Вместо пароля можно использовать SSH ключ:

### На локальной машине:

```bash
# Генерируем SSH ключ (если еще нет)
ssh-keygen -t rsa -b 4096 -C "github-actions"

# Копируем публичный ключ на сервер
ssh-copy-id root@178.209.127.17
```

### На GitHub:

1. Добавьте секрет **SSH_PRIVATE_KEY** с содержимым приватного ключа (`~/.ssh/id_rsa`)
2. Удалите секрет **SERVER_PASSWORD** (он больше не нужен)
3. Обновите workflow файл, используя версию с SSH ключом

## Устранение проблем

### Ошибка: "Permission denied"
- Убедитесь, что путь `DEPLOY_PATH` правильный
- Проверьте права доступа к директории на сервере

### Ошибка: "git: command not found"
- Установите git на сервере: `yum install git` или `apt-get install git`

### Ошибка: "composer: command not found"
- Установите Composer на сервере или загрузите папку `vendor/` вручную

### Ошибка подключения по SSH
- Проверьте, что порт 22 открыт
- Убедитесь, что пароль правильный
- Попробуйте подключиться вручную: `ssh root@178.209.127.17`

## Безопасность

⚠️ **ВАЖНО:** После настройки деплоя:
1. Смените root пароль на более надежный
2. Рассмотрите использование SSH ключей вместо пароля
3. Ограничьте доступ к серверу по IP (firewall)
4. Не храните пароли в открытом виде в коде

