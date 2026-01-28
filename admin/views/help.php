<?php
/**
 * Help page.
 *
 * @package    RankFlow_SEO
 * @subpackage RankFlow_SEO/admin/views
 */

if (!defined('ABSPATH')) {
	exit;
}
?>

<div class="wrap rankflow-seo-help">
	<?php require_once RANKFLOW_SEO_PLUGIN_DIR . 'admin/partials/header.php'; ?>

	<div class="help-content">
		<div class="help-main">
			<!-- Getting Started -->
			<div class="help-section">
				<h2><?php esc_html_e('Getting Started', 'rankflow-seo'); ?></h2>

				<div class="help-card">
					<h3><?php esc_html_e('1. Configure API Settings', 'rankflow-seo'); ?></h3>
					<p><?php esc_html_e('Navigate to RankFlow SEO → Settings → AI API tab and configure your preferred AI provider:', 'rankflow-seo'); ?>
					</p>
					<ul>
						<li><?php esc_html_e('Choose your AI provider (Anthropic Claude or Google Gemini)', 'rankflow-seo'); ?>
						</li>
						<li><?php esc_html_e('Enter your API key', 'rankflow-seo'); ?></li>
						<li><?php esc_html_e('Optionally enable auto-generation', 'rankflow-seo'); ?></li>
					</ul>
				</div>

				<div class="help-card">
					<h3><?php esc_html_e('2. Enable Post Types', 'rankflow-seo'); ?></h3>
					<p><?php esc_html_e('Go to Settings → General and select which post types should have SEO optimization:', 'rankflow-seo'); ?>
					</p>
					<ul>
						<li><?php esc_html_e('Posts', 'rankflow-seo'); ?></li>
						<li><?php esc_html_e('Pages', 'rankflow-seo'); ?></li>
						<li><?php esc_html_e('Custom post types (WooCommerce products, portfolio, etc.)', 'rankflow-seo'); ?>
						</li>
					</ul>
				</div>

				<div class="help-card">
					<h3><?php esc_html_e('3. Optimize Your Content', 'rankflow-seo'); ?></h3>
					<p><?php esc_html_e('When editing any post:', 'rankflow-seo'); ?></p>
					<ol>
						<li><?php esc_html_e('Scroll to the RankFlow SEO meta box', 'rankflow-seo'); ?></li>
						<li><?php esc_html_e('Enter your focus keyword (optional)', 'rankflow-seo'); ?></li>
						<li><?php esc_html_e('Click "Generate Now with AI" button', 'rankflow-seo'); ?></li>
						<li><?php esc_html_e('Review and edit the generated meta tags', 'rankflow-seo'); ?></li>
						<li><?php esc_html_e('Check your SEO score and recommendations', 'rankflow-seo'); ?></li>
					</ol>
				</div>

				<div class="help-card">
					<h3><?php esc_html_e('4. Configure XML Sitemap', 'rankflow-seo'); ?></h3>
					<p><?php esc_html_e('Go to RankFlow SEO → Settings → Sitemap tab:', 'rankflow-seo'); ?></p>
					<ul>
						<li><?php esc_html_e('Enable sitemap generation', 'rankflow-seo'); ?></li>
						<li><?php esc_html_e('Select post types to include', 'rankflow-seo'); ?></li>
						<li><?php esc_html_e('Your sitemap will be available at /sitemap_index.xml', 'rankflow-seo'); ?>
						</li>
						<li><?php esc_html_e('Submit sitemap URL to Google Search Console', 'rankflow-seo'); ?></li>
					</ul>
				</div>

				<div class="help-card">
					<h3><?php esc_html_e('5. Set Up Schema Markup', 'rankflow-seo'); ?></h3>
					<p><?php esc_html_e('Navigate to RankFlow SEO → Schema:', 'rankflow-seo'); ?></p>
					<ol>
						<li><?php esc_html_e('Click "Add Schema" button', 'rankflow-seo'); ?></li>
						<li><?php esc_html_e('Select schema type (Local Business, Organization, etc.)', 'rankflow-seo'); ?>
						</li>
						<li><?php esc_html_e('Fill in the dynamic fields', 'rankflow-seo'); ?></li>
						<li><?php esc_html_e('Configure display rules (where to show)', 'rankflow-seo'); ?></li>
						<li><?php esc_html_e('Save and test with Google Rich Results Test', 'rankflow-seo'); ?></li>
					</ol>
				</div>

				<div class="help-card">
					<h3><?php esc_html_e('6. Verify Webmaster Tools', 'rankflow-seo'); ?></h3>
					<p><?php esc_html_e('Go to RankFlow SEO → Settings → Site Connections tab:', 'rankflow-seo'); ?></p>
					<ul>
						<li><?php esc_html_e('Paste your verification meta tag or code', 'rankflow-seo'); ?></li>
						<li><?php esc_html_e('The plugin automatically extracts the verification code', 'rankflow-seo'); ?>
						</li>
						<li><?php esc_html_e('Supported: Google, Bing, Baidu, Yandex, Pinterest, Ahrefs', 'rankflow-seo'); ?>
						</li>
					</ul>
				</div>
			</div>

			<!-- Features Overview -->
			<div class="help-section">
				<h2><?php esc_html_e('Features Overview', 'rankflow-seo'); ?></h2>

				<div class="features-grid">
					<div class="feature-card">
						<span class="feature-icon">
							<img src="<?php echo esc_url(RANKFLOW_SEO_PLUGIN_URL . 'assets/images/ai-meta-generator-icon.png'); ?>"
								alt="AI Meta Generation" class="rankflow-seo-icon-35">
						</span>
						<h4><?php esc_html_e('AI Meta Generation', 'rankflow-seo'); ?></h4>
						<p><?php esc_html_e('Generate SEO-optimized meta titles and descriptions using Anthropic Claude or Google Gemini AI.', 'rankflow-seo'); ?>
						</p>
					</div>

					<div class="feature-card">
						<span class="feature-icon">
							<img src="<?php echo esc_url(RANKFLOW_SEO_PLUGIN_URL . 'assets/images/seo-analysis-icon.png'); ?>"
								alt="SEO Analysis" class="rankflow-seo-icon-32">
						</span>
						<h4><?php esc_html_e('SEO Analysis', 'rankflow-seo'); ?></h4>
						<p><?php esc_html_e('17 comprehensive SEO checks with color-coded scores and actionable recommendations.', 'rankflow-seo'); ?>
						</p>
					</div>

					<div class="feature-card">
						<span class="feature-icon">
							<img src="<?php echo esc_url(RANKFLOW_SEO_PLUGIN_URL . 'assets/images/readyblity-analysis-icon.png'); ?>"
								alt="Readability Analysis" class="rankflow-seo-icon-32">
						</span>
						<h4><?php esc_html_e('Readability Analysis', 'rankflow-seo'); ?></h4>
						<p><?php esc_html_e('8 readability checks including sentence length, paragraph structure, and passive voice.', 'rankflow-seo'); ?>
						</p>
					</div>

					<div class="feature-card">
						<span class="feature-icon">
							<img src="<?php echo esc_url(RANKFLOW_SEO_PLUGIN_URL . 'assets/images/xml-sitemap-icon.png'); ?>"
								alt="XML Sitemap" class="rankflow-seo-icon-32">
						</span>
						<h4><?php esc_html_e('XML Sitemap', 'rankflow-seo'); ?></h4>
						<p><?php esc_html_e('Automatic sitemap generation with post, page, and category sitemaps. Auto-ping search engines.', 'rankflow-seo'); ?>
						</p>
					</div>

					<div class="feature-card">
						<span class="feature-icon">
							<img src="<?php echo esc_url(RANKFLOW_SEO_PLUGIN_URL . 'assets/images/redirect-manager-icon.png'); ?>"
								alt="Redirect Manager" class="rankflow-seo-icon-32">
						</span>
						<h4><?php esc_html_e('Redirect Manager', 'rankflow-seo'); ?></h4>
						<p><?php esc_html_e('Create 301/302 redirects, monitor 404 errors, and fix broken links with one click.', 'rankflow-seo'); ?>
						</p>
					</div>

					<div class="feature-card">
						<span class="feature-icon">
							<img src="<?php echo esc_url(RANKFLOW_SEO_PLUGIN_URL . 'assets/images/robots-txt-editor-icon.png'); ?>"
								alt="Robots.txt Editor" class="rankflow-seo-icon-32">
						</span>
						<h4><?php esc_html_e('Robots.txt Editor', 'rankflow-seo'); ?></h4>
						<p><?php esc_html_e('Virtual robots.txt with one-click AI crawler blocking and custom rules editor.', 'rankflow-seo'); ?>
						</p>
					</div>

					<div class="feature-card">
						<span class="feature-icon">
							<img src="<?php echo esc_url(RANKFLOW_SEO_PLUGIN_URL . 'assets/images/schema-generator-icon.png'); ?>"
								alt="Schema Generator" class="rankflow-seo-icon-32">
						</span>
						<h4><?php esc_html_e('Schema Generator', 'rankflow-seo'); ?></h4>
						<p><?php esc_html_e('14 schema types with display rules. Target specific pages, post types, or exclude pages.', 'rankflow-seo'); ?>
						</p>
					</div>

					<div class="feature-card">
						<span class="feature-icon">
							<img src="<?php echo esc_url(RANKFLOW_SEO_PLUGIN_URL . 'assets/images/site-conneciton-icon.png'); ?>"
								alt="Site Connections" class="rankflow-seo-icon-32">
						</span>
						<h4><?php esc_html_e('Site Connections', 'rankflow-seo'); ?></h4>
						<p><?php esc_html_e('Verify ownership with Google, Bing, Baidu, Yandex, Pinterest, and Ahrefs.', 'rankflow-seo'); ?>
						</p>
					</div>

					<div class="feature-card">
						<span class="feature-icon">
							<img src="<?php echo esc_url(RANKFLOW_SEO_PLUGIN_URL . 'assets/images/social-media-tags-icon.png'); ?>"
								alt="Social Media Tags" class="rankflow-seo-icon-32">
						</span>
						<h4><?php esc_html_e('Social Media Tags', 'rankflow-seo'); ?></h4>
						<p><?php esc_html_e('Open Graph and Twitter Card meta tags for better social sharing.', 'rankflow-seo'); ?>
						</p>
					</div>

					<div class="feature-card">
						<span class="feature-icon">
							<img src="<?php echo esc_url(RANKFLOW_SEO_PLUGIN_URL . 'assets/images/search-preview-icon.png'); ?>"
								alt="Search Preview" class="rankflow-seo-icon-32">
						</span>
						<h4><?php esc_html_e('Search Preview', 'rankflow-seo'); ?></h4>
						<p><?php esc_html_e('See how your content will appear in Google search results before publishing.', 'rankflow-seo'); ?>
						</p>
					</div>

					<div class="feature-card">
						<span class="feature-icon">
							<img src="<?php echo esc_url(RANKFLOW_SEO_PLUGIN_URL . 'assets/images/advanced-options-icon.png'); ?>"
								alt="Advanced Options" class="rankflow-seo-icon-32">
						</span>
						<h4><?php esc_html_e('Advanced Options', 'rankflow-seo'); ?></h4>
						<p><?php esc_html_e('Per-post noindex, nofollow, canonical URL, and exclude from sitemap options.', 'rankflow-seo'); ?>
						</p>
					</div>

					<div class="feature-card">
						<span class="feature-icon">
							<img src="<?php echo esc_url(RANKFLOW_SEO_PLUGIN_URL . 'assets/images/custom-post-type-icon.png'); ?>"
								alt="Custom Post Types" class="rankflow-seo-icon-32">
						</span>
						<h4><?php esc_html_e('Custom Post Types', 'rankflow-seo'); ?></h4>
						<p><?php esc_html_e('Works with any public post type including WooCommerce products and portfolios.', 'rankflow-seo'); ?>
						</p>
					</div>
				</div>
			</div>

			<!-- Schema Types -->
			<div class="help-section">
				<h2><?php esc_html_e('Schema Markup Types', 'rankflow-seo'); ?></h2>
				<p><?php esc_html_e('RankFlow SEO supports 13 schema types for rich search results:', 'rankflow-seo'); ?>
				</p>

				<div class="schema-types-grid">
					<div class="schema-type-card">
						<h4><?php esc_html_e('Local Business', 'rankflow-seo'); ?></h4>
						<p><?php esc_html_e('Name, address, hours, phone, geo coordinates, price range, map URL', 'rankflow-seo'); ?>
						</p>
					</div>

					<div class="schema-type-card">
						<h4><?php esc_html_e('Organization', 'rankflow-seo'); ?></h4>
						<p><?php esc_html_e('Company name, logo, contact info, founding date, social profiles', 'rankflow-seo'); ?>
						</p>
					</div>

					<div class="schema-type-card">
						<h4><?php esc_html_e('Person', 'rankflow-seo'); ?></h4>
						<p><?php esc_html_e('Name, job title, works for, bio, photo, social profiles', 'rankflow-seo'); ?>
						</p>
					</div>

					<div class="schema-type-card">
						<h4><?php esc_html_e('Website', 'rankflow-seo'); ?></h4>
						<p><?php esc_html_e('Site name, search action, language, alternate name', 'rankflow-seo'); ?>
						</p>
					</div>

					<div class="schema-type-card">
						<h4><?php esc_html_e('Article', 'rankflow-seo'); ?></h4>
						<p><?php esc_html_e('Headline, author, publisher, dates, word count, image', 'rankflow-seo'); ?>
						</p>
					</div>

					<div class="schema-type-card">
						<h4><?php esc_html_e('Product', 'rankflow-seo'); ?></h4>
						<p><?php esc_html_e('Name, price, availability, brand, SKU, ratings, reviews', 'rankflow-seo'); ?>
						</p>
					</div>

					<div class="schema-type-card">
						<h4><?php esc_html_e('Service', 'rankflow-seo'); ?></h4>
						<p><?php esc_html_e('Name, description, provider, area served, price, offer catalog, ratings', 'rankflow-seo'); ?>
						</p>
					</div>

					<div class="schema-type-card">
						<h4><?php esc_html_e('FAQ Page', 'rankflow-seo'); ?></h4>
						<p><?php esc_html_e('Question and answer pairs with repeater interface', 'rankflow-seo'); ?></p>
					</div>

					<div class="schema-type-card">
						<h4><?php esc_html_e('How To', 'rankflow-seo'); ?></h4>
						<p><?php esc_html_e('Steps, tools, supplies, estimated time and cost', 'rankflow-seo'); ?></p>
					</div>

					<div class="schema-type-card">
						<h4><?php esc_html_e('Event', 'rankflow-seo'); ?></h4>
						<p><?php esc_html_e('Date, venue, tickets, performer, organizer, online/offline', 'rankflow-seo'); ?>
						</p>
					</div>

					<div class="schema-type-card">
						<h4><?php esc_html_e('Recipe', 'rankflow-seo'); ?></h4>
						<p><?php esc_html_e('Ingredients, instructions, cook time, nutrition, ratings', 'rankflow-seo'); ?>
						</p>
					</div>

					<div class="schema-type-card">
						<h4><?php esc_html_e('Video', 'rankflow-seo'); ?></h4>
						<p><?php esc_html_e('Title, description, thumbnail, duration, upload date', 'rankflow-seo'); ?>
						</p>
					</div>

					<div class="schema-type-card">
						<h4><?php esc_html_e('Breadcrumb', 'rankflow-seo'); ?></h4>
						<p><?php esc_html_e('Navigation path with name and URL for each level', 'rankflow-seo'); ?></p>
					</div>

					<div class="schema-type-card">
						<h4><?php esc_html_e('Software App', 'rankflow-seo'); ?></h4>
						<p><?php esc_html_e('App name, category, OS, price, ratings, download URL', 'rankflow-seo'); ?>
						</p>
					</div>
				</div>

				<div class="help-card rankflow-seo-mt-20">
					<h4><?php esc_html_e('Schema Display Rules', 'rankflow-seo'); ?></h4>
					<p><?php esc_html_e('Each schema can be targeted to specific pages:', 'rankflow-seo'); ?></p>
					<ul>
						<li><strong><?php esc_html_e('All Pages', 'rankflow-seo'); ?></strong> -
							<?php esc_html_e('Display on every page (default)', 'rankflow-seo'); ?>
						</li>
						<li><strong><?php esc_html_e('Homepage Only', 'rankflow-seo'); ?></strong> -
							<?php esc_html_e('Display only on the front page', 'rankflow-seo'); ?>
						</li>
						<li><strong><?php esc_html_e('Specific Post Types', 'rankflow-seo'); ?></strong> -
							<?php esc_html_e('Display on selected post types (posts, pages, products)', 'rankflow-seo'); ?>
						</li>
						<li><strong><?php esc_html_e('Only Specific Pages/Posts', 'rankflow-seo'); ?></strong> -
							<?php esc_html_e('Include list - only show on selected items', 'rankflow-seo'); ?>
						</li>
						<li><strong><?php esc_html_e('All Except Specific', 'rankflow-seo'); ?></strong> -
							<?php esc_html_e('Exclude list - show everywhere except selected items', 'rankflow-seo'); ?>
						</li>
					</ul>
				</div>
			</div>

			<!-- Robots.txt Blocking -->
			<div class="help-section">
				<h2><?php esc_html_e('Robots.txt & Bot Blocking', 'rankflow-seo'); ?></h2>

				<div class="help-card">
					<h4><?php esc_html_e('AI Crawlers Blocked (14 bots)', 'rankflow-seo'); ?></h4>
					<p><?php esc_html_e('One-click blocking for AI training bots:', 'rankflow-seo'); ?></p>
					<code>GPTBot, ChatGPT-User, Google-Extended, CCBot, ClaudeBot, Claude-Web, Bytespider, Omgilibot, FacebookBot, Diffbot, Applebot-Extended, PerplexityBot, cohere-ai</code>
				</div>

				<div class="help-card">
					<h4><?php esc_html_e('Aggressive SEO Bots Blocked (19 bots)', 'rankflow-seo'); ?></h4>
					<p><?php esc_html_e('One-click blocking for resource-heavy SEO crawlers:', 'rankflow-seo'); ?></p>
					<code>AhrefsBot, SemrushBot, MJ12bot, DotBot, BLEXBot, SearchmetricsBot, PetalBot, MegaIndex, SEOkicks, linkdexbot, Sogou, BaiduSpider, YandexBot, Exabot, MauiBot, SeznamBot, Rogerbot, MixnodeCache, AspiegelBot</code>
				</div>
			</div>

			<!-- FAQ -->
			<div class="help-section">
				<h2><?php esc_html_e('Frequently Asked Questions', 'rankflow-seo'); ?></h2>

				<div class="faq-item">
					<h4><?php esc_html_e('Which AI provider should I use?', 'rankflow-seo'); ?></h4>
					<p><?php esc_html_e('Both providers work excellently. Anthropic Claude excels at natural, engaging language while Google Gemini is powerful for technical content. Choose based on your preference and API pricing.', 'rankflow-seo'); ?>
					</p>
				</div>

				<div class="faq-item">
					<h4><?php esc_html_e('Do I need an API key for all features?', 'rankflow-seo'); ?></h4>
					<p><?php esc_html_e('No! API keys are only needed for AI meta generation. All other features (sitemap, redirects, schema, robots.txt) work without an API key.', 'rankflow-seo'); ?>
					</p>
				</div>

				<div class="faq-item">
					<h4><?php esc_html_e('How much does the API usage cost?', 'rankflow-seo'); ?></h4>
					<p><?php esc_html_e('Costs vary by provider. Typically, generating meta tags for a single post costs less than $0.01. Check your provider\'s pricing page for current rates.', 'rankflow-seo'); ?>
					</p>
				</div>

				<div class="faq-item">
					<h4><?php esc_html_e('Will this conflict with other SEO plugins?', 'rankflow-seo'); ?></h4>
					<p><?php esc_html_e('We recommend deactivating other SEO plugins (Yoast, Rank Math, All in One SEO) to avoid conflicts with meta tags, sitemaps, and schema output.', 'rankflow-seo'); ?>
					</p>
				</div>

				<div class="faq-item">
					<h4><?php esc_html_e('Can I exclude specific pages from the sitemap?', 'rankflow-seo'); ?></h4>
					<p><?php esc_html_e('Yes! Each post/page has an "Exclude from Sitemap" checkbox in the RankFlow SEO meta box under Advanced Options.', 'rankflow-seo'); ?>
					</p>
				</div>

				<div class="faq-item">
					<h4><?php esc_html_e('How do I show different schemas on different pages?', 'rankflow-seo'); ?></h4>
					<p><?php esc_html_e('Each schema has "Display Rules" where you can choose: All Pages, Homepage Only, Specific Post Types, or Include/Exclude specific pages.', 'rankflow-seo'); ?>
					</p>
				</div>

				<div class="faq-item">
					<h4><?php esc_html_e('Does the robots.txt editor create a physical file?', 'rankflow-seo'); ?></h4>
					<p><?php esc_html_e('No, it uses WordPress\'s virtual robots.txt system. This is safer and doesn\'t require file permissions. If you have a physical robots.txt file, the plugin will warn you.', 'rankflow-seo'); ?>
					</p>
				</div>

				<div class="faq-item">
					<h4><?php esc_html_e('How do I fix 404 errors?', 'rankflow-seo'); ?></h4>
					<p><?php esc_html_e('Go to RankFlow SEO → Redirects → 404 Monitor tab. You\'ll see all 404 errors with a "Create Redirect" button to quickly redirect them to the correct page.', 'rankflow-seo'); ?>
					</p>
				</div>

				<div class="faq-item">
					<h4><?php esc_html_e('Can I edit AI-generated content?', 'rankflow-seo'); ?></h4>
					<p><?php esc_html_e('Yes! All AI-generated content can be edited manually. The AI provides a starting point, but you have full control to customize as needed.', 'rankflow-seo'); ?>
					</p>
				</div>

				<div class="faq-item">
					<h4><?php esc_html_e('Does it work with custom post types?', 'rankflow-seo'); ?></h4>
					<p><?php esc_html_e('Yes! You can enable RankFlow SEO for any public custom post type including WooCommerce products, portfolio items, and more.', 'rankflow-seo'); ?>
					</p>
				</div>
			</div>

			<!-- Troubleshooting -->
			<div class="help-section">
				<h2><?php esc_html_e('Troubleshooting', 'rankflow-seo'); ?></h2>

				<div class="troubleshooting-item">
					<h4><?php esc_html_e('Meta tags not appearing on site', 'rankflow-seo'); ?></h4>
					<ul>
						<li><?php esc_html_e('Check if another SEO plugin is active', 'rankflow-seo'); ?></li>
						<li><?php esc_html_e('Clear your site cache and CDN cache', 'rankflow-seo'); ?></li>
						<li><?php esc_html_e('View page source (Ctrl+U) to confirm tags are present', 'rankflow-seo'); ?>
						</li>
					</ul>
				</div>

				<div class="troubleshooting-item">
					<h4><?php esc_html_e('AI generation not working', 'rankflow-seo'); ?></h4>
					<ul>
						<li><?php esc_html_e('Verify your API key is correct', 'rankflow-seo'); ?></li>
						<li><?php esc_html_e('Check if you have API credits available', 'rankflow-seo'); ?></li>
						<li><?php esc_html_e('Ensure your server can make external API calls', 'rankflow-seo'); ?></li>
						<li><?php esc_html_e('Check browser console (F12) for error messages', 'rankflow-seo'); ?></li>
					</ul>
				</div>

				<div class="troubleshooting-item">
					<h4><?php esc_html_e('Meta box not visible', 'rankflow-seo'); ?></h4>
					<ul>
						<li><?php esc_html_e('Check Screen Options at top of edit page', 'rankflow-seo'); ?></li>
						<li><?php esc_html_e('Verify post type is enabled in Settings → General', 'rankflow-seo'); ?>
						</li>
						<li><?php esc_html_e('Clear browser cache and refresh', 'rankflow-seo'); ?></li>
					</ul>
				</div>

				<div class="troubleshooting-item">
					<h4><?php esc_html_e('Sitemap not accessible', 'rankflow-seo'); ?></h4>
					<ul>
						<li><?php esc_html_e('Go to Settings → Permalinks and click Save (flushes rewrite rules)', 'rankflow-seo'); ?>
						</li>
						<li><?php esc_html_e('Ensure sitemap is enabled in RankFlow SEO → Settings → Sitemap', 'rankflow-seo'); ?>
						</li>
						<li><?php esc_html_e('Check if another plugin is generating sitemaps', 'rankflow-seo'); ?></li>
						<li><?php esc_html_e('Try accessing /sitemap_index.xml directly', 'rankflow-seo'); ?></li>
					</ul>
				</div>

				<div class="troubleshooting-item">
					<h4><?php esc_html_e('Schema not showing in Rich Results Test', 'rankflow-seo'); ?></h4>
					<ul>
						<li><?php esc_html_e('Verify schema is enabled and saved', 'rankflow-seo'); ?></li>
						<li><?php esc_html_e('Check display rules - ensure current page is included', 'rankflow-seo'); ?>
						</li>
						<li><?php esc_html_e('View page source and search for "application/ld+json"', 'rankflow-seo'); ?>
						</li>
						<li><?php esc_html_e('Ensure required fields are filled (marked with *)', 'rankflow-seo'); ?>
						</li>
					</ul>
				</div>

				<div class="troubleshooting-item">
					<h4><?php esc_html_e('Redirects not working', 'rankflow-seo'); ?></h4>
					<ul>
						<li><?php esc_html_e('Go to Settings → Permalinks and click Save', 'rankflow-seo'); ?></li>
						<li><?php esc_html_e('Clear all caches (site cache, CDN, browser)', 'rankflow-seo'); ?></li>
						<li><?php esc_html_e('Ensure source URL starts with /', 'rankflow-seo'); ?></li>
						<li><?php esc_html_e('Check for conflicting redirects in .htaccess', 'rankflow-seo'); ?></li>
					</ul>
				</div>

				<div class="troubleshooting-item">
					<h4><?php esc_html_e('Robots.txt changes not appearing', 'rankflow-seo'); ?></h4>
					<ul>
						<li><?php esc_html_e('Check if a physical robots.txt file exists in your root directory', 'rankflow-seo'); ?>
						</li>
						<li><?php esc_html_e('Physical files override virtual robots.txt - rename or delete it', 'rankflow-seo'); ?>
						</li>
						<li><?php esc_html_e('Clear CDN cache if using Cloudflare or similar', 'rankflow-seo'); ?></li>
						<li><?php esc_html_e('Ensure robots.txt editor is enabled', 'rankflow-seo'); ?></li>
					</ul>
				</div>
			</div>

			<!-- Useful Links -->
			<div class="help-section">
				<h2><?php esc_html_e('Useful Testing Tools', 'rankflow-seo'); ?></h2>

				<div class="help-card">
					<ul class="tools-list">
						<li>
							<a href="https://search.google.com/test/rich-results" target="_blank" rel="noopener">
								<?php esc_html_e('Google Rich Results Test', 'rankflow-seo'); ?>
							</a>
							- <?php esc_html_e('Test your schema markup', 'rankflow-seo'); ?>
						</li>
						<li>
							<a href="https://validator.schema.org/" target="_blank" rel="noopener">
								<?php esc_html_e('Schema.org Validator', 'rankflow-seo'); ?>
							</a>
							- <?php esc_html_e('Validate JSON-LD structured data', 'rankflow-seo'); ?>
						</li>
						<li>
							<a href="https://search.google.com/search-console" target="_blank" rel="noopener">
								<?php esc_html_e('Google Search Console', 'rankflow-seo'); ?>
							</a>
							- <?php esc_html_e('Submit sitemap and monitor search performance', 'rankflow-seo'); ?>
						</li>
						<li>
							<a href="https://www.bing.com/webmasters" target="_blank" rel="noopener">
								<?php esc_html_e('Bing Webmaster Tools', 'rankflow-seo'); ?>
							</a>
							- <?php esc_html_e('Submit sitemap to Bing', 'rankflow-seo'); ?>
						</li>
						<li>
							<a href="https://developers.facebook.com/tools/debug/" target="_blank" rel="noopener">
								<?php esc_html_e('Facebook Sharing Debugger', 'rankflow-seo'); ?>
							</a>
							- <?php esc_html_e('Test Open Graph tags', 'rankflow-seo'); ?>
						</li>
						<li>
							<a href="https://cards-dev.twitter.com/validator" target="_blank" rel="noopener">
								<?php esc_html_e('Twitter Card Validator', 'rankflow-seo'); ?>
							</a>
							- <?php esc_html_e('Test Twitter Card tags', 'rankflow-seo'); ?>
						</li>
					</ul>
				</div>
			</div>
		</div>

		<div class="help-sidebar">
			<?php require_once RANKFLOW_SEO_PLUGIN_DIR . 'admin/partials/help-sidebar.php'; ?>
		</div>
	</div>
</div>

