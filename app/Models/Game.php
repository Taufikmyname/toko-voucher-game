<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Game extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    // Otomatis membuat slug dari nama game saat menyimpan
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($game) {
            $game->slug = Str::slug($game->name);
        });
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}