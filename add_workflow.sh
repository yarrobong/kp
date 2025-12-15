#!/bin/bash

# Скрипт для добавления workflow файла через GitHub API
# Требуется токен с правами workflow

REPO="yarrobong/kp"
TOKEN="${GITHUB_TOKEN:-}"  # Используйте переменную окружения или передайте как параметр
WORKFLOW_FILE=".github/workflows/deploy.yml"

if [ -z "$TOKEN" ]; then
    echo "Использование: GITHUB_TOKEN=your_token ./add_workflow.sh"
    echo "Или передайте токен как параметр: ./add_workflow.sh your_token"
    if [ -n "$1" ]; then
        TOKEN="$1"
    else
        exit 1
    fi
fi

# Читаем содержимое workflow файла
CONTENT=$(base64 -i "$WORKFLOW_FILE" | tr -d '\n')

# Создаем файл через GitHub API
curl -X PUT \
  -H "Authorization: token $TOKEN" \
  -H "Accept: application/vnd.github.v3+json" \
  "https://api.github.com/repos/$REPO/contents/$WORKFLOW_FILE" \
  -d "{
    \"message\": \"Add deployment workflow\",
    \"content\": \"$CONTENT\",
    \"branch\": \"main\"
  }"

echo ""
echo "Если получили ошибку о правах, обновите токен с правами 'workflow'"
echo "Или добавьте файл вручную через веб-интерфейс GitHub"

