<?php
/**
 * Admin notice partial.
 *
 * @package    MPSEO
 * @subpackage MPSEO/admin/partials
 *
 * @var string $mpseo_notice_type        Notice type (success, error, warning, info).
 * @var string $mpseo_notice_message     Notice message.
 * @var bool   $mpseo_notice_dismissible Whether notice is dismissible.
 */

if (!defined('ABSPATH')) {
	exit;
}

$mpseo_notice_type = isset($mpseo_notice_type) ? $mpseo_notice_type : 'info';
$mpseo_notice_message = isset($mpseo_notice_message) ? $mpseo_notice_message : '';
$mpseo_notice_dismissible = isset($mpseo_notice_dismissible) ? $mpseo_notice_dismissible : true;

$mpseo_notice_classes = array('notice', 'notice-' . $mpseo_notice_type);
if ($mpseo_notice_dismissible) {
	$mpseo_notice_classes[] = 'is-dismissible';
}
?>

<div class="<?php echo esc_attr(implode(' ', $mpseo_notice_classes)); ?>">
	<p><?php echo wp_kses_post($mpseo_notice_message); ?></p>
</div>