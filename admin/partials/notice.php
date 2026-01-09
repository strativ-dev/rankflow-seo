<?php
/**
 * Admin notice partial.
 *
 * @package    AI_SEO_Pro
 * @subpackage AI_SEO_Pro/admin/partials
 *
 * @var string $ai_seo_pro_notice_type        Notice type (success, error, warning, info).
 * @var string $ai_seo_pro_notice_message     Notice message.
 * @var bool   $ai_seo_pro_notice_dismissible Whether notice is dismissible.
 */

if (!defined('ABSPATH')) {
	exit;
}

$ai_seo_pro_notice_type = isset($ai_seo_pro_notice_type) ? $ai_seo_pro_notice_type : 'info';
$ai_seo_pro_notice_message = isset($ai_seo_pro_notice_message) ? $ai_seo_pro_notice_message : '';
$ai_seo_pro_notice_dismissible = isset($ai_seo_pro_notice_dismissible) ? $ai_seo_pro_notice_dismissible : true;

$ai_seo_pro_notice_classes = array('notice', 'notice-' . $ai_seo_pro_notice_type);
if ($ai_seo_pro_notice_dismissible) {
	$ai_seo_pro_notice_classes[] = 'is-dismissible';
}
?>

<div class="<?php echo esc_attr(implode(' ', $ai_seo_pro_notice_classes)); ?>">
	<p><?php echo wp_kses_post($ai_seo_pro_notice_message); ?></p>
</div>