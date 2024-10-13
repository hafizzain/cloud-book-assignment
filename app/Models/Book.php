<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasPermissions;

class Book extends Model
{
    use HasFactory, HasPermissions;

    protected $fillable = ['title', 'author_id'];

    public function sections()
    {
        return $this->hasMany(Section::class);
    }
    
    public function collaborators()
    {
        return $this->belongsToMany(User::class, 'book_collaborators')->withPivot('permission');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
