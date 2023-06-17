<?php

namespace App\Models;

use App\Domain\Filter\FilterBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\Category
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Article> $articles
 * @property-read int|null $articles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Source> $sources
 * @property-read int|null $sources_count
 * @method static Builder|Category filterBy(array $filters)
 * @method static Builder|Category newModelQuery()
 * @method static Builder|Category newQuery()
 * @method static Builder|Category query()
 * @method static Builder|Category whereCreatedAt($value)
 * @method static Builder|Category whereDescription($value)
 * @method static Builder|Category whereId($value)
 * @method static Builder|Category whereName($value)
 * @method static Builder|Category whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description'
    ];

    /**
     * @return BelongsToMany
     */
    public function sources(): BelongsToMany
    {
        return $this->belongsToMany(Source::class, 'source_categories',
            'category_id', 'source_id'
        );
    }

    /**
     * Category Articles
     *
     * @return BelongsToMany
     */
    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'article_categories',
            'category_id', 'article_id'
        );
    }

    /**
     * @param Builder<Category> $query
     * @param array<string, string> $filters
     * @return Builder<Category>
     */
    public function scopeFilterBy(Builder $query, array $filters): Builder
    {
        $filter = new FilterBuilder($query, $filters, 'App\Models\Filters\Category');

        return $filter->apply();
    }
}
