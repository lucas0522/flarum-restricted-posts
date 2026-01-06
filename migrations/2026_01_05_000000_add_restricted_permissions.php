<?php

use Flarum\Database\Migration;
use Flarum\Group\Group;

// 直接使用 Migration 类的静态方法，它会自动生成 up 和 down 逻辑
return Migration::addPermissions([
    // 权限1：允许查看受限内容（默认给普通会员）
    'discussion.viewRestrictedContent' => Group::MEMBER_ID, 
    
    // 权限2：允许标记帖子为受限（默认给版主）
    'discussion.markRestrictedPosts' => Group::MODERATOR_ID, 
]);