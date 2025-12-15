# ✅ Cron установлен! Проверяем работу

## Cron задача успешно установлена

Видно, что Cron задача добавлена:
```
*/5 * * * * /usr/local/bin/deploy-kp.sh >> /var/log/deploy-kp.log 2>&1
```

## Проверка работы

Выполните на сервере для немедленной проверки:

```bash
ssh root@178.209.127.17

# 1. Запустите деплой вручную (немедленно)
/usr/local/bin/deploy-kp.sh

# 2. Проверьте логи
tail -20 /var/log/deploy-kp.log

# 3. Проверьте, появился ли тестовый файл
cd /var/www/html
ls -la test_deploy_check.txt

# 4. Проверьте последний коммит
git log --oneline -3
```

## Ожидаемый результат

После выполнения `/usr/local/bin/deploy-kp.sh` вы должны увидеть:

✅ **Если есть новые изменения:**
```
2025-12-15 XX:XX:XX: New changes detected. Deploying...
2025-12-15 XX:XX:XX: Deployment completed!
```

✅ **Если изменений нет:**
```
2025-12-15 XX:XX:XX: No changes detected.
```

## Полная проверка одной командой

```bash
ssh root@178.209.127.17 << 'EOF'
echo "=== Запуск деплоя ==="
/usr/local/bin/deploy-kp.sh

echo ""
echo "=== Логи ==="
tail -10 /var/log/deploy-kp.log

echo ""
echo "=== Проверка файла ==="
cd /var/www/html
ls -la test_deploy_check.txt 2>/dev/null && echo "✅ Файл найден!" || echo "❌ Файл не найден"

echo ""
echo "=== Последний коммит ==="
git log --oneline -3
EOF
```

## Автоматическая работа

Теперь код будет автоматически обновляться каждые 5 минут при каждом push в GitHub!

Проверьте логи через несколько минут:
```bash
tail -f /var/log/deploy-kp.log
```

