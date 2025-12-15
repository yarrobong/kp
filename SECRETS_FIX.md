# ⚠️ ВАЖНО: Секреты не настроены!

## Проблема

Ошибка в логах: `ssh-keyscan -H  >> ~/.ssh/known_hosts` - после `-H` нет значения.

Это означает, что секрет **SERVER_HOST** не установлен в GitHub!

## Решение: Добавьте секреты СЕЙЧАС

### Шаг 1: Откройте настройки секретов

Перейдите: **https://github.com/yarrobong/kp/settings/secrets/actions**

### Шаг 2: Добавьте три секрета

Нажмите **"New repository secret"** и добавьте каждый секрет:

#### 1. SERVER_HOST
- **Name:** `SERVER_HOST` (копируйте точно!)
- **Secret:** `178.209.127.17`
- Нажмите **"Add secret"**

#### 2. SERVER_PASSWORD  
- **Name:** `SERVER_PASSWORD` (копируйте точно!)
- **Secret:** `vJq*s-YNG5XQt7`
- Нажмите **"Add secret"**

#### 3. DEPLOY_PATH
- **Name:** `DEPLOY_PATH` (копируйте точно!)
- **Secret:** `/var/www/html`
- Нажмите **"Add secret"**

### Шаг 3: Проверьте список секретов

После добавления вы должны увидеть в списке:
- ✅ SERVER_HOST
- ✅ SERVER_PASSWORD
- ✅ DEPLOY_PATH

### Шаг 4: Обновите workflow файл

Я исправил workflow файл локально. Обновите его на GitHub:

1. Откройте: https://github.com/yarrobong/kp/edit/main/.github/workflows/deploy.yml
2. Замените весь файл на содержимое из файла `.github/workflows/deploy.yml` в проекте
3. Или скопируйте улучшенную версию ниже

### Шаг 5: Тестовый запуск

После добавления секретов и обновления workflow:

```bash
git add .
git commit -m "Test deployment after secrets setup"
git push origin main
```

Затем проверьте: https://github.com/yarrobong/kp/actions

## Проверка

После добавления секретов workflow должен:
1. ✅ Успешно найти секрет SERVER_HOST
2. ✅ Подключиться к серверу
3. ✅ Выполнить деплой

Если все еще есть ошибки, проверьте логи в GitHub Actions.

