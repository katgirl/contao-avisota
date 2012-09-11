<?php if (defined('AVISOTA_TRANSPORT')): echo $GLOBALS['TL_LANG']['tl_avisota_newsletter']['online'] . "\n" ?>
[{{newsletter::href}}]
<?php endif; ?>

<?php echo $this->body; ?>

<?php if (!isset($GLOBALS['objPage'])): ?>--------------------------------------------------------------------------------
{{newsletter::unsubscribe::plain}}
<?php endif; ?>
