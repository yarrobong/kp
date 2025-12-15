# Устранение проблем с деплоем

## Проверка статуса workflow

1. Откройте https://github.com/yarrobong/kp/actions
2. Найдите последний запуск workflow
3. Нажмите на него, чтобы увидеть детали

## Частые ошибки и решения

### Ошибка 1: "yaml: invalid syntax" или синтаксическая ошибка

**Проблема:** В файле `.github/workflows/deploy.yml` есть лишняя строка `yaml` в начале.

**Решение:**
1. Откройте https://github.com/yarrobong/kp/blob/main/.github/workflows/deploy.yml
2. Нажмите кнопку редактирования (карандаш)
3. Убедитесь, что файл начинается с `name: Deploy to Server` (без строки `yaml` в начале)
4. Сохраните изменения

### Ошибка 2: "Secret not found" или "secrets.SERVER_HOST is not defined"

**Проблема:** Секреты не настроены или имеют неправильные имена.

**Решение:**
1. Проверьте, что все секреты добавлены:
   - https://github.com/yarrobong/kp/settings/secrets/actions
2. Убедитесь, что имена секретов ТОЧНО такие:
   - `SERVER_HOST` (не `SERVER-HOST`)
   - `SERVER_PASSWORD` (не `SERVER-PASSWORD`)
   - `DEPLOY_PATH` (не `DEPLOY-PATH`)

### Ошибка 3: "Permission denied" при SSH подключении

**Проблема:** Неправильный пароль или проблемы с SSH.

**Решение:**
1. Проверьте пароль в секрете `SERVER_PASSWORD`
2. Убедитесь, что сервер доступен: `ping 178.209.127.17`
3. Попробуйте подключиться вручную: `ssh root@178.209.127.17`

### Ошибка 4: "cd: /var/www/html: No such file or directory"

**Проблема:** Путь в секрете `DEPLOY_PATH` неправильный или директория не существует.

**Решение:**
1. Подключитесь к серверу: `ssh root@178.209.127.17`
2. Найдите правильный путь к проекту: `find / -name "kp" -type d 2>/dev/null`
3. Обновите секрет `DEPLOY_PATH` с правильным путем

### Ошибка 5: "git: command not found" на сервере

**Проблема:** Git не установлен на сервере.

**Решение:**
```bash
ssh root@178.209.127.17
# Для CentOS/RHEL:
yum install git -y
# Для Ubuntu/Debian:
apt-get update && apt-get install git -y
```

### Ошибка 6: "composer: command not found"

**Проблема:** Composer не установлен на сервере.

**Решение:**
```bash
ssh root@178.209.127.17
# Установите Composer или загрузите папку vendor вручную
```

## Как проверить логи workflow

1. Откройте https://github.com/yarrobong/kp/actions
2. Найдите нужный запуск (обычно самый верхний)
3. Нажмите на него
4. Разверните шаг, который завершился с ошибкой
5. Посмотрите логи выполнения

## Исправление workflow файла вручную

Если токен не имеет прав для обновления workflow:

1. Откройте https://github.com/yarrobong/kp/blob/main/.github/workflows/deploy.yml
2. Нажмите кнопку редактирования (карандаш)
3. Исправьте файл (убедитесь, что нет строки `yaml` в начале)
4. Сохраните изменения

**Правильное начало файла:**
```yaml
name: Deploy to Server

on:
  push:
    branches:
      - main
```

## Тестирование подключения к серверу

Перед настройкой деплоя проверьте подключение:

```bash
# Проверка доступности сервера
ping 178.209.127.17

# Проверка SSH подключения
ssh root@178.209.127.17

# Проверка пути на сервере
ssh root@178.209.127.17 "ls -la /var/www/html"
```

## Проверка секретов

Убедитесь, что все секреты настроены правильно:
- https://github.com/yarrobong/kp/settings/secrets/actions

Должны быть:
- ✅ SERVER_HOST
- ✅ SERVER_PASSWORD  
- ✅ DEPLOY_PATH

