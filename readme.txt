=== RankFlow SEO ===
Contributors: wpstrativ, joystrativ, shuvostrativ, sayed24, redwanstrativ 
Tags: seo, ai seo, schema markup, xml sitemap, redirects
Requires at least: 5.8
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Professional AI-powered SEO plugin with advanced meta generation, content analysis, schema markup, XML sitemaps, redirects, and robots.txt editor.

== Description ==

RankFlow SEO is a comprehensive, professional-grade SEO plugin for WordPress that combines AI-powered optimization with essential SEO tools. Whether you're a beginner or an SEO expert, RankFlow SEO provides everything you need to improve your search engine rankings.

= AI-Powered Meta Generation =

Generate SEO-optimized meta titles and descriptions using cutting-edge AI technology:

* Support for **Anthropic Claude** and **Google Gemini**
* One-click generation with smart content analysis
* Bulk generation support for multiple posts
* Natural, engaging meta descriptions that drive clicks

= Content Analysis =

Comprehensive analysis to ensure your content is fully optimized:

* **17 SEO checks** covering all ranking factors
* **8 readability checks** for better user engagement
* **Focus keyword analysis** including density and placement
* **Structure analysis** for headings, links, and images
* Yoast-style tabbed interface with color-coded scores
* Real-time analysis as you type

= XML Sitemap =

Automatic sitemap generation to help search engines discover your content:

* Generates sitemap at `/sitemap_index.xml`
* Separate sitemaps for posts, pages, and categories
* Exclude specific posts or pages from sitemap
* Auto-redirect `/sitemap.xml` to `/sitemap_index.xml`
* Ping search engines automatically on content updates

= Redirect Manager =

Powerful redirect management with 404 monitoring:

* Create 301 (permanent) and 302 (temporary) redirects
* Monitor and log 404 errors automatically
* One-click redirect creation from 404 logs
* CSV export for redirects and 404 logs
* Regex support for advanced redirect patterns

= Robots.txt Editor =

Full control over search engine crawling:

* Virtual robots.txt generation (no file needed)
* **One-click block AI crawlers:** GPTBot, ChatGPT-User, Google-Extended, ClaudeBot, Claude-Web, Bytespider, Omgilibot, FacebookBot, Diffbot, Applebot-Extended, PerplexityBot, cohere-ai, CCBot
* **One-click block aggressive SEO bots:** AhrefsBot, SemrushBot, MJ12bot, DotBot, BLEXBot, SearchmetricsBot, PetalBot, MegaIndex, and more
* Custom rules editor with syntax reference
* Live preview with copy to clipboard

= Schema Markup Generator =

Add structured data to improve rich snippets in search results:

**14 Schema Types Supported:**

* **Local Business** - Name, address, hours, geo coordinates, price range
* **Organization** - Name, logo, founders, contact information
* **Person** - Name, job title, works for, social profiles
* **Website** - Name, search action, language
* **Article** - Headline, author, publisher, dates
* **Product** - Name, price, availability, rating
* **Service** - Name, description, provider, area served, price, offer catalog
* **FAQ Page** - Question and answer pairs
* **How To** - Steps, tools, supplies, time required
* **Event** - Date, venue, tickets, performer
* **Recipe** - Ingredients, instructions, nutrition
* **Video** - Title, duration, thumbnail
* **Breadcrumb** - Navigation path
* **Software Application** - Category, OS, rating, download URL

**Flexible Display Rules:**

* All pages (default)
* Homepage only
* Specific post types
* Include only specific pages/posts
* Exclude specific pages/posts

= Site Connections =

Easy webmaster tools verification:

* Google Search Console
* Bing Webmaster Tools
* Baidu Webmaster
* Yandex Webmaster
* Pinterest
* Ahrefs Analytics

Smart paste detection automatically extracts verification codes from full meta tags.

= Social Media Integration =

Optimize how your content appears when shared:

* Open Graph meta tags for Facebook and LinkedIn
* Twitter Card meta tags
* Custom social images per post
* Preview how posts appear when shared

= Additional Features =

