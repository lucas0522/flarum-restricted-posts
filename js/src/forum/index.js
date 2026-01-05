import { extend } from 'flarum/common/extend';
import app from 'flarum/forum/app';
import Post from 'flarum/forum/components/Post';
import CommentPost from 'flarum/forum/components/CommentPost';
import Button from 'flarum/common/components/Button';
import DiscussionComposer from 'flarum/forum/components/DiscussionComposer';
import ReplyComposer from 'flarum/forum/components/ReplyComposer';
import PostControls from 'flarum/forum/utils/PostControls';

app.initializers.add('hertz-dev-restricted-posts', () => {
  // 1. [保持不变] 给帖子头部添加“锁”图标
  extend(CommentPost.prototype, 'headerItems', function (items) {
    const post = this.attrs.post;
    
    if (post && post.attribute('isRestricted')) {
      items.add('restrictedBadge',
        m('span', {
          className: 'RestrictedBadge',
          title: app.translator.trans('hertz-dev-restricted-posts.forum.restricted_post')
        }, 
          m('i', { className: 'fas fa-lock' })
        ),
        70
      );
    }
  });

  // 2. [修改] 帖子下拉菜单的操作按钮
  extend(PostControls, 'userControls', function (items, post, context) {
    // 关键修改：不再检查 user.id === post.user.id
    // 而是检查我们在 PHP PostSerializer 中传过来的 'canMarkRestricted' 属性
    if (!post.attribute('canMarkRestricted')) return;

    if (post.attribute('isRestricted')) {
      items.add('unmarkRestricted',
        Button.component({
          icon: 'fas fa-unlock',
          onclick: () => context.unmarkAsRestricted()
        }, app.translator.trans('hertz-dev-restricted-posts.forum.unmark_restricted')),
        90
      );
    } else {
      items.add('markRestricted',
        Button.component({
          icon: 'fas fa-lock',
          onclick: () => context.markAsRestricted()
        }, app.translator.trans('hertz-dev-restricted-posts.forum.mark_restricted')),
        90
      );
    }
  });

  // 3. [保持不变] 给 Post 原型添加 API 请求方法
  extend(Post.prototype, 'oninit', function () {
    this.markAsRestricted = () => {
      const post = this.attrs.post;
      app.request({
        method: 'POST',
        url: app.forum.attribute('apiUrl') + '/posts/' + post.id() + '/mark-restricted'
      }).then(() => {
        post.pushAttributes({ isRestricted: true });
        m.redraw();
      });
    };

    this.unmarkAsRestricted = () => {
      const post = this.attrs.post;
      app.request({
        method: 'DELETE',
        url: app.forum.attribute('apiUrl') + '/posts/' + post.id() + '/unmark-restricted'
      }).then(() => {
        post.pushAttributes({ isRestricted: false });
        m.redraw();
      });
    };
  });

  // 4. [修改] 发布主题时的勾选框 (Composer)
  extend(DiscussionComposer.prototype, 'headerItems', function (items) {
    // 关键修改：检查全局权限 'canMarkRestrictedPosts' (来自 extend.php)
    if (!app.forum.attribute('canMarkRestrictedPosts')) return;

    items.add('isRestricted',
      m('div', { className: 'Form-group' }, [
        m('label', { className: 'checkbox' }, [
          m('input', {
            type: 'checkbox',
            checked: this.composer.fields.isRestricted || false,
            onchange: (e) => {
              this.composer.fields.isRestricted = e.target.checked;
            }
          }),
          ' ',
          app.translator.trans('hertz-dev-restricted-posts.forum.restricted_checkbox')
        ])
      ]),
      9
    );
  });

  // 5. [修改] 回复时的勾选框 (Reply Composer)
  extend(ReplyComposer.prototype, 'headerItems', function (items) {
    // 同样检查权限
    if (!app.forum.attribute('canMarkRestrictedPosts')) return;

    items.add('isRestricted',
      m('div', { className: 'Form-group' }, [
        m('label', { className: 'checkbox' }, [
          m('input', {
            type: 'checkbox',
            checked: this.composer.fields.isRestricted || false,
            onchange: (e) => {
              this.composer.fields.isRestricted = e.target.checked;
            }
          }),
          ' ',
          app.translator.trans('hertz-dev-restricted-posts.forum.restricted_checkbox')
        ])
      ]),
      9
    );
  });

  // 6. [保持不变] 传输数据逻辑
  extend(DiscussionComposer.prototype, 'data', function (data) {
    data.isRestricted = this.composer.fields.isRestricted || false;
  });
  
  extend(ReplyComposer.prototype, 'data', function (data) {
    data.isRestricted = this.composer.fields.isRestricted || false;
  });
});