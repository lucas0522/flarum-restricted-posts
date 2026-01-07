import { extend } from 'flarum/common/extend';
import app from 'flarum/forum/app';
import Post from 'flarum/forum/components/Post';
import CommentPost from 'flarum/forum/components/CommentPost';
import Button from 'flarum/common/components/Button';
import Select from 'flarum/common/components/Select'; // 引入 Select 组件
import DiscussionComposer from 'flarum/forum/components/DiscussionComposer';
import ReplyComposer from 'flarum/forum/components/ReplyComposer';
import PostControls from 'flarum/forum/utils/PostControls';

app.initializers.add('hertz-dev-restricted-posts', () => {
  
  // 1. [保持不变] 帖子右上角的锁图标
  extend(CommentPost.prototype, 'headerItems', function (items) {
    const post = this.attrs.post;
    // 只要有 restrictionType 或者 isRestricted 为真，就显示图标
    if (post && (post.attribute('restrictionType') || post.attribute('isRestricted'))) {
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

  // 2. [临时保持不变] 帖子下拉菜单的操作按钮 (针对已有帖子)
  // 目前这个按钮点击后默认设为 "group" (VIP) 类型，后续我们可以把它改成弹窗选择
  extend(PostControls, 'userControls', function (items, post, context) {
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

  extend(Post.prototype, 'oninit', function () {
    this.markAsRestricted = () => {
      const post = this.attrs.post;
      // 默认按钮行为：标记为 VIP (group)
      app.request({
        method: 'POST',
        url: app.forum.attribute('apiUrl') + '/posts/' + post.id() + '/mark-restricted',
        body: { restrictionType: 'group' } // 默认设为 VIP
      }).then(() => {
        post.pushAttributes({ isRestricted: true, restrictionType: 'group' });
        m.redraw();
      });
    };

    this.unmarkAsRestricted = () => {
      const post = this.attrs.post;
      app.request({
        method: 'DELETE',
        url: app.forum.attribute('apiUrl') + '/posts/' + post.id() + '/unmark-restricted'
      }).then(() => {
        post.pushAttributes({ isRestricted: false, restrictionType: null });
        m.redraw();
      });
    };
  });

  // 3. [关键修改] 定义下拉菜单的选项
  const getRestrictionOptions = () => ({
    '': app.translator.trans('hertz-dev-restricted-posts.forum.options.public'), // 默认空值 = 公开
    'login': app.translator.trans('hertz-dev-restricted-posts.forum.options.login'),
    'group': app.translator.trans('hertz-dev-restricted-posts.forum.options.group')
  });

  // 4. [关键修改] 发布主题时的下拉菜单 (Composer)
  extend(DiscussionComposer.prototype, 'headerItems', function (items) {
    if (!app.forum.attribute('canMarkRestrictedPosts')) return;

    items.add('restrictionType',
      m('div', { className: 'Form-group RestrictionSelect' }, [
        Select.component({
          options: getRestrictionOptions(),
          value: this.composer.fields.restrictionType || '', // 绑定值
          onchange: (value) => {
            this.composer.fields.restrictionType = value;
          }
        })
      ]),
      9
    );
  });

  // 5. [关键修改] 回复时的下拉菜单 (Reply Composer)
  extend(ReplyComposer.prototype, 'headerItems', function (items) {
    if (!app.forum.attribute('canMarkRestrictedPosts')) return;

    items.add('restrictionType',
      m('div', { className: 'Form-group RestrictionSelect' }, [
        Select.component({
          options: getRestrictionOptions(),
          value: this.composer.fields.restrictionType || '',
          onchange: (value) => {
            this.composer.fields.restrictionType = value;
          }
        })
      ]),
      9
    );
  });

  // 6. [关键修改] 数据传输：发送 restrictionType 给后端
  extend(DiscussionComposer.prototype, 'data', function (data) {
    data.restrictionType = this.composer.fields.restrictionType || null;
  });
  
  extend(ReplyComposer.prototype, 'data', function (data) {
    data.restrictionType = this.composer.fields.restrictionType || null;
  });
});