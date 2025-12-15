# Альтернативные способы автоматического деплоя (без GitHub Actions)

## Способ 1: Cron Job (Самый простой) ⭐ РЕКОМЕНДУЕТСЯ

Автоматически проверяет GitHub каждые 5 минут и обновляет код при изменениях.

### Установка на сервере:

```bash
ssh root@178.209.127.17

# Скачайте и запустите скрипт
cd /tmp
wget https://raw.githubusercontent.com/yarrobong/kp/main/deploy-cron.sh
chmod +x deploy-cron.sh
sudo bash deploy-cron.sh /var/www/html root
```

### Как это работает:

- Cron проверяет GitHub каждые 5 минут
- Если есть новые коммиты - автоматически обновляет код
- Логи: `/var/log/deploy-kp.log`

### Преимущества:
- ✅ Просто настроить
- ✅ Не требует настройки GitHub
- ✅ Работает автоматически
- ✅ Можно настроить интервал проверки

### Недостатки:
- ⏱️ Задержка до 5 минут (можно уменьшить)

---

## Способ 2: GitHub Webhook (Мгновенный деплой)

Деплой запускается сразу при push в GitHub.

### Установка на сервере:

```bash
ssh root@178.209.127.17

# Перейдите в директорию проекта
cd /var/www/html

# Если проект еще не клонирован
git clone https://github.com/yarrobong/kp.git .

# Сделайте скрипт исполняемым
chmod +x deploy-webhook.sh
```

### Настройка в GitHub:

1. Откройте: https://github.com/yarrobong/kp/settings/hooks
2. Нажмите **"Add webhook"**
3. Заполните:
   - **Payload URL:** `http://178.209.127.17/webhook.php`
     (или ваш домен, если настроен)
   - **Content type:** `application/json`
   - **Secret:** (оставьте пустым или создайте секрет)
   - **Events:** Выберите "Just the push event"
4. Нажмите **"Add webhook"**

### Преимущества:
- ✅ Мгновенный деплой при push
- ✅ Не требует cron
- ✅ Официальный способ GitHub

### Недостатки:
- ⚠️ Нужен публичный URL (или настройка SSH туннеля)
- ⚠️ Нужно настроить веб-сервер для обработки PHP

---

## Способ 3: Git Hook на сервере

Использует git hook для автоматического деплоя при push.

### Установка на сервере:

```bash
ssh root@178.209.127.17

# Скачайте и запустите скрипт
cd /tmp
wget https://raw.githubusercontent.com/yarrobong/kp/main/deploy-server-hook.sh
chmod +x deploy-server-hook.sh
sudo bash deploy-server-hook.sh /var/www/html
```

### Настройка в GitHub:

1. Добавьте remote для bare репозитория:
```bash
cd /var/www/html
git remote add deploy /var/repos/kp.git
```

2. При push используйте:
```bash
git push deploy main
```

### Преимущества:
- ✅ Мгновенный деплой
- ✅ Полный контроль

### Недостатки:
- ⚠️ Нужно настраивать bare репозиторий
- ⚠️ Более сложная настройка

---

## Рекомендация

**Используйте Способ 1 (Cron Job)** - самый простой и надежный:

```bash
ssh root@178.209.127.17
cd /tmp
wget https://raw.githubusercontent.com/yarrobong/kp/main/deploy-cron.sh
chmod +x deploy-cron.sh
sudo bash deploy-cron.sh /var/www/html root
```

После этого код будет автоматически обновляться каждые 5 минут!

---

## Проверка работы

### Для Cron:
```bash
# Проверить cron задачу
crontab -l

# Посмотреть логи
tail -f /var/log/deploy-kp.log

# Запустить вручную
/usr/local/bin/deploy-kp.sh
```

### Для Webhook:
1. Сделайте тестовый коммит и push
2. Проверьте логи: `tail -f storage/logs/webhook.log`
3. Проверьте webhook в GitHub: Settings -> Webhooks -> Recent Deliveries

---

## Отключение GitHub Actions

Если хотите отключить GitHub Actions:

1. Удалите или переименуйте файл: `.github/workflows/deploy.yml`
2. Или отключите Actions в настройках репозитория

