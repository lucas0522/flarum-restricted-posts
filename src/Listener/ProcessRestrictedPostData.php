<?php

namespace Hertz\RestrictedPosts\Listener;

use Flarum\Discussion\Event\Saving as DiscussionSaving;
use Flarum\Post\Event\Saving as PostSaving;
use Illuminate\Support\Arr;

class ProcessRestrictedPostData
{
    public function handleDiscussion(DiscussionSaving $event)
    {
        $discussion = $event->discussion;
        $data = $event->data;
        $actor = $event->actor;

        if (isset($data['attributes']['restrictionType']) && !$discussion->exists) {
            $discussion->afterSave(function ($discussion) use ($data, $actor) {
                $firstPost = $discussion->posts()->first();
                
                if ($firstPost && $actor->can('markRestricted', $firstPost)) {
                    // 获取限制类型：null, 'login', 或 'group'
                    $type = Arr::get($data['attributes'], 'restrictionType');
                    
                    $firstPost->restriction_type = $type;
                    // 为了兼容旧逻辑，如果有类型，就把 is_restricted 设为 true
                    $firstPost->is_restricted = !empty($type);
                    $firstPost->save();
                }
            });
        }
    }

    public function handlePost(PostSaving $event)
    {
        $post = $event->post;
        $data = $event->data;
        $actor = $event->actor;

        // 监听前端发来的 'restrictionType'
        if (isset($data['attributes']['restrictionType'])) {
            if ($actor->can('markRestricted', $post)) {
                $type = Arr::get($data['attributes'], 'restrictionType');
                
                $post->restriction_type = $type;
                $post->is_restricted = !empty($type);
            }
        }
    }
}