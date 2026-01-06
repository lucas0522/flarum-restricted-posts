const config = require('flarum-webpack-config');
const path = require('path');

// 获取基础配置
const conf = config();

// 强制手动指定入口文件，不再依赖自动扫描
conf.entry = {
    forum: path.resolve(__dirname, 'src/forum/index.js'),
    admin: path.resolve(__dirname, 'src/admin/index.js')
};

module.exports = conf;