# Metapilot Smart SEO

AI-powered SEO plugin for WordPress — meta generation, content analysis, schema markup, XML sitemaps, redirect manager, and robots.txt editor.

[![WordPress](https://img.shields.io/badge/WordPress-5.8%2B-blue)](https://wordpress.org)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-purple)](https://php.net)
[![License](https://img.shields.io/badge/License-GPLv2%2B-green)](https://www.gnu.org/licenses/gpl-2.0.html)
[![Version](https://img.shields.io/badge/Version-1.0.0-orange)](https://wordpress.org/plugins/metapilot-smart-seo/)

---

## Features

### AI-Powered Meta Generation

- Generate SEO-optimized meta titles and descriptions with one click
- Supports **Anthropic Claude** and **Google Gemini**
- Bulk generation for multiple posts
- Auto-generate on save option

### Content Analysis

- 17 SEO checks covering all major ranking factors
- 8 readability checks for better user engagement
- Focus keyword analysis (density, placement, prominence)
- Real-time analysis as you write
- Color-coded scores with actionable suggestions

### XML Sitemap

- Automatic sitemap at `/sitemap_index.xml`
- Separate sitemaps per post type and taxonomy
- Configurable posts per page
- Auto-ping Google and Bing on content updates
- Include/exclude specific post types, taxonomies, and authors

### Redirect Manager

- 301 (permanent) and 302 (temporary) redirects
- Automatic 404 error logging and monitoring
- One-click redirect creation from 404 logs
- Regex pattern support for advanced rules
- CSV import/export

### Robots.txt Editor

- Virtual robots.txt (no physical file needed)
- One-click block for 13+ AI crawlers (GPTBot, ClaudeBot, ChatGPT-User, etc.)
- One-click block for aggressive SEO bots (AhrefsBot, SemrushBot, etc.)
- Custom rules editor with syntax reference
- Live preview with copy to clipboard

### Schema Markup Generator

14 schema types with flexible display rules:

| Schema Type    | Schema Type          |
| -------------- | -------------------- |
| Local Business | Organization         |
| Person         | Website              |
| Article        | Product              |
| Service        | FAQ Page             |
| How To         | Event                |
| Recipe         | Video                |
| Breadcrumb     | Software Application |

Display rules: all pages, homepage only, specific post types, include/exclude by ID.

### Social Media

- Open Graph meta tags (Facebook, LinkedIn)
- Twitter Card meta tags
- Custom social image per post
- Default fallback OG image

### Site Connections

- Google Search Console
- Bing Webmaster Tools
- Baidu Webmaster
- Yandex Webmaster
- Pinterest
- Ahrefs Analytics

Smart paste — automatically extracts verification codes from full meta tag strings.

---

## Installation

**Via WordPress Admin**

1. Go to **Plugins → Add New**
2. Search for "Metapilot Smart SEO"
3. Click **Install Now** then **Activate**

**Manual Installation**

1. Download the plugin zip
2. Upload to `/wp-content/plugins/metapilot-smart-seo/`
3. Activate via **Plugins** screen

**After Activation**

1. Navigate to **Metapilot Smart SEO → Settings**
2. (Optional) Add your AI API key under **Settings → AI API**
3. Configure XML Sitemap, Schema, and other modules as needed

---

## Requirements

| Requirement | Minimum | Recommended |
| ----------- | ------- | ----------- |
| WordPress   | 5.8     | Latest      |
| PHP         | 7.4     | 8.0+        |
| MySQL       | 5.6     | 8.0+        |

An AI API key (Anthropic or Google) is only needed for the AI meta generation feature. All other features work without one.

---

## External Services

This plugin optionally connects to external services. All connections are opt-in and documented in `readme.txt`.

| Service              | Purpose                          | When                              |
| -------------------- | -------------------------------- | --------------------------------- |
| Anthropic Claude API | AI meta generation               | Only on explicit user action      |
| Google Gemini API    | AI meta generation               | Only on explicit user action      |
| Google Sitemap Ping  | Notify Google of sitemap updates | On publish/update (if enabled)    |
| Bing Sitemap Ping    | Notify Bing of sitemap updates   | On publish/update (if enabled)    |
| Ahrefs Analytics     | Site analytics/verification      | Homepage only (if key configured) |

---

## File Structure

```
metapilot-smart-seo/
├── admin/                          # Admin-facing functionality
│   ├── class-admin.php             # Script/style enqueue, notices
│   ├── class-settings.php          # Settings pages and registration
│   ├── class-metabox.php           # Post editor meta box
│   ├── class-schema-admin.php      # Schema management
│   ├── class-redirect-admin.php    # Redirect and 404 admin
│   ├── class-sitemap-admin.php     # Sitemap settings admin
│   ├── partials/                   # Reusable admin partials
│   └── views/                     # Full admin page views
├── includes/                       # Core plugin classes
│   ├── class-metapilot-smart-seo.php  # Main plugin class and loader
│   ├── class-loader.php
│   ├── class-robots-txt.php
│   ├── api/                        # AI provider integrations
│   ├── redirects/
│   ├── sitemap/
│   └── utils/
├── public/                         # Frontend functionality
│   ├── class-public.php
│   ├── class-schema-output.php
│   └── partials/
├── assets/
│   ├── css/
│   └── js/
├── languages/                      # Translation files (.pot)
├── metapilot-smart-seo.php         # Main plugin file
├── readme.txt                      # WordPress.org readme
└── uninstall.php
```

---

## Coding Standards

The plugin follows [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/):

- All input sanitized with appropriate `sanitize_*()` functions
- All output escaped with `esc_html()`, `esc_attr()`, `esc_url()`, `wp_kses()`
- All forms and AJAX handlers use nonce verification
- All DB queries use `$wpdb->prepare()`
- Proper capability checks on all admin actions

---

## Changelog

### 1.0.0

- Initial release
- AI-powered meta generation supporting Anthropic Claude and Google Gemini
- Content analysis with 17 SEO checks and 8 readability checks
- XML Sitemap generation with post type and taxonomy support
- Redirect Manager with 301/302 support and 404 monitoring
- Robots.txt Editor with AI crawler and SEO bot blocking
- Schema Generator with 14 schema types and flexible display rules
- Site Connections for 6 webmaster tools
- Open Graph and Twitter Card meta tags
- Search engine preview (SERP simulator)

---

## License

[GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html)

---

## Credits

Developed by [Strativ AB](https://strativ.se)

Contributors: wpstrativ, joystrativ, shuvostrativ, sayed24, redwanstrativ, mariumstrativ
