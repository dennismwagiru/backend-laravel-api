<?php

namespace App\Models;

use App\Domain\Filter\FilterBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Author
 *
 * @property int $id
 * @property int|null $source_id
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Article> $articles
 * @property-read int|null $articles_count
 * @property-read \App\Models\Source|null $source
 * @method static Builder|Author filterBy(array $filters)
 * @method static Builder|Author newModelQuery()
 * @method static Builder|Author newQuery()
 * @method static Builder|Author query()
 * @method static Builder|Author whereCreatedAt($value)
 * @method static Builder|Author whereDescription($value)
 * @method static Builder|Author whereId($value)
 * @method static Builder|Author whereName($value)
 * @method static Builder|Author whereSourceId($value)
 * @method static Builder|Author whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Author extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_id', 'name', 'description'
    ];

    /**
     * @return BelongsTo
     */
    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class, 'source_id');
    }

    /**
     * @return HasMany
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'author_id');
    }

    /**
     * @param Builder<Author> $query
     * @param array<string, string> $filters
     * @return Builder<Author>
     */
    public function scopeFilterBy(Builder $query, array $filters): Builder
    {
        $filter = new FilterBuilder($query, $filters, 'App\Models\Filters\Author');

        return $filter->apply();
    }
}
