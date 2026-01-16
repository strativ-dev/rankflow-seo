# RankFlow SEO - Tabbed Analysis Feature

## New Files to Add

### 1. Core Analyzer Classes (includes/core/)

- `class-seo-analyzer.php` - Comprehensive SEO analysis returning problems/good results
- `class-readability-analyzer.php` - Readability analysis with problems/good results

### 2. Admin Files (admin/)

- `class-metabox.php` - Updated metabox class with tab support and analysis
- `views/metabox.php` - New tabbed metabox view

### 3. Assets

- `assets/css/metabox.css` - Updated styling with tabs and analysis accordion
- `assets/js/metabox.js` - Updated JavaScript with tab switching and real-time analysis

---

## Integration Steps

### Step 1: Add New Files

Copy the following files to your plugin:

```
rankflow-seo/
├── admin/
│   ├── class-metabox.php (replace existing)
│   └── views/
│       └── metabox.php (replace existing)
├── assets/
│   ├── css/
│   │   └── metabox.css (replace existing)
│   └── js/
│       └── metabox.js (replace existing)
└── includes/
    └── core/
        ├── class-seo-analyzer.php (new file)
        └── class-readability-analyzer.php (new file)
```

### Step 2: Include New Classes

In your main plugin file or bootstrap, add the includes for the new analyzer classes:

```php
// Add these includes in your plugin loader/bootstrap
require_once RANKFLOW_SEO_PLUGIN_DIR . 'includes/core/class-seo-analyzer.php';
require_once RANKFLOW_SEO_PLUGIN_DIR . 'includes/core/class-readability-analyzer.php';
```

### Step 3: Register New AJAX Hook

Make sure to register the new AJAX action in your hooks setup:

```php
// In class-rankflow-seo.php or wherever you define hooks
add_action('wp_ajax_rankflow_seo_update_analysis', array($metabox_instance, 'ajax_update_analysis'));
```

Or call the `register_ajax_hooks()` method from the metabox class.

---

## Features Added

### SEO Tab
- SEO Score display
- AI Generation options with field filters
- Focus Keyphrase input
- SEO Title with character counter
- Slug preview
- Meta Description with character counter
- Meta Keywords
- Search Engine Preview
- **SEO Analysis Accordion** with:
  - Problems (red indicators)
  - Good results (green indicators)
- Advanced Options (collapsible)

### Readability Tab
- Readability Score (Flesch Reading Ease)
- Grade Level display
- **Readability Analysis Accordion** with:
  - Problems (red indicators)
  - Good results (green indicators)
- Readability Tips

---

## SEO Analysis Checks

1. Keyphrase length
2. Keyphrase in SEO title
3. Keyphrase in meta description
4. Keyphrase in slug
5. Keyphrase in introduction
6. Keyphrase density
7. Meta description length
8. SEO title width
9. Text length
10. Single H1 title
11. Keyphrase in subheadings
12. Internal links
13. Outbound links
14. Competing links
15. Images (alt text)
16. Keyphrase in image alt
17. Previously used keyphrase

---

## Readability Analysis Checks

1. Sentence length (>20 words)
2. Paragraph length (>150 words)
3. Subheading distribution
4. Transition words percentage
5. Passive voice percentage
6. Consecutive sentences (same starting word)
7. Word complexity (4+ syllables)
8. Flesch Reading Ease score

---

## AJAX Endpoints

### Existing
- `rankflow_seo_generate_meta` - Generate meta with AI
- `rankflow_seo_analyze_content` - Analyze content
- `rankflow_seo_calculate_score` - Calculate SEO score

### New
- `rankflow_seo_update_analysis` - Real-time analysis update (both SEO and Readability)

---

## Notes

- Tab badges show problem count
- Analysis updates in real-time as user types
- Accordions are collapsible
- All styles follow WordPress admin design patterns
- Mobile responsive design included