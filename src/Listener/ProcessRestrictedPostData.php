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

        if (isset($data['attributes']['isRestricted']) && !$discussion->exists) {
            $discussion->afterSave(function ($discussion) use ($data, $actor) {
                $firstPost = $discussion->posts()->first();
                
                // 修改点：使用 can('markRestricted') 检查权限
                if ($firstPost && $actor->can('markRestricted', $firstPost)) {
                    $isRestricted = (bool) Arr::get($data['attributes'], 'isRestricted', false);
                    $firstPost->is_restricted = $isRestricted;
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

        if (isset($data['attributes']['isRestricted']) && !$post->exists) {
            // 修改点：使用 can('markRestricted') 检查权限
            if ($actor->can('markRestricted', $post)) {
                $isRestricted = (bool) Arr::get($data['attributes'], 'isRestricted', false);
                $post->is_restricted = $isRestricted;
            }
        }
    }
}