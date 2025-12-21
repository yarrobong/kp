<?php

namespace App\Models;

use App\Models\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $fillable = ['user_id', 'name', 'price', 'photo', 'description'];

    public function user()
    {
        // Связь с пользователем
        return User::find($this->user_id);
    }

    public static function getByUserId($userId)
    {
        $db = static::getDB();
        $stmt = $db->prepare("SELECT * FROM products WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getPhotoUrl()
    {
        if ($this->photo) {
            return '/storage/' . $this->photo;
        }
        return '/css/placeholder-product.png'; // Заглушка для фото
    }
}
