<?php

namespace Hertz\RestrictedPosts\Access;

use Flarum\Post\Post;
use Flarum\User\Access\AbstractPolicy;
use Flarum\User\User;

class PostPolicy extends AbstractPolicy
{
    public function viewRestrictedContent(User $actor, Post $post)
    {
        // 1. 如果没有任何限制类型，允许查看
        if (empty($post->restriction_type)) {
            return $this->allow();
        }

        // 2. 作者本人永远允许查看
        if ($actor->id === $post->user_id) {
            return $this->allow();
        }

        // 3. 管理员永远允许查看
        if ($actor->isAdmin()) {
            return $this->allow();
        }

        // 4. 【新逻辑】根据类型分流
        switch ($post->restriction_type) {
            case 'login':
                // 如果是 'login' 类型：只要不是游客 (isGuest 为 false) 就允许
                if (!$actor->isGuest()) {
                    return $this->allow();
                }
                break;

            case 'group':
                // 如果是 'group' 类型：检查有没有之前的 VIP 权限
                if ($actor->hasPermission('discussion.viewRestrictedContent')) {
                    return $this->allow();
                }
                break;
        }

        // 5. 都不满足，拒绝
        return $this->deny();
    }

    public function markRestricted(User $actor, Post $post)
    {
        if ($actor->can('discussion.markRestrictedPosts')) {
            return $this->allow();
        }
        return $this->deny();
    }
}