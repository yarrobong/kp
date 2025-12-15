# ER-диаграмма базы данных

## Сущности и связи

```
┌─────────────┐
│    users    │
├─────────────┤
│ id (PK)     │
│ name        │
│ email       │
│ password    │
│ role        │
│ created_at  │
│ updated_at  │
└─────────────┘
      │
      │ 1:N
      │
      ├─────────────────┐
      │                 │
      ▼                 ▼
┌─────────────┐  ┌──────────────┐
│  proposals  │  │  templates   │
├─────────────┤  ├──────────────┤
│ id (PK)     │  │ id (PK)      │
│ user_id(FK) │  │ user_id(FK)  │
│ template_id │  │ title        │
│ title       │  │ body_html    │
│ body_html   │  │ is_system    │
│ status      │  │ is_published │
│ ...         │  │ ...           │
└─────────────┘  └──────────────┘
      │
      │ 1:N
      │
      ├─────────────────┐
      │                 │
      ▼                 ▼
┌──────────────┐  ┌──────────────┐
│proposal_items│  │proposal_files│
├──────────────┤  ├──────────────┤
│ id (PK)      │  │ id (PK)      │
│ proposal_id │  │ proposal_id  │
│ name         │  │ user_id (FK)  │
│ quantity     │  │ type         │
│ price        │  │ path         │
│ ...          │  │ ...          │
└──────────────┘  └──────────────┘
```

## Описание таблиц

### users
Хранит информацию о пользователях системы.
- **role**: guest, user, admin

### templates
Шаблоны коммерческих предложений.
- **is_system**: системные шаблоны (только для админа)
- **is_published**: опубликованные шаблоны доступны всем

### proposals
Коммерческие предложения.
- **status**: draft, published
- **soft delete**: поддержка восстановления

### proposal_items
Позиции товаров/услуг в КП.
- Связана с proposals через proposal_id

### proposal_files
Загруженные файлы (логотипы, изображения).
- **type**: logo, image

## Индексы

- users.email (UNIQUE)
- proposals.user_id
- proposals.template_id
- proposal_items.proposal_id
- proposal_files.proposal_id
- proposal_files.user_id



