# Language Support Integration Guide

## Files Created

1. `languages/ai-seo-pro.pot` - Translation template file
2. `includes/core/class-readability.php` - Fixed with translator comments

## Setup Instructions

### Step 1: Create Languages Folder

Create a `languages` folder in your plugin root:
```
ai-seo-pro/
├── languages/
│   └── ai-seo-pro.pot
├── includes/
├── admin/
└── ...
```

### Step 2: Add Textdomain Loading

In your main plugin class (`includes/class-ai-seo-pro.php`), add this method:

```php
/**
 * Load plugin textdomain for translations.
 *
 * @since 1.0.0
 */
public function load_textdomain() {
    load_plugin_textdomain(
        'ai-seo-pro',
        false,
        dirname( AI_SEO_PRO_PLUGIN_BASENAME ) . '/languages/'
    );
}
```

### Step 3: Hook the Textdomain Loading

In the `define_hooks()` or similar method, add:

```php
// Load translations
add_action( 'init', array( $this, 'load_textdomain' ) );
```

Or in your main plugin file (`ai-seo-pro.php`), add this function:

```php
/**
 * Load plugin textdomain.
 */
function ai_seo_pro_load_textdomain() {
    load_plugin_textdomain(
        'ai-seo-pro',
        false,
        dirname( plugin_basename( __FILE__ ) ) . '/languages/'
    );
}
add_action( 'plugins_loaded', 'ai_seo_pro_load_textdomain' );
```

## What Was Fixed

### class-readability.php - Lines 261, 272, 283

**Before (Error):**
```php
'message' => sprintf(
    __( 'Readability score is %.1f. Consider using shorter sentences and simpler words.', 'ai-seo-pro' ),
    $analysis['flesch_score']
),
```

**After (Fixed):**
```php
/* translators: %s: readability score number */
'message' => sprintf(
    __( 'Readability score is %s. Consider using shorter sentences and simpler words.', 'ai-seo-pro' ),
    number_format( $analysis['flesch_score'], 1 )
),
```

### Changes Made:

1. Added `/* translators: ... */` comment before each string with placeholders
2. Changed `%.1f` to `%s` and used `number_format()` for better internationalization
3. The translator comment explains what each placeholder represents

## Translator Comment Rules

When using `sprintf()` with `__()`, always add a comment:

```php
// Single placeholder
/* translators: %s: item name */
sprintf( __( 'Item: %s', 'ai-seo-pro' ), $name );

// Multiple placeholders
/* translators: 1: user name, 2: date */
sprintf( __( 'Posted by %1$s on %2$s', 'ai-seo-pro' ), $user, $date );

// Number placeholder
/* translators: %d: number of items */
sprintf( __( '%d items found', 'ai-seo-pro' ), $count );
```

## Generating Translation Files

To generate/update the .pot file, use WP-CLI:

```bash
wp i18n make-pot . languages/ai-seo-pro.pot --domain=ai-seo-pro
```

Or use the POEdit application to scan your plugin files.

## Testing Translations

1. Create a `.po` file for your language (e.g., `ai-seo-pro-es_ES.po`)
2. Translate the strings
3. Compile to `.mo` file
4. Place in `languages/` folder
5. Change WordPress language to test

## File Placement

```
ai-seo-pro/
├── languages/
│   ├── ai-seo-pro.pot          (template - required)
│   ├── ai-seo-pro-es_ES.po     (Spanish translations)
│   ├── ai-seo-pro-es_ES.mo     (compiled Spanish)
│   ├── ai-seo-pro-de_DE.po     (German translations)
│   └── ai-seo-pro-de_DE.mo     (compiled German)
```
