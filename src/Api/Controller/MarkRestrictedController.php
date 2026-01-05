<?php

namespace Hertz\RestrictedPosts\Api\Controller;

use Flarum\Api\Controller\AbstractCreateController;
use Flarum\Http\RequestUtil;
use Flarum\Post\Post;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use Illuminate\Validation\ValidationException;
use Hertz\RestrictedPosts\Event\RestrictedPostMarked;

class MarkRestrictedController extends AbstractCreateController
{
    public $serializer = \Flarum\Api\Serializer\PostSerializer::class;

    protected $db;

    public function __construct(ConnectionInterface $db)
    {
        $this->db = $db;
    }

    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);
        $postId = Arr::get($request->getQueryParams(), 'id');

        return $this->db->transaction(function () use ($actor, $postId) {
            $post = Post::findOrFail($postId);
            
            // Only post author can mark their own posts as restricted
            if ($actor->id !== $post->user_id) {
                throw new ValidationException(['error' => 'Only post author can mark posts as restricted']);
            }

            // Skip if already marked as restricted
            if ($post->is_restricted) {
                return $post;
            }

            $post->is_restricted = true;
            $post->save();

            // Emit event for extensibility
            event(new RestrictedPostMarked($post));

            return $post;
        });
    }
}