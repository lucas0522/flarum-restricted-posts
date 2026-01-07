<?php

namespace Hertz\RestrictedPosts\Access;

use Flarum\Post\Post;
use Flarum\User\Access\AbstractPolicy;
use Flarum\User\User;

class PostPolicy extends AbstractPolicy
{
    /**
     * 判断用户是否有权查看受限帖子的内容
     */
    public function viewRestrictedContent(User $actor, Post $post)
    {
        // 1. 如果帖子没被限制，允许查看（或者交给其他逻辑处理）
        if (!$post->is_restricted) {
            return $this->allow();
        }

        // 2. 如果是管理员，允许查看
        if ($actor->isAdmin()) {
            return $this->allow();
        }

        // 3. 如果是作者本人，允许查看
        if ($actor->id === $post->user_id) {
            return $this->allow();
        }

        // 4. 检查用户是否有 'discussion.viewRestrictedContent' 权限
        if ($actor->hasPermission('discussion.viewRestrictedContent')) {
            return $this->allow();
        }

        // 5. 否则拒绝
        return $this->deny();
    }

    /**
     * 判断用户是否有权将帖子标记为受限
     */
    public function markRestricted(User $actor, Post $post)
    {
        if ($actor->can('discussion.markRestrictedPosts')) {
            return $this->allow();
        }
        
        return $this->deny();
    }
}