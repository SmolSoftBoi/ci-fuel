<?php if (isset($keywords)): ?>
	<?php if (is_array($keywords)): ?>
		<meta name="keywords" content="<?= implode(', ', $keywords) ?>">
	<?php else: ?>
		<meta name="keywords" content="<?= $keywords ?>">
	<?php endif; ?>
<?php endif; ?>