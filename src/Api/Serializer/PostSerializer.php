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

        // 1. 输出标记状态
        $attributes['isRestricted'] = (bool) $model->is_restricted;

        // 2. [新增] 告诉前端：当前用户是否有权“开关”这个限制
        // 这将决定前端是否显示那个“锁”按钮
        $attributes['canMarkRestricted'] = $serializer->getActor()->can('markRestricted', $model);

        // 3. 内容隐藏逻辑 (之前写过的)
        if ($model->is_restricted && !$serializer->getActor()->can('viewRestrictedContent', $model)) {
            $attributes['content'] = '<p>[' . $serializer->getTranslator()->trans('hertz-dev-restricted-posts.forum.restricted_content_hidden') . ']</p>';
            $attributes['contentHtml'] = '<div class="restricted-content-placeholder"><p>[' . $serializer->getTranslator()->trans('hertz-dev-restricted-posts.forum.restricted_content_hidden') . ']</p></div>';
        }

        return $attributes;
    }
}