* Search engine preview (Google SERP simulator)
* Custom post type support
* Advanced options per post (noindex, nofollow, canonical)
* Google Tag Manager integration
* Clean, modern admin interface
* WordPress coding standards compliant

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/rankflow-seo/` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Go to **RankFlow SEO → Settings** to configure the plugin.
4. (Optional) Enter your AI API key for AI-powered features.
5. Start optimizing your content!

= AI Provider Setup =

1. Navigate to **RankFlow SEO → Settings → AI API**
2. Select your AI provider (Anthropic Claude or Google Gemini)
3. Enter your API key
4. Save settings

= Sitemap Configuration =

1. Go to **RankFlow SEO → XML Sitemap**
2. Enable or disable sitemap generation
3. Configure included post types
4. Set posts per sitemap page
5. View your sitemap at yoursite.com/sitemap_index.xml

= Schema Setup =

1. Go to **RankFlow SEO → Schema**
2. Click "Add Schema"
3. Select schema type from dropdown
4. Fill in the required fields
5. Configure display rules
6. Save your schema

== Frequently Asked Questions ==

= Do I need an API key to use this plugin? =

An API key is only required for AI-powered meta generation features. All other features including XML sitemaps, redirects, schema markup, and robots.txt editor work without an API key.

= Which AI provider should I choose? =

Both providers work excellently. Anthropic Claude excels at natural, engaging language, while Google Gemini is Google's powerful AI offering. Choose based on your preference or existing subscriptions.

= Does RankFlow SEO work with custom post types? =

Yes! You can enable RankFlow SEO for any public custom post type in the settings. The plugin will add meta boxes and include them in sitemaps.

= Will this plugin conflict with other SEO plugins? =

We recommend deactivating other SEO plugins (like Yoast SEO or Rank Math) before using RankFlow SEO to avoid conflicts and duplicate meta tags.

= Can I show different schemas on different pages? =

Yes! Each schema you create has flexible display rules. You can target specific pages, post types, or exclude certain content from showing a particular schema.

= How do I verify my site with Google Search Console? =

1. Go to **RankFlow SEO → Settings → Site Connections**
2. Paste your Google verification code or the full meta tag
3. Save settings
4. Verify in Google Search Console

= Is the sitemap automatically updated? =

Yes! The sitemap is dynamically generated and always reflects your current content. Search engines are automatically pinged when you publish or update content.

= Can I block AI crawlers from scraping my content? =

Yes! Go to **RankFlow SEO → Robots.txt** and use the one-click buttons to block AI crawlers like GPTBot, ChatGPT-User, ClaudeBot, and many others.

= Does the plugin support multilingual sites? =

The plugin is translation-ready and works with multilingual plugins. All strings use proper internationalization functions.

= Where can I get support? =

For support questions, please use the WordPress.org support forum for this plugin.

== Screenshots ==

1. Dashboard - Overview of your site's SEO status
2. Meta Box - AI-powered meta generation with live preview
3. SEO Analysis - Comprehensive checks with color-coded scores
4. Schema Generator - Easy-to-use interface with 14 schema types
5. Redirect Manager - Create and manage redirects with 404 monitoring
6. Robots.txt Editor - Live preview with one-click bot blocking
7. XML Sitemap - Automatic sitemap generation settings
8. Site Connections - Webmaster tools verification

== Changelog ==

= 1.0.0 =
* Initial release
* AI-powered meta generation supporting Anthropic Claude and Google Gemini
* Content analysis with 17 SEO checks and 8 readability checks
* XML Sitemap generation with exclude options
* Redirect Manager with 301/302 support and 404 monitoring
* Robots.txt Editor with AI crawler and SEO bot blocking
* Schema Generator with 14 schema types and flexible display rules
* Site Connections for 6 webmaster tools
* Open Graph and Twitter Card meta tags
* Google Tag Manager integration
* Search engine preview (SERP simulator)

== Upgrade Notice ==

= 1.0.0 =
Initial release of RankFlow SEO. Install to start optimizing your WordPress site with AI-powered SEO tools.

== Privacy Policy ==

RankFlow SEO respects your privacy:

* The plugin does not collect any personal data from your visitors.
* If you use AI features, your content is sent to your chosen AI provider (Anthropic or Google) for processing. Please review their privacy policies.
* Webmaster verification codes are stored locally in your WordPress database.
* 404 logs store only URLs and timestamps, no visitor personal information.

== Credits ==

Developed by Strativ AB.

== Additional Notes ==

= Minimum Requirements =

* WordPress 5.8 or higher
* PHP 7.4 or higher
* MySQL 5.6 or higher

= Recommended =

* PHP 8.0 or higher for best performance
* HTTPS enabled site
* Modern browser for admin interface

For more information and documentation, visit the plugin settings page after installation.