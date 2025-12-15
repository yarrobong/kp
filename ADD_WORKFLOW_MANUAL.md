# Как добавить workflow файл вручную

Токен GitHub не имеет прав для создания workflow файлов. Добавьте файл вручную:

## Способ 1: Через веб-интерфейс GitHub

1. Перейдите на https://github.com/yarrobong/kp
2. Нажмите кнопку **Add file** → **Create new file**
3. Введите путь: `.github/workflows/deploy.yml`
4. Скопируйте содержимое из файла `.github/workflows/deploy.yml` в локальном проекте
5. Нажмите **Commit new file**

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

