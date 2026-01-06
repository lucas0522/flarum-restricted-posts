import app from 'flarum/admin/app';

app.initializers.add('hertz-dev-restricted-posts', () => {
  app.extensionData
    .for('hertz-dev-restricted-posts')
    .registerPermission(
      {
        icon: 'fas fa-lock',
        label: app.translator.trans('hertz-dev-restricted-posts.admin.permissions.view_restricted_content'),
        permission: 'discussion.viewRestrictedContent',
      },
      'view' // 放在“查看”权限组
    )
    .registerPermission(
      {
        icon: 'fas fa-edit',
        label: app.translator.trans('hertz-dev-restricted-posts.admin.permissions.mark_restricted_posts'),
        permission: 'discussion.markRestrictedPosts',
      },
      'start' // 放在“发布/回复”权限组
    );
});