<?php

use Flarum\Database\Migration;
use Flarum\Group\Group;

return [
    'up' => function (Migration $migration) {
        $migration->addPermissions([
            // 权限1：允许查看受限内容（默认给普通会员）
            'discussion.viewRestrictedContent' => Group::MEMBER_ID, 
            
            // 权限2：允许标记帖子为受限（默认给版主）
            'discussion.markRestrictedPosts' => Group::MODERATOR_ID, 
        ]);
    },
    'down' => function (Migration $migration) {
        $migration->removePermissions([
            'discussion.viewRestrictedContent',
            'discussion.markRestrictedPosts'
        ]);
    }
];