<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcessStatus extends Model
{
    use HasFactory;

    protected $table = 'process_status';

    protected $fillable = [
        'user_id',
        'process_name',
        'status',
        'updated_at',
    ];

    public $timestamps = false;

    /**
     * Relacionamento com a tabela Users.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
