<?php

namespace App\Models;

use App\Domain\Filter\FilterBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Article
 *
 * @property int $id
 * @property int|null $source_id
 * @property int|null $author_id
 * @property string $title
 * @property string $web_url
 * @property string|null $image_url
 * @property string|null $description
 * @property string|null $content
 * @property string $published_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Author|null $author
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Category> $categories
 * @property-read int|null $categories_count
 * @property-read \App\Models\Source|null $source
 * @method static Builder|Article filterBy(array $filters)
 * @method static Builder|Article newModelQuery()
 * @method static Builder|Article newQuery()
 * @method static Builder|Article query()
 * @method static Builder|Article whereAuthorId($value)
 * @method static Builder|Article whereContent($value)
 * @method static Builder|Article whereCreatedAt($value)
 * @method static Builder|Article whereDescription($value)
 * @method static Builder|Article whereId($value)
 * @method static Builder|Article whereImageUrl($value)
 * @method static Builder|Article wherePublishedAt($value)
 * @method static Builder|Article whereSourceId($value)
 * @method static Builder|Article whereTitle($value)
 * @method static Builder|Article whereUpdatedAt($value)
 * @method static Builder|Article whereWebUrl($value)
 * @mixin \Eloquent
 */
class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_id', 'author_id', 'title', 'web_url', 'image_url', 'description', 'content', 'published_at'
    ];

    /**
     * @return BelongsTo
     */
    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class, 'source_id');
    }

    /**
     * Article Author
     *
     * @return BelongsTo
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class, 'author_id');
    }

    /**
     * Article Categories
     *
     * @return BelongsToMany
     */
    public function categories(): BelongsToMany {
        return $this->belongsToMany(Category::class, 'article_categories',
            'article_id', 'category_id'
        );
    }

    /**
     * @param Builder<Article> $query
     * @param array<string, string> $filters
     * @return Builder<Article>
     */
    public function scopeFilterBy(Builder $query, array $filters): Builder
    {
        $filter = new FilterBuilder($query, $filters, 'App\Models\Filters\Article');

        return $filter->apply();
    }
}
