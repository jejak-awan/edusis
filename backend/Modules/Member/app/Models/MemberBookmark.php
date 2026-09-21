<?php

declare(strict_types=1);

namespace Modules\Member\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Publishing\Models\Content;

/**
 * @property string $id
 * @property string $member_id
 * @property string $content_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Member|null $member
 * @property-read Content|null $content
 */
class MemberBookmark extends Model
{
    use HasUuids;

    protected $table = 'mem_bookmarks';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'member_id',
        'content_id',
    ];

    /**
     * @return BelongsTo<Member, $this>
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * @return BelongsTo<Content, $this>
     */
    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class);
    }
}
