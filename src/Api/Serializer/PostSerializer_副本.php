<?php

namespace Hertz\RestrictedPosts\Api\Serializer;

use Flarum\Post\Post;

class PostSerializer
{
    public function __invoke($serializer, $model, $attributes): array
    {
        if (!($model instanceof Post)) {
            return $attributes;
        }

        $attributes['isRestricted'] = (bool) $model->is_restricted;
        $attributes['canMarkRestricted'] = $serializer->getActor()->can('markRestricted', $model);

        if ($model->is_restricted && !$serializer->getActor()->can('viewRestrictedContent', $model)) {
            $translator = resolve('translator');
            $message = $translator->trans('hertz-dev-restricted-posts.forum.restricted_content_hidden');
            
            $attributes['content'] = $message;
            // 保持使用 RestrictedWarning 这个类名
            $attributes['contentHtml'] = '<div class="RestrictedWarning"><i class="fas fa-lock"></i> <span>' . $message . '</span></div>';
        }

        return $attributes;
    }
}