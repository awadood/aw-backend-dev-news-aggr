<?php

declare(strict_types=1);

namespace App\Transformers;

use App\Models\Article;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

/**
 * Class ArticleTransformer
 *
 * This transformer transforms the model Article.
 */
class ArticleTransformer extends TransformerAbstract
{
    /**
     * transforms the article.
     */
    public function transform(Article $article): array
    {
        $data = [
            'title' => $article->title,
            'url' => $article->url,
            'description' => $article->description,
            'last_updated' => (new Carbon($article->updated_at))->toDateTimeString(),
            'attributes' => [],
        ];

        foreach ($article->attributes as $attribute) {
            $data['attributes'][] = [
                'name' => $attribute->name,
                'value' => $attribute->value,
            ];
        }

        return $data;
    }
}
