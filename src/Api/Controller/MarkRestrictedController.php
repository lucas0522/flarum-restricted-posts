<?php

namespace Hertz\RestrictedPosts\Api\Controller;

use Flarum\Api\Controller\AbstractCreateController;
use Flarum\Http\RequestUtil;
use Flarum\Post\Post;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use Flarum\User\Exception\PermissionDeniedException; // 引入权限异常
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
            
            // 【修正】: 使用 can() 方法检查权限，而不是硬编码对比 ID
            // 这样我们在 Policy 里写的逻辑（版主可操作）才会生效
            if ($actor->cannot('markRestricted', $post)) {
                throw new PermissionDeniedException();
            }

            // 如果已经是受限状态，直接返回
            if ($post->is_restricted) {
                return $post;
            }

            $post->is_restricted = true;
            $post->save();

            event(new RestrictedPostMarked($post));

            return $post;
        });
    }
}