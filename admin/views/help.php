<?php
/**
 * Help page.
 *
 * @package    AI_SEO_Pro
 * @subpackage AI_SEO_Pro/admin/views
 */

if (!defined('ABSPATH')) {
	exit;
}
?>

<div class="wrap ai-seo-pro-help">
	<?php require_once AI_SEO_PRO_PLUGIN_DIR . 'admin/partials/header.php'; ?>

	<div class="help-content">
		<div class="help-main">
			<!-- Getting Started -->
			<div class="help-section">
				<h2><?php esc_html_e('Getting Started', 'ai-seo-pro'); ?></h2>

				<div class="help-card">
					<h3><?php esc_html_e('1. Configure API Settings', 'ai-seo-pro'); ?></h3>
					<p><?php esc_html_e('Navigate to AI SEO Pro → Settings → AI API tab and configure your preferred AI provider:', 'ai-seo-pro'); ?>
					</p>
					<ul>
						<li><?php esc_html_e('Choose your AI provider (Anthropic Claude or Google Gemini)', 'ai-seo-pro'); ?>
						</li>
						<li><?php esc_html_e('Enter your API key', 'ai-seo-pro'); ?></li>
						<li><?php esc_html_e('Optionally enable auto-generation', 'ai-seo-pro'); ?></li>
					</ul>
				</div>

				<div class="help-card">
					<h3><?php esc_html_e('2. Enable Post Types', 'ai-seo-pro'); ?></h3>
					<p><?php esc_html_e('Go to Settings → General and select which post types should have SEO optimization:', 'ai-seo-pro'); ?>
					</p>
					<ul>
						<li><?php esc_html_e('Posts', 'ai-seo-pro'); ?></li>
						<li><?php esc_html_e('Pages', 'ai-seo-pro'); ?></li>
						<li><?php esc_html_e('Custom post types (WooCommerce products, portfolio, etc.)', 'ai-seo-pro'); ?>
						</li>
					</ul>
				</div>

				<div class="help-card">
					<h3><?php esc_html_e('3. Optimize Your Content', 'ai-seo-pro'); ?></h3>
					<p><?php esc_html_e('When editing any post:', 'ai-seo-pro'); ?></p>
					<ol>
						<li><?php esc_html_e('Scroll to the AI SEO Pro meta box', 'ai-seo-pro'); ?></li>
						<li><?php esc_html_e('Enter your focus keyword (optional)', 'ai-seo-pro'); ?></li>
						<li><?php esc_html_e('Click "Generate Now with AI" button', 'ai-seo-pro'); ?></li>
						<li><?php esc_html_e('Review and edit the generated meta tags', 'ai-seo-pro'); ?></li>
						<li><?php esc_html_e('Check your SEO score and recommendations', 'ai-seo-pro'); ?></li>
					</ol>
				</div>

				<div class="help-card">
					<h3><?php esc_html_e('4. Configure XML Sitemap', 'ai-seo-pro'); ?></h3>
					<p><?php esc_html_e('Go to AI SEO Pro → Settings → Sitemap tab:', 'ai-seo-pro'); ?></p>
					<ul>
						<li><?php esc_html_e('Enable sitemap generation', 'ai-seo-pro'); ?></li>
						<li><?php esc_html_e('Select post types to include', 'ai-seo-pro'); ?></li>
						<li><?php esc_html_e('Your sitemap will be available at /sitemap_index.xml', 'ai-seo-pro'); ?>
						</li>
						<li><?php esc_html_e('Submit sitemap URL to Google Search Console', 'ai-seo-pro'); ?></li>
					</ul>
				</div>

				<div class="help-card">
					<h3><?php esc_html_e('5. Set Up Schema Markup', 'ai-seo-pro'); ?></h3>
					<p><?php esc_html_e('Navigate to AI SEO Pro → Schema:', 'ai-seo-pro'); ?></p>
					<ol>
						<li><?php esc_html_e('Click "Add Schema" button', 'ai-seo-pro'); ?></li>
						<li><?php esc_html_e('Select schema type (Local Business, Organization, etc.)', 'ai-seo-pro'); ?>
						</li>
						<li><?php esc_html_e('Fill in the dynamic fields', 'ai-seo-pro'); ?></li>
						<li><?php esc_html_e('Configure display rules (where to show)', 'ai-seo-pro'); ?></li>
						<li><?php esc_html_e('Save and test with Google Rich Results Test', 'ai-seo-pro'); ?></li>
					</ol>
				</div>

				<div class="help-card">
					<h3><?php esc_html_e('6. Verify Webmaster Tools', 'ai-seo-pro'); ?></h3>
					<p><?php esc_html_e('Go to AI SEO Pro → Settings → Site Connections tab:', 'ai-seo-pro'); ?></p>
					<ul>
						<li><?php esc_html_e('Paste your verification meta tag or code', 'ai-seo-pro'); ?></li>
						<li><?php esc_html_e('The plugin automatically extracts the verification code', 'ai-seo-pro'); ?>
						</li>
						<li><?php esc_html_e('Supported: Google, Bing, Baidu, Yandex, Pinterest, Ahrefs', 'ai-seo-pro'); ?>
						</li>
					</ul>
				</div>
			</div>

			<!-- Features Overview -->
			<div class="help-section">
				<h2><?php esc_html_e('Features Overview', 'ai-seo-pro'); ?></h2>

				<div class="features-grid">
					<div class="feature-card">
						<span class="feature-icon">🤖</span>
						<h4><?php esc_html_e('AI Meta Generation', 'ai-seo-pro'); ?></h4>
						<p><?php esc_html_e('Generate SEO-optimized meta titles and descriptions using Anthropic Claude or Google Gemini AI.', 'ai-seo-pro'); ?>
						</p>
					</div>

					<div class="feature-card">
						<span class="feature-icon">📊</span>
						<h4><?php esc_html_e('SEO Analysis', 'ai-seo-pro'); ?></h4>
						<p><?php esc_html_e('17 comprehensive SEO checks with color-coded scores and actionable recommendations.', 'ai-seo-pro'); ?>
						</p>
					</div>

					<div class="feature-card">
						<span class="feature-icon">📖</span>
						<h4><?php esc_html_e('Readability Analysis', 'ai-seo-pro'); ?></h4>
						<p><?php esc_html_e('8 readability checks including sentence length, paragraph structure, and passive voice.', 'ai-seo-pro'); ?>
						</p>
					</div>

					<div class="feature-card">
						<span class="feature-icon">🗺️</span>
						<h4><?php esc_html_e('XML Sitemap', 'ai-seo-pro'); ?></h4>
						<p><?php esc_html_e('Automatic sitemap generation with post, page, and category sitemaps. Auto-ping search engines.', 'ai-seo-pro'); ?>
						</p>
					</div>

					<div class="feature-card">
						<span class="feature-icon">🔄</span>
						<h4><?php esc_html_e('Redirect Manager', 'ai-seo-pro'); ?></h4>
						<p><?php esc_html_e('Create 301/302 redirects, monitor 404 errors, and fix broken links with one click.', 'ai-seo-pro'); ?>
						</p>
					</div>

					<div class="feature-card">
						<span class="feature-icon">🤖</span>
						<h4><?php esc_html_e('Robots.txt Editor', 'ai-seo-pro'); ?></h4>
						<p><?php esc_html_e('Virtual robots.txt with one-click AI crawler blocking and custom rules editor.', 'ai-seo-pro'); ?>
						</p>
					</div>

					<div class="feature-card">
						<span class="feature-icon">📋</span>
						<h4><?php esc_html_e('Schema Generator', 'ai-seo-pro'); ?></h4>
						<p><?php esc_html_e('13 schema types with display rules. Target specific pages, post types, or exclude pages.', 'ai-seo-pro'); ?>
						</p>
					</div>

					<div class="feature-card">
						<span class="feature-icon">🔗</span>
						<h4><?php esc_html_e('Site Connections', 'ai-seo-pro'); ?></h4>
						<p><?php esc_html_e('Verify ownership with Google, Bing, Baidu, Yandex, Pinterest, and Ahrefs.', 'ai-seo-pro'); ?>
						</p>
					</div>

					<div class="feature-card">
						<span class="feature-icon">📱</span>
						<h4><?php esc_html_e('Social Media Tags', 'ai-seo-pro'); ?></h4>
						<p><?php esc_html_e('Open Graph and Twitter Card meta tags for better social sharing.', 'ai-seo-pro'); ?>
						</p>
					</div>

					<div class="feature-card">
						<span class="feature-icon">👁️</span>
						<h4><?php esc_html_e('Search Preview', 'ai-seo-pro'); ?></h4>
						<p><?php esc_html_e('See how your content will appear in Google search results before publishing.', 'ai-seo-pro'); ?>
						</p>
					</div>

					<div class="feature-card">
						<span class="feature-icon">⚙️</span>
						<h4><?php esc_html_e('Advanced Options', 'ai-seo-pro'); ?></h4>
						<p><?php esc_html_e('Per-post noindex, nofollow, canonical URL, and exclude from sitemap options.', 'ai-seo-pro'); ?>
						</p>
					</div>

					<div class="feature-card">
						<span class="feature-icon">📦</span>
						<h4><?php esc_html_e('Custom Post Types', 'ai-seo-pro'); ?></h4>
						<p><?php esc_html_e('Works with any public post type including WooCommerce products and portfolios.', 'ai-seo-pro'); ?>
						</p>
					</div>
				</div>
			</div>

			<!-- Schema Types -->
			<div class="help-section">
				<h2><?php esc_html_e('Schema Markup Types', 'ai-seo-pro'); ?></h2>
				<p><?php esc_html_e('AI SEO Pro supports 13 schema types for rich search results:', 'ai-seo-pro'); ?>
				</p>

				<div class="schema-types-grid">
					<div class="schema-type-card">
						<h4>🏢 <?php esc_html_e('Local Business', 'ai-seo-pro'); ?></h4>
						<p><?php esc_html_e('Name, address, hours, phone, geo coordinates, price range, map URL', 'ai-seo-pro'); ?>
						</p>
					</div>

					<div class="schema-type-card">
						<h4>🏛️ <?php esc_html_e('Organization', 'ai-seo-pro'); ?></h4>
						<p><?php esc_html_e('Company name, logo, contact info, founding date, social profiles', 'ai-seo-pro'); ?>
						</p>
					</div>

					<div class="schema-type-card">
						<h4>👤 <?php esc_html_e('Person', 'ai-seo-pro'); ?></h4>
						<p><?php esc_html_e('Name, job title, works for, bio, photo, social profiles', 'ai-seo-pro'); ?>
						</p>
					</div>

					<div class="schema-type-card">
						<h4>🌐 <?php esc_html_e('Website', 'ai-seo-pro'); ?></h4>
						<p><?php esc_html_e('Site name, search action, language, alternate name', 'ai-seo-pro'); ?>
						</p>
					</div>

					<div class="schema-type-card">
						<h4>📰 <?php esc_html_e('Article', 'ai-seo-pro'); ?></h4>
						<p><?php esc_html_e('Headline, author, publisher, dates, word count, image', 'ai-seo-pro'); ?>
						</p>
					</div>

					<div class="schema-type-card">
						<h4>🛒 <?php esc_html_e('Product', 'ai-seo-pro'); ?></h4>
						<p><?php esc_html_e('Name, price, availability, brand, SKU, ratings, reviews', 'ai-seo-pro'); ?>
						</p>
					</div>

					<div class="schema-type-card">
						<h4>❓ <?php esc_html_e('FAQ Page', 'ai-seo-pro'); ?></h4>
						<p><?php esc_html_e('Question and answer pairs with repeater interface', 'ai-seo-pro'); ?></p>
					</div>

					<div class="schema-type-card">
						<h4>📝 <?php esc_html_e('How To', 'ai-seo-pro'); ?></h4>
						<p><?php esc_html_e('Steps, tools, supplies, estimated time and cost', 'ai-seo-pro'); ?></p>
					</div>

					<div class="schema-type-card">
						<h4>📅 <?php esc_html_e('Event', 'ai-seo-pro'); ?></h4>
						<p><?php esc_html_e('Date, venue, tickets, performer, organizer, online/offline', 'ai-seo-pro'); ?>
						</p>
					</div>

					<div class="schema-type-card">
						<h4>🍳 <?php esc_html_e('Recipe', 'ai-seo-pro'); ?></h4>
						<p><?php esc_html_e('Ingredients, instructions, cook time, nutrition, ratings', 'ai-seo-pro'); ?>
						</p>
					</div>

					<div class="schema-type-card">
						<h4>🎬 <?php esc_html_e('Video', 'ai-seo-pro'); ?></h4>
						<p><?php esc_html_e('Title, description, thumbnail, duration, upload date', 'ai-seo-pro'); ?>
						</p>
					</div>

					<div class="schema-type-card">
						<h4>🔗 <?php esc_html_e('Breadcrumb', 'ai-seo-pro'); ?></h4>
						<p><?php esc_html_e('Navigation path with name and URL for each level', 'ai-seo-pro'); ?></p>
					</div>

					<div class="schema-type-card">
						<h4>💻 <?php esc_html_e('Software App', 'ai-seo-pro'); ?></h4>
						<p><?php esc_html_e('App name, category, OS, price, ratings, download URL', 'ai-seo-pro'); ?>
						</p>
					</div>
				</div>

				<div class="help-card" style="margin-top: 20px;">
					<h4><?php esc_html_e('Schema Display Rules', 'ai-seo-pro'); ?></h4>
					<p><?php esc_html_e('Each schema can be targeted to specific pages:', 'ai-seo-pro'); ?></p>
					<ul>
						<li><strong><?php esc_html_e('All Pages', 'ai-seo-pro'); ?></strong> -
							<?php esc_html_e('Display on every page (default)', 'ai-seo-pro'); ?></li>
						<li><strong><?php esc_html_e('Homepage Only', 'ai-seo-pro'); ?></strong> -
							<?php esc_html_e('Display only on the front page', 'ai-seo-pro'); ?></li>
						<li><strong><?php esc_html_e('Specific Post Types', 'ai-seo-pro'); ?></strong> -
							<?php esc_html_e('Display on selected post types (posts, pages, products)', 'ai-seo-pro'); ?>
						</li>
						<li><strong><?php esc_html_e('Only Specific Pages/Posts', 'ai-seo-pro'); ?></strong> -
							<?php esc_html_e('Include list - only show on selected items', 'ai-seo-pro'); ?></li>
						<li><strong><?php esc_html_e('All Except Specific', 'ai-seo-pro'); ?></strong> -
							<?php esc_html_e('Exclude list - show everywhere except selected items', 'ai-seo-pro'); ?>
						</li>
					</ul>
				</div>
			</div>

			<!-- Robots.txt Blocking -->
			<div class="help-section">
				<h2><?php esc_html_e('Robots.txt & Bot Blocking', 'ai-seo-pro'); ?></h2>

				<div class="help-card">
					<h4><?php esc_html_e('AI Crawlers Blocked (14 bots)', 'ai-seo-pro'); ?></h4>
					<p><?php esc_html_e('One-click blocking for AI training bots:', 'ai-seo-pro'); ?></p>
					<code>GPTBot, ChatGPT-User, Google-Extended, CCBot, ClaudeBot, Claude-Web, Bytespider, Omgilibot, FacebookBot, Diffbot, Applebot-Extended, PerplexityBot, cohere-ai</code>
				</div>

				<div class="help-card">
					<h4><?php esc_html_e('Aggressive SEO Bots Blocked (19 bots)', 'ai-seo-pro'); ?></h4>
					<p><?php esc_html_e('One-click blocking for resource-heavy SEO crawlers:', 'ai-seo-pro'); ?></p>
					<code>AhrefsBot, SemrushBot, MJ12bot, DotBot, BLEXBot, SearchmetricsBot, PetalBot, MegaIndex, SEOkicks, linkdexbot, Sogou, BaiduSpider, YandexBot, Exabot, MauiBot, SeznamBot, Rogerbot, MixnodeCache, AspiegelBot</code>
				</div>
			</div>

			<!-- FAQ -->
			<div class="help-section">
				<h2><?php esc_html_e('Frequently Asked Questions', 'ai-seo-pro'); ?></h2>

				<div class="faq-item">
					<h4><?php esc_html_e('Which AI provider should I use?', 'ai-seo-pro'); ?></h4>
					<p><?php esc_html_e('Both providers work excellently. Anthropic Claude excels at natural, engaging language while Google Gemini is powerful for technical content. Choose based on your preference and API pricing.', 'ai-seo-pro'); ?>
					</p>
				</div>

				<div class="faq-item">
					<h4><?php esc_html_e('Do I need an API key for all features?', 'ai-seo-pro'); ?></h4>
					<p><?php esc_html_e('No! API keys are only needed for AI meta generation. All other features (sitemap, redirects, schema, robots.txt) work without an API key.', 'ai-seo-pro'); ?>
					</p>
				</div>

				<div class="faq-item">
					<h4><?php esc_html_e('How much does the API usage cost?', 'ai-seo-pro'); ?></h4>
					<p><?php esc_html_e('Costs vary by provider. Typically, generating meta tags for a single post costs less than $0.01. Check your provider\'s pricing page for current rates.', 'ai-seo-pro'); ?>
					</p>
				</div>

				<div class="faq-item">
					<h4><?php esc_html_e('Will this conflict with other SEO plugins?', 'ai-seo-pro'); ?></h4>
					<p><?php esc_html_e('We recommend deactivating other SEO plugins (Yoast, Rank Math, All in One SEO) to avoid conflicts with meta tags, sitemaps, and schema output.', 'ai-seo-pro'); ?>
					</p>
				</div>

				<div class="faq-item">
					<h4><?php esc_html_e('Can I exclude specific pages from the sitemap?', 'ai-seo-pro'); ?></h4>
					<p><?php esc_html_e('Yes! Each post/page has an "Exclude from Sitemap" checkbox in the AI SEO Pro meta box under Advanced Options.', 'ai-seo-pro'); ?>
					</p>
				</div>

				<div class="faq-item">
					<h4><?php esc_html_e('How do I show different schemas on different pages?', 'ai-seo-pro'); ?></h4>
					<p><?php esc_html_e('Each schema has "Display Rules" where you can choose: All Pages, Homepage Only, Specific Post Types, or Include/Exclude specific pages.', 'ai-seo-pro'); ?>
					</p>
				</div>

				<div class="faq-item">
					<h4><?php esc_html_e('Does the robots.txt editor create a physical file?', 'ai-seo-pro'); ?></h4>
					<p><?php esc_html_e('No, it uses WordPress\'s virtual robots.txt system. This is safer and doesn\'t require file permissions. If you have a physical robots.txt file, the plugin will warn you.', 'ai-seo-pro'); ?>
					</p>
				</div>

				<div class="faq-item">
					<h4><?php esc_html_e('How do I fix 404 errors?', 'ai-seo-pro'); ?></h4>
					<p><?php esc_html_e('Go to AI SEO Pro → Redirects → 404 Monitor tab. You\'ll see all 404 errors with a "Create Redirect" button to quickly redirect them to the correct page.', 'ai-seo-pro'); ?>
					</p>
				</div>

				<div class="faq-item">
					<h4><?php esc_html_e('Can I edit AI-generated content?', 'ai-seo-pro'); ?></h4>
					<p><?php esc_html_e('Yes! All AI-generated content can be edited manually. The AI provides a starting point, but you have full control to customize as needed.', 'ai-seo-pro'); ?>
					</p>
				</div>

				<div class="faq-item">
					<h4><?php esc_html_e('Does it work with custom post types?', 'ai-seo-pro'); ?></h4>
					<p><?php esc_html_e('Yes! You can enable AI SEO Pro for any public custom post type including WooCommerce products, portfolio items, and more.', 'ai-seo-pro'); ?>
					</p>
				</div>
			</div>

			<!-- Troubleshooting -->
			<div class="help-section">
				<h2><?php esc_html_e('Troubleshooting', 'ai-seo-pro'); ?></h2>

				<div class="troubleshooting-item">
					<h4><?php esc_html_e('Meta tags not appearing on site', 'ai-seo-pro'); ?></h4>
					<ul>
						<li><?php esc_html_e('Check if another SEO plugin is active', 'ai-seo-pro'); ?></li>
						<li><?php esc_html_e('Clear your site cache and CDN cache', 'ai-seo-pro'); ?></li>
						<li><?php esc_html_e('View page source (Ctrl+U) to confirm tags are present', 'ai-seo-pro'); ?>
						</li>
					</ul>
				</div>

				<div class="troubleshooting-item">
					<h4><?php esc_html_e('AI generation not working', 'ai-seo-pro'); ?></h4>
					<ul>
						<li><?php esc_html_e('Verify your API key is correct', 'ai-seo-pro'); ?></li>
						<li><?php esc_html_e('Check if you have API credits available', 'ai-seo-pro'); ?></li>
						<li><?php esc_html_e('Ensure your server can make external API calls', 'ai-seo-pro'); ?></li>
						<li><?php esc_html_e('Check browser console (F12) for error messages', 'ai-seo-pro'); ?></li>
					</ul>
				</div>

				<div class="troubleshooting-item">
					<h4><?php esc_html_e('Meta box not visible', 'ai-seo-pro'); ?></h4>
					<ul>
						<li><?php esc_html_e('Check Screen Options at top of edit page', 'ai-seo-pro'); ?></li>
						<li><?php esc_html_e('Verify post type is enabled in Settings → General', 'ai-seo-pro'); ?>
						</li>
						<li><?php esc_html_e('Clear browser cache and refresh', 'ai-seo-pro'); ?></li>
					</ul>
				</div>

				<div class="troubleshooting-item">
					<h4><?php esc_html_e('Sitemap not accessible', 'ai-seo-pro'); ?></h4>
					<ul>
						<li><?php esc_html_e('Go to Settings → Permalinks and click Save (flushes rewrite rules)', 'ai-seo-pro'); ?>
						</li>
						<li><?php esc_html_e('Ensure sitemap is enabled in AI SEO Pro → Settings → Sitemap', 'ai-seo-pro'); ?>
						</li>
						<li><?php esc_html_e('Check if another plugin is generating sitemaps', 'ai-seo-pro'); ?></li>
						<li><?php esc_html_e('Try accessing /sitemap_index.xml directly', 'ai-seo-pro'); ?></li>
					</ul>
				</div>

				<div class="troubleshooting-item">
					<h4><?php esc_html_e('Schema not showing in Rich Results Test', 'ai-seo-pro'); ?></h4>
					<ul>
						<li><?php esc_html_e('Verify schema is enabled and saved', 'ai-seo-pro'); ?></li>
						<li><?php esc_html_e('Check display rules - ensure current page is included', 'ai-seo-pro'); ?>
						</li>
						<li><?php esc_html_e('View page source and search for "application/ld+json"', 'ai-seo-pro'); ?>
						</li>
						<li><?php esc_html_e('Ensure required fields are filled (marked with *)', 'ai-seo-pro'); ?>
						</li>
					</ul>
				</div>

				<div class="troubleshooting-item">
					<h4><?php esc_html_e('Redirects not working', 'ai-seo-pro'); ?></h4>
					<ul>
						<li><?php esc_html_e('Go to Settings → Permalinks and click Save', 'ai-seo-pro'); ?></li>
						<li><?php esc_html_e('Clear all caches (site cache, CDN, browser)', 'ai-seo-pro'); ?></li>
						<li><?php esc_html_e('Ensure source URL starts with /', 'ai-seo-pro'); ?></li>
						<li><?php esc_html_e('Check for conflicting redirects in .htaccess', 'ai-seo-pro'); ?></li>
					</ul>
				</div>

				<div class="troubleshooting-item">
					<h4><?php esc_html_e('Robots.txt changes not appearing', 'ai-seo-pro'); ?></h4>
					<ul>
						<li><?php esc_html_e('Check if a physical robots.txt file exists in your root directory', 'ai-seo-pro'); ?>
						</li>
						<li><?php esc_html_e('Physical files override virtual robots.txt - rename or delete it', 'ai-seo-pro'); ?>
						</li>
						<li><?php esc_html_e('Clear CDN cache if using Cloudflare or similar', 'ai-seo-pro'); ?></li>
						<li><?php esc_html_e('Ensure robots.txt editor is enabled', 'ai-seo-pro'); ?></li>
					</ul>
				</div>
			</div>

			<!-- Useful Links -->
			<div class="help-section">
				<h2><?php esc_html_e('Useful Testing Tools', 'ai-seo-pro'); ?></h2>

				<div class="help-card">
					<ul class="tools-list">
						<li>
							<a href="https://search.google.com/test/rich-results" target="_blank" rel="noopener">
								<?php esc_html_e('Google Rich Results Test', 'ai-seo-pro'); ?>
							</a>
							- <?php esc_html_e('Test your schema markup', 'ai-seo-pro'); ?>
						</li>
						<li>
							<a href="https://validator.schema.org/" target="_blank" rel="noopener">
								<?php esc_html_e('Schema.org Validator', 'ai-seo-pro'); ?>
							</a>
							- <?php esc_html_e('Validate JSON-LD structured data', 'ai-seo-pro'); ?>
						</li>
						<li>
							<a href="https://search.google.com/search-console" target="_blank" rel="noopener">
								<?php esc_html_e('Google Search Console', 'ai-seo-pro'); ?>
							</a>
							- <?php esc_html_e('Submit sitemap and monitor search performance', 'ai-seo-pro'); ?>
						</li>
						<li>
							<a href="https://www.bing.com/webmasters" target="_blank" rel="noopener">
								<?php esc_html_e('Bing Webmaster Tools', 'ai-seo-pro'); ?>
							</a>
							- <?php esc_html_e('Submit sitemap to Bing', 'ai-seo-pro'); ?>
						</li>
						<li>
							<a href="https://developers.facebook.com/tools/debug/" target="_blank" rel="noopener">
								<?php esc_html_e('Facebook Sharing Debugger', 'ai-seo-pro'); ?>
							</a>
							- <?php esc_html_e('Test Open Graph tags', 'ai-seo-pro'); ?>
						</li>
						<li>
							<a href="https://cards-dev.twitter.com/validator" target="_blank" rel="noopener">
								<?php esc_html_e('Twitter Card Validator', 'ai-seo-pro'); ?>
							</a>
							- <?php esc_html_e('Test Twitter Card tags', 'ai-seo-pro'); ?>
						</li>
					</ul>
				</div>
			</div>
		</div>

		<div class="help-sidebar">
			<?php require_once AI_SEO_PRO_PLUGIN_DIR . 'admin/partials/help-sidebar.php'; ?>
		</div>
	</div>
