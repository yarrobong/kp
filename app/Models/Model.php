<?php

namespace App\Models;

use App\Database\Database;
use App\Database\QueryBuilder;
use PDO;

abstract class Model
{
    protected $table;
    protected $fillable = [];
    protected $hidden = [];
    protected $attributes = [];
    protected $casts = [];
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    public function __get($key)
    {
        if (isset($this->attributes[$key])) {
            return $this->castAttribute($key, $this->attributes[$key]);
        }
        return null;
    }

    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    protected function castAttribute($key, $value)
    {
        if (isset($this->casts[$key])) {
            $cast = $this->casts[$key];
            if ($cast === 'date' || $cast === 'datetime') {
                return $value ? new \DateTime($value) : null;
            }
            if ($cast === 'array' || $cast === 'json') {
                return json_decode($value, true);
            }
            if ($cast === 'boolean') {
                return (bool)$value;
            }
            if (str_starts_with($cast, 'decimal:')) {
                $precision = (int)substr($cast, 8);
                return round((float)$value, $precision);
            }
        }
        return $value;
    }

    public function getTable()
    {
        return $this->table ?? strtolower(class_basename($this)) . 's';
    }

    public static function query()
    {
        return new QueryBuilder(static::class);
    }

    public static function where($column, $operator = null, $value = null)
    {
        return static::query()->where($column, $operator, $value);
    }

    public static function all()
    {
        return static::query()->get();
    }

    public static function count()
    {
        return static::query()->count();
    }

    public static function find($id)
    {
        $model = new static();
        $db = Database::connection();
        $stmt = $db->prepare("SELECT * FROM {$model->getTable()} WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        if ($data) {
            return new static($data);
        }
        return null;
    }

    public static function findOrFail($id)
    {
        $model = static::find($id);
        if (!$model) {
            abort(404);
        }
        return $model;
    }

    public static function create(array $data)
    {
        $model = new static();
        $fillable = array_intersect_key($data, array_flip($model->fillable));
        $fillable['created_at'] = date('Y-m-d H:i:s');
        $fillable['updated_at'] = date('Y-m-d H:i:s');

        $db = Database::connection();
        $columns = implode(', ', array_keys($fillable));
        $placeholders = ':' . implode(', :', array_keys($fillable));
        
        $sql = "INSERT INTO {$model->getTable()} ({$columns}) VALUES ({$placeholders})";
        $stmt = $db->prepare($sql);
        $stmt->execute($fillable);

        $fillable['id'] = $db->lastInsertId();
        return new static($fillable);
    }

    public function update(array $data)
    {
        $fillable = array_intersect_key($data, array_flip($this->fillable));
        $fillable['updated_at'] = date('Y-m-d H:i:s');

        $db = Database::connection();
        $set = [];
        foreach ($fillable as $key => $value) {
            $set[] = "{$key} = :{$key}";
        }
        $set = implode(', ', $set);

        $sql = "UPDATE {$this->getTable()} SET {$set} WHERE id = :id";
        $stmt = $db->prepare($sql);
        $fillable['id'] = $this->attributes['id'];
        $stmt->execute($fillable);

        $this->attributes = array_merge($this->attributes, $fillable);
        return $this;
    }

    public function delete()
    {
        $db = Database::connection();
        $stmt = $db->prepare("DELETE FROM {$this->getTable()} WHERE id = ?");
        return $stmt->execute([$this->attributes['id']]);
    }

    public function toArray()
    {
        return $this->attributes;
    }
}

function class_basename($class)
{
    $class = is_object($class) ? get_class($class) : $class;
    return basename(str_replace('\\', '/', $class));
}

