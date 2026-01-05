<?php

namespace Hertz\RestrictedPosts\Api\Controller;

use Flarum\Api\Controller\AbstractDeleteController;
use Flarum\Http\RequestUtil;
use Flarum\Post\Post;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Illuminate\Validation\ValidationException;
use Hertz\RestrictedPosts\Event\RestrictedPostUnmarked;

class UnmarkRestrictedController extends AbstractDeleteController
{
    protected $db;

    public function __construct(ConnectionInterface $db)
    {
        $this->db = $db;
    }

    protected function delete(ServerRequestInterface $request)
    {
        $actor = RequestUtil::getActor($request);
        $postId = Arr::get($request->getQueryParams(), 'id');

        return $this->db->transaction(function () use ($actor, $postId) {
            $post = Post::findOrFail($postId);
            
            // Only post author can unmark their own posts
            if ($actor->id !== $post->user_id) {
                throw new ValidationException(['error' => 'Only post author can unmark posts']);
            }

            // Skip if not marked as restricted
            if (!$post->is_restricted) {
                return;
            }

            $post->is_restricted = false;
            $post->save();

            // Emit event for extensibility
            event(new RestrictedPostUnmarked($post));
        });
    }
}