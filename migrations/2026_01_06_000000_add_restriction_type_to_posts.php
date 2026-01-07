<?php

use Flarum\Database\Migration;

return Migration::addColumns('posts', [
    // 添加一个字符串列，允许为空
    // null = 公开
    // 'login' = 仅登录用户可见
    // 'group' = 仅特定权限组可见 (原V1功能)
    'restriction_type' => ['string', 'length' => 20, 'nullable' => true],
]);