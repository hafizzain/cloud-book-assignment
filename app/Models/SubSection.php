<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubSection extends Model
{
    use HasFactory;

    protected $fillable = ['title','content','section_id', 'parent_subsection_id', 'book_id'];

    // A subsection can belong to another subsection (parent subsection)
    public function parentSubsection()
    {
        return $this->belongsTo(Subsection::class, 'parent_subsection_id');
    }

    // A subsection can have many child subsections
    public function childSubsections()
    {
        return $this->hasMany(Subsection::class, 'parent_subsection_id')->with('childSubsections'); // Recursive
    }
}
