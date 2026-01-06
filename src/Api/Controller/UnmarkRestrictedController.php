<?php

namespace Hertz\RestrictedPosts\Api\Controller;

use Flarum\Api\Controller\AbstractDeleteController;
use Flarum\Http\RequestUtil;
use Flarum\Post\Post;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Flarum\User\Exception\PermissionDeniedException; // 引入权限异常
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
            
            // 【修正】: 同样改为检查权限
            if ($actor->cannot('markRestricted', $post)) {
                throw new PermissionDeniedException();
            }

            if (!$post->is_restricted) {
                return;
            }

            $post->is_restricted = false;
            $post->save();

            event(new RestrictedPostUnmarked($post));
        });
    }
}