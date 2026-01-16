<?php
/**
 * Admin notice partial.
 *
 * @package    RankFlow_SEO
 * @subpackage RankFlow_SEO/admin/partials
 *
 * @var string $rankflow_seo_notice_type        Notice type (success, error, warning, info).
 * @var string $rankflow_seo_notice_message     Notice message.
 * @var bool   $rankflow_seo_notice_dismissible Whether notice is dismissible.
 */

if (!defined('ABSPATH')) {
	exit;
}

$rankflow_seo_notice_type = isset($rankflow_seo_notice_type) ? $rankflow_seo_notice_type : 'info';
$rankflow_seo_notice_message = isset($rankflow_seo_notice_message) ? $rankflow_seo_notice_message : '';
$rankflow_seo_notice_dismissible = isset($rankflow_seo_notice_dismissible) ? $rankflow_seo_notice_dismissible : true;

$rankflow_seo_notice_classes = array('notice', 'notice-' . $rankflow_seo_notice_type);
if ($rankflow_seo_notice_dismissible) {
	$rankflow_seo_notice_classes[] = 'is-dismissible';
}
?>

<div class="<?php echo esc_attr(implode(' ', $rankflow_seo_notice_classes)); ?>">
	<p><?php echo wp_kses_post($rankflow_seo_notice_message); ?></p>
</div>