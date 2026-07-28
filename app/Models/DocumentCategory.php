<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DocumentCategory extends Model
{
    use BelongsToTeam, HasFactory;

    protected $fillable = ['team_id', 'user_id', 'name', 'description'];

    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(Document::class, 'document_document_category');
    }
}