</div>

<style>
	.ai-seo-pro-help .help-content {
		display: flex;
		gap: 30px;
		margin-top: 20px;
	}

	.help-main {
		flex: 1;
	}

	.help-sidebar {
		width: 300px;
	}

	.help-section {
		margin-bottom: 40px;
	}

	.help-section h2 {
		font-size: 24px;
		margin-bottom: 20px;
		color: #1d2327;
		padding-bottom: 10px;
		border-bottom: 2px solid #2271b1;
	}

	.help-card {
		background: #fff;
		border: 1px solid #ddd;
		border-radius: 6px;
		padding: 20px;
		margin-bottom: 20px;
	}

	.help-card h3 {
		margin-top: 0;
		color: #2271b1;
		font-size: 18px;
	}

	.help-card h4 {
		margin-top: 0;
		color: #1d2327;
		font-size: 16px;
	}

	.help-card ul,
	.help-card ol {
		margin: 10px 0;
		padding-left: 25px;
	}

	.help-card li {
		margin-bottom: 8px;
	}

	.help-card code {
		background: #f0f0f1;
		padding: 8px 12px;
		display: block;
		margin-top: 10px;
		border-radius: 4px;
		font-size: 12px;
		word-break: break-word;
	}

	.features-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
		gap: 20px;
	}

	.feature-card {
		background: #fff;
		border: 1px solid #ddd;
		border-radius: 6px;
		padding: 20px;
		text-align: center;
		transition: box-shadow 0.2s, transform 0.2s;
	}

	.feature-card:hover {
		box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
		transform: translateY(-2px);
	}

	.feature-icon {
		font-size: 40px;
		display: block;
		margin-bottom: 12px;
	}

	.feature-card h4 {
		margin: 10px 0;
		font-size: 15px;
		color: #1d2327;
	}

	.feature-card p {
		font-size: 13px;
		color: #666;
		margin: 0;
		line-height: 1.5;
	}

	/* Schema Types Grid */
	.schema-types-grid {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
		gap: 15px;
	}

	.schema-type-card {
		background: #fff;
		border: 1px solid #ddd;
		border-radius: 6px;
		padding: 15px;
	}

	.schema-type-card h4 {
		margin: 0 0 8px 0;
		font-size: 14px;
		color: #1d2327;
	}

	.schema-type-card p {
		margin: 0;
		font-size: 12px;
		color: #666;
		line-height: 1.4;
	}

	/* FAQ */
	.faq-item,
	.troubleshooting-item {
		background: #fff;
		border: 1px solid #ddd;
		border-radius: 6px;
		padding: 20px;
		margin-bottom: 15px;
	}

	.faq-item h4,
	.troubleshooting-item h4 {
		margin-top: 0;
		color: #1d2327;
		font-size: 15px;
	}

	.faq-item p {
		margin: 0;
		color: #555;
		line-height: 1.6;
	}

	.troubleshooting-item ul {
		margin: 10px 0 0 0;
		padding-left: 25px;
	}

	.troubleshooting-item li {
		margin-bottom: 5px;
	}

	/* Tools List */
	.tools-list {
		list-style: none;
		padding: 0;
		margin: 0;
	}

	.tools-list li {
		padding: 10px 0;
		border-bottom: 1px solid #eee;
	}

	.tools-list li:last-child {
		border-bottom: none;
	}

	.tools-list a {
		color: #2271b1;
		text-decoration: none;
		font-weight: 500;
	}

	.tools-list a:hover {
		text-decoration: underline;
	}

	@media (max-width: 1200px) {
		.ai-seo-pro-help .help-content {
			flex-direction: column;
		}

		.help-sidebar {
			width: 100%;
		}
	}

	@media (max-width: 782px) {

		.features-grid,
		.schema-types-grid {
			grid-template-columns: 1fr;
		}
	}
</style>