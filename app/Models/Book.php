<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'publication_year',
        'status',
    ];

    protected $casts = [
        'publication_year' => 'integer',
    ];


    public function authors()
    {
        return $this->belongsToMany(Author::class);
    }


    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }


    public function scopeSearchByTitle($query, $title)
    {
        return $query->where('title', 'like', "%{$title}%");
    }


    public function scopeSearchByAuthor($query, $authorName)
    {
        return $query->whereHas('authors', function ($q) use ($authorName) {
            $q->where('name', 'like', "%{$authorName}%");
        });
    }
}
