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

        $attributes['restrictionType'] = $model->restriction_type;
        $attributes['canMarkRestricted'] = $serializer->getActor()->can('markRestricted', $model);

        // 如果被限制且无权查看
        if (!empty($model->restriction_type) && !$serializer->getActor()->can('viewRestrictedContent', $model)) {
            $translator = resolve('translator');
            
            // 默认设置 (VIP/Group) -> 橙色
            $messageKey = 'hertz-dev-restricted-posts.forum.restricted_content_hidden';
            $cssClass = 'RestrictedWarning'; 
            $icon = 'fas fa-crown'; // VIP 用皇冠图标 (或者保持 fa-lock)

            // 特殊设置 (Login) -> 蓝色
            if ($model->restriction_type === 'login') {
                // 暂时复用同一个翻译，或者你可以去 zh.yml 加一个新的
                $cssClass = 'RestrictedWarning RestrictedWarning--login';
                $icon = 'fas fa-user-lock'; // 登录限制用“用户锁”图标
            } else {
                // 默认情况（VIP）
                $icon = 'fas fa-lock';
            }

            $message = $translator->trans($messageKey);

            $attributes['content'] = $message;
            // 输出带有特定 CSS 类的 HTML
            $attributes['contentHtml'] = '<div class="' . $cssClass . '"><i class="' . $icon . '"></i> <span>' . $message . '</span></div>';
        }

        return $attributes;
    }
}