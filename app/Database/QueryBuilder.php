<?php

namespace App\Database;

use App\Models\Model;

class QueryBuilder
{
    protected $model;
    protected $table;
    protected $wheres = [];
    protected $orderBy = [];
    protected $limit = null;
    protected $offset = null;
    protected $with = [];

    public function __construct($model)
    {
        $this->model = $model;
        $instance = new $model();
        $this->table = $instance->getTable();
    }

    public function where($column, $operator = null, $value = null)
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }
        $this->wheres[] = compact('column', 'operator', 'value');
        return $this;
    }

    public function orWhere($column, $operator = null, $value = null)
    {
        // Упрощенная реализация - в реальности нужна более сложная логика
        return $this->where($column, $operator, $value);
    }

    public function whereIn($column, $values)
    {
        $this->wheres[] = ['type' => 'in', 'column' => $column, 'values' => $values];
        return $this;
    }

    public function orderBy($column, $direction = 'ASC')
    {
        $this->orderBy[] = compact('column', 'direction');
        return $this;
    }

    public function limit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset($offset)
    {
        $this->offset = $offset;
        return $this;
    }

    public function with($relations)
    {
        $this->with = is_array($relations) ? $relations : [$relations];
        return $this;
    }

    public function get()
    {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];

        if (!empty($this->wheres)) {
            $whereClauses = [];
            foreach ($this->wheres as $where) {
                if (isset($where['type']) && $where['type'] === 'in') {
                    $placeholders = implode(',', array_fill(0, count($where['values']), '?'));
                    $whereClauses[] = "{$where['column']} IN ({$placeholders})";
                    $params = array_merge($params, $where['values']);
                } else {
                    $whereClauses[] = "{$where['column']} {$where['operator']} ?";
                    $params[] = $where['value'];
                }
            }
            $sql .= " WHERE " . implode(' AND ', $whereClauses);
        }

        if (!empty($this->orderBy)) {
            $orders = [];
            foreach ($this->orderBy as $order) {
                $orders[] = "{$order['column']} {$order['direction']}";
            }
            $sql .= " ORDER BY " . implode(', ', $orders);
        }

        if ($this->limit) {
            $sql .= " LIMIT {$this->limit}";
            if ($this->offset) {
                $sql .= " OFFSET {$this->offset}";
            }
        }

        $db = Database::connection();
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll();

        $models = [];
        foreach ($results as $data) {
            $model = new $this->model($data);
            $models[] = $model;
        }

        return collect($models);
    }

    public function first()
    {
        $this->limit(1);
        $results = $this->get();
        return $results->first();
    }

    public function paginate($perPage = 15)
    {
        $page = $_GET['page'] ?? 1;
        $this->offset(($page - 1) * $perPage);
        $this->limit($perPage);
        
        $total = $this->count();
        $items = $this->get();

        return new Paginator($items, $total, $perPage, $page);
    }

    public function count()
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        $params = [];

        if (!empty($this->wheres)) {
            $whereClauses = [];
            foreach ($this->wheres as $where) {
                if (isset($where['type']) && $where['type'] === 'in') {
                    $placeholders = implode(',', array_fill(0, count($where['values']), '?'));
                    $whereClauses[] = "{$where['column']} IN ({$placeholders})";
                    $params = array_merge($params, $where['values']);
                } else {
                    $whereClauses[] = "{$where['column']} {$where['operator']} ?";
                    $params[] = $where['value'];
                }
            }
            $sql .= " WHERE " . implode(' AND ', $whereClauses);
        }

        $db = Database::connection();
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return (int)$result['count'];
    }
}

class Paginator
{
    protected $items;
    protected $total;
    protected $perPage;
    protected $currentPage;

    public function __construct($items, $total, $perPage, $currentPage)
    {
        $this->items = $items;
        $this->total = $total;
        $this->perPage = $perPage;
        $this->currentPage = $currentPage;
    }

    public function links()
    {
        $totalPages = ceil($this->total / $this->perPage);
        if ($totalPages <= 1) {
            return '';
        }

        $html = '<div class="pagination">';
        for ($i = 1; $i <= $totalPages; $i++) {
            $active = $i == $this->currentPage ? 'active' : '';
            $html .= "<a href=\"?page={$i}\" class=\"{$active}\">{$i}</a> ";
        }
        $html .= '</div>';
        return $html;
    }
}

function collect($items)
{
    return new Collection($items);
}

class Collection
{
    protected $items;

    public function __construct($items)
    {
        $this->items = $items;
    }

    public function first()
    {
        return $this->items[0] ?? null;
    }

    public function all()
    {
        return $this->items;
    }

    public function count()
    {
        return count($this->items);
    }
}

