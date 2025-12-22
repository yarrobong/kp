#!/bin/bash

# Триггер для быстрой синхронизации
# Использование: ./sync_trigger.sh [commit_message]

COMMIT_MESSAGE=${1:-"Быстрая синхронизация $(date +'%H:%M:%S')"}

echo "⚡ Быстрая синхронизация..."
./auto_sync.sh "$COMMIT_MESSAGE"
echo "✅ Готово!"
