<?php

namespace App\Models;

use App\Domain\Filter\FilterBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Source
 *
 * @property int $id
 * @property string $name
 * @property string $api_key
 * @property string|null $description
 * @property string|null $service_class
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Article> $articles
 * @property-read int|null $articles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Author> $authors
 * @property-read int|null $authors_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Category> $categories
 * @property-read int|null $categories_count
 * @method static Builder|Source filterBy(array $filters)
 * @method static Builder|Source newModelQuery()
 * @method static Builder|Source newQuery()
 * @method static Builder|Source query()
 * @method static Builder|Source whereApiKey($value)
 * @method static Builder|Source whereCreatedAt($value)
 * @method static Builder|Source whereDescription($value)
 * @method static Builder|Source whereId($value)
 * @method static Builder|Source whereName($value)
 * @method static Builder|Source whereServiceClass($value)
 * @method static Builder|Source whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Source extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'api_key', 'description', 'service_class'
    ];

    /**
     * @return BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'source_categories',
            'source_id', 'category_id'
        );
    }

    /**
     * @return HasMany
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'source_id');
    }

    /**
     * @return HasMany
     */
    public function authors(): HasMany
    {
        return $this->hasMany(Author::class, 'source_id');
    }

    /**
     * @param Builder<Source> $query
     * @param array<string, string> $filters
     * @return Builder<Source>
     */
    public function scopeFilterBy(Builder $query, array $filters): Builder
    {
        $filter = new FilterBuilder($query, $filters, 'App\Models\Filters\Source');

        return $filter->apply();
    }
}
