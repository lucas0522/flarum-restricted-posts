# Restricted Posts for Flarum

[![Latest Stable Version](https://poser.pugx.org/zhihe/restricted-posts/v/stable)](https://packagist.org/packages/zhihe/restricted-posts)
[![Total Downloads](https://poser.pugx.org/zhihe/restricted-posts/downloads)](https://packagist.org/packages/zhihe/restricted-posts)
[![License](https://poser.pugx.org/zhihe/restricted-posts/license)](https://packagist.org/packages/zhihe/restricted-posts)

A Flarum extension that allows post authors to mark their posts as restricted content with visual indicators and integrated access control.

## ✨ Features

- 🔒 **Post Marking**: Post authors can mark their own posts as restricted
- 🎯 **Visual Indicators**: Lock icon badges appear on restricted posts
- ⚙️ **Post Controls**: Easy mark/unmark via post dropdown menu
- 📝 **Composer Integration**: Checkbox to mark posts as restricted during creation
- 🌐 **Multi-language**: English and Chinese (Simplified) support
- 🔗 **Extension Integration**: Works with money systems and access control

## 📋 Requirements

- Flarum 1.8.0+
- PHP 8.1+

## 🚀 Installation

```bash
composer require hertz-dev/restricted-posts
php flarum extension:enable hertz-dev-restricted-posts
php flarum migrate
```

## Usage

### For Post Authors
- Use the "Restricted" checkbox when creating posts
- Mark existing posts via the post dropdown menu (⋮)
- Only post authors can mark/unmark their own posts

### Visual Design
- 🔒 Orange lock badge appears on the right side of post headers
- Works alongside zhihe-primary-posts bookmark badges
- Consistent styling with Flarum's design system

## 🔗 Integration

This extension is designed to work seamlessly with:
- **zhihe-money-system**: Provides the `is_restricted` flag for money-based content filtering
- **zhihe-primary-posts**: Compatible badge positioning
- **Access control systems**: Integrates with permission-based restrictions

## 🛠️ Development

### Local Development Setup

```bash
git clone https://github.com/echolocked/zhihe-restricted-posts.git
cd zhihe-restricted-posts
composer install
cd js && npm install && npm run build
```

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📄 License

MIT License

## 🆘 Support

- **Issues**: [GitHub Issues](https://github.com/echolocked/zhihe-restricted-posts/issues)
- **Community**: [Flarum Community Forum](https://discuss.flarum.org)

---

*Love this extension? Consider starring the repository and sharing it with the Flarum community! ⭐*