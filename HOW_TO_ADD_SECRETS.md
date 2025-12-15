# Как добавить секреты в GitHub - Пошаговая инструкция

## Шаг 1: Откройте репозиторий

Перейдите на главную страницу репозитория:
**https://github.com/yarrobong/kp**

## Шаг 2: Откройте Settings (Настройки)

На странице репозитория найдите вкладку **Settings** (Настройки). Она находится в верхней части страницы, в горизонтальном меню рядом с:
- Code
- Issues  
- Pull requests
- Actions
- Projects
- **Settings** ← вот эта вкладка
- Security
- Insights

**Или перейдите напрямую по ссылке:**
**https://github.com/yarrobong/kp/settings**

## Шаг 3: Найдите раздел Secrets and variables

В левом боковом меню найдите раздел **Secrets and variables** и нажмите на него.

Под ним откроется подменю:
- **Actions** ← нажмите сюда

**Или перейдите напрямую:**
**https://github.com/yarrobong/kp/settings/secrets/actions**

## Шаг 4: Добавьте секреты

На странице "Secrets and variables" → "Actions" вы увидите кнопку:
**"New repository secret"** (Новый секрет репозитория)

⚠️ **ВАЖНО:** Имена секретов должны соответствовать требованиям GitHub:
- Только буквы (a-z, A-Z), цифры (0-9) и подчеркивания (_)
- Без пробелов и дефисов
- Должно начинаться с буквы или подчеркивания
- Используйте ТОЧНО такие имена, как указано ниже (копируйте их!)

Нажмите на кнопку и добавьте каждый секрет по очереди:

### Секрет 1: SERVER_HOST

1. Нажмите **"New repository secret"**
2. В поле **Name** введите ТОЧНО (скопируйте): `SERVER_HOST`
   - ✅ Правильно: `SERVER_HOST`
   - ❌ Неправильно: `SERVER-HOST`, `SERVER HOST`, `server-host`
3. В поле **Secret** введите: `178.209.127.17`
4. Нажмите **"Add secret"**

### Секрет 2: SERVER_PASSWORD

1. Снова нажмите **"New repository secret"**
2. В поле **Name** введите ТОЧНО (скопируйте): `SERVER_PASSWORD`
   - ✅ Правильно: `SERVER_PASSWORD`
   - ❌ Неправильно: `SERVER-PASSWORD`, `SERVER PASSWORD`, `server-password`
3. В поле **Secret** введите: `vJq*s-YNG5XQt7`
4. Нажмите **"Add secret"**

### Секрет 3: DEPLOY_PATH

1. Снова нажмите **"New repository secret"**
2. В поле **Name** введите ТОЧНО (скопируйте): `DEPLOY_PATH`
   - ✅ Правильно: `DEPLOY_PATH`
   - ❌ Неправильно: `DEPLOY-PATH`, `DEPLOY PATH`, `deploy-path`
3. В поле **Secret** введите: `/var/www/html`
   *(Или путь, куда вы установите проект на сервере)*
4. Нажмите **"Add secret"**

## Готово! ✅

После добавления всех трёх секретов вы увидите их в списке:
- SERVER_HOST
- SERVER_PASSWORD  
- DEPLOY_PATH

**Важно:** После добавления секретов вы не сможете увидеть их значения (они скрыты для безопасности). Вы сможете только удалить и создать заново.

## Быстрые ссылки

- Главная страница репозитория: https://github.com/yarrobong/kp
- Настройки репозитория: https://github.com/yarrobong/kp/settings
- Секреты Actions: https://github.com/yarrobong/kp/settings/secrets/actions

## Альтернативный путь (если не видите Settings)

Если вы не видите вкладку **Settings**, возможно:
1. Вы не авторизованы - войдите в GitHub
2. У вас нет прав администратора репозитория - попросите владельца добавить вас как collaborator с правами admin

