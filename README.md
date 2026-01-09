=== AI SEO Pro ===
Contributors: joyroy
Tags: seo, ai, claude, meta tags, schema, optimization
Requires at least: 5.8
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

# AI SEO Pro

Professional AI-powered SEO plugin for WordPress with advanced meta generation, content analysis, schema markup, XML sitemaps, redirects, and robots.txt editor.

![WordPress](https://img.shields.io/badge/WordPress-5.8%2B-blue)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-purple)
![License](https://img.shields.io/badge/License-GPLv2-green)
![Version](https://img.shields.io/badge/Version-1.0.0-orange)

## Features

### 🤖 AI-Powered Meta Generation
- Generate SEO-optimized meta titles and descriptions using AI
- Support for **Anthropic Claude** and **Google Gemini**
- One-click generation with smart analysis
- Bulk generation support

### 📊 Content Analysis
| Feature | Checks |
|---------|--------|
| SEO Analysis | 17 comprehensive checks |
| Readability | 8 checks |
| Focus Keyword | Density, placement, optimization |
| Structure | Headings, links, images |

- Yoast-style tabbed interface with color-coded scores
- Real-time analysis as you type

### 🗺️ XML Sitemap
- Automatic sitemap generation at `/sitemap_index.xml`
- Separate sitemaps for posts, pages, and categories
- Exclude specific posts/pages from sitemap
- Auto-redirect `/sitemap.xml` to `/sitemap_index.xml`
- Ping search engines on content updates

### 🔄 Redirect Manager
- Create 301 (permanent) and 302 (temporary) redirects
- 404 error monitoring and logging
- One-click redirect from 404 logs
- CSV export of redirects and 404 logs
- Regex support for advanced redirects

### 🤖 Robots.txt Editor
- Virtual robots.txt generation (no file needed)
- **One-click block AI crawlers:**
  - GPTBot, ChatGPT-User, Google-Extended, ClaudeBot, Claude-Web
  - Bytespider, Omgilibot, FacebookBot, Diffbot, Applebot-Extended
  - PerplexityBot, cohere-ai, CCBot
- **One-click block aggressive SEO bots:**
  - AhrefsBot, SemrushBot, MJ12bot, DotBot, BLEXBot
  - SearchmetricsBot, PetalBot, MegaIndex, and more
- Custom rules editor with syntax reference
- Live preview with copy to clipboard

### 📋 Schema Markup Generator

**13 Schema Types Supported:**

| Schema Type | Key Fields |
|-------------|------------|
| Local Business | Name, Address, Hours, Geo, Price Range |
| Organization | Name, Logo, Founders, Contact |
| Person | Name, Job Title, Works For, Social |
| Website | Name, Search Action, Language |
| Article | Headline, Author, Publisher, Dates |
| Product | Name, Price, Availability, Rating |
| FAQ Page | Question/Answer pairs |
| How To | Steps, Tools, Supplies, Time |
| Event | Date, Venue, Tickets, Performer |
| Recipe | Ingredients, Instructions, Nutrition |
| Video | Title, Duration, Thumbnail |
| Breadcrumb | Navigation path |
| Software App | Category, OS, Rating, Download |

**Display Rules:**
- All Pages (default)
- Homepage Only
- Specific Post Types
- Only Specific Pages/Posts (include)
- All Except Specific Pages/Posts (exclude)

### 🔗 Site Connections
Webmaster tools verification for:
- Google Search Console
- Bing Webmaster Tools
- Baidu Webmaster
- Yandex Webmaster
- Pinterest
- Ahrefs

Smart paste detection - extracts verification codes from full meta tags.

### 📱 Social Media
- Open Graph meta tags (Facebook, LinkedIn)
- Twitter Card meta tags
- Custom social images per post
- Preview how posts appear when shared

### 🎯 Additional Features
- Search engine preview (Google SERP simulator)
- Custom post type support
- Advanced options per post (noindex, nofollow, canonical)
- Clean, modern admin interface
- WordPress coding standards compliant

## Installation

1. Upload the plugin files to `/wp-content/plugins/ai-seo-pro/`
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Go to **AI SEO Pro → Settings**
4. Configure your AI API key
5. Start optimizing your content!

## Configuration

### AI Provider Setup

1. Navigate to **AI SEO Pro → Settings**
2. Select your AI provider (Anthropic or Google)
3. Enter your API key
4. Save settings

### Sitemap Configuration

1. Go to **AI SEO Pro → Settings → Sitemap**
2. Enable/disable sitemap generation
3. Configure included post types
4. Set posts per sitemap page

### Schema Setup

1. Go to **AI SEO Pro → Schema**
2. Click **"Add Schema"**
3. Select schema type from dropdown
4. Fill in the dynamic fields
5. Configure display rules
6. Save

## Requirements

- WordPress 5.8 or higher
- PHP 7.4 or higher
- API key from Anthropic or Google (for AI features)

## Screenshots

| Feature | Description |
|---------|-------------|
| Meta Box | AI-powered meta generation with preview |
| SEO Analysis | 17 checks with color-coded scores |
| Schema Generator | Repeater interface with display rules |
| Robots.txt Editor | Live preview with copy function |
| Redirect Manager | 301/302 redirects with 404 monitor |

## Frequently Asked Questions

**Do I need an API key?**
Yes, for AI features. Other features (sitemap, redirects, schema) work without an API key.

**Which AI provider should I choose?**
Both work excellently. Claude excels at natural language, Gemini is Google's powerful offering.

**Does it work with custom post types?**
Yes! Enable AI SEO Pro for any public post type.

**Will it conflict with other SEO plugins?**
We recommend deactivating other SEO plugins to avoid conflicts.

**Can I show different schemas on different pages?**
Yes! Each schema has display rules for targeting specific pages, post types, or exclusions.

## Changelog

### 1.0.0
- Initial release
- AI-powered meta generation (Claude, Gemini)
- Content analysis (17 SEO + 8 readability checks)
- XML Sitemap with exclude option
- Redirect Manager with 404 monitoring
- Robots.txt Editor with AI/bot blocking
- Schema Generator (13 types) with display rules
- Site Connections (6 webmaster tools)
- Open Graph & Twitter Cards
- Search engine preview

## License

This plugin is licensed under the GPLv2 or later.

## Credits

Developed by Joy Roy

---

**AI SEO Pro** - Your complete AI-powered SEO solution for WordPress.