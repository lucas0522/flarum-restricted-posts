<?php

namespace Hertz\RestrictedPosts\Event;

use Flarum\Post\Post;

class RestrictedPostMarked
{
    public $post;
    
    public function __construct(Post $post)
    {
        $this->post = $post;
    }
}