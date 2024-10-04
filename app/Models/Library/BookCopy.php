<?php

namespace App\Models\Library;

use App\Concerns\HasFilter;
use App\Concerns\HasMeta;
use App\Concerns\HasUuid;
use App\Models\Option;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class BookCopy extends Model
{
    use HasFactory, HasFilter, HasMeta, HasUuid, LogsActivity;

    protected $guarded = [];

    protected $primaryKey = 'id';

    protected $table = 'book_copies';

    protected $casts = [
        'meta' => 'array',
    ];

    public function addition(): BelongsTo
    {
        return $this->belongsTo(BookAddition::class, 'book_addition_id');
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

    public function condition(): BelongsTo
    {
        return $this->belongsTo(Option::class, 'condition_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('book_copy')
            ->logAll()
            ->logExcept(['updated_at'])
            ->logOnlyDirty();
    }
}
