<?php if (defined('AVISOTA_TRANSPORT')): echo $GLOBALS['TL_LANG']['tl_avisota_newsletter']['online'] . "\n" ?>
[{{newsletter::href}}]
<?php endif; ?>

<?php if ($this->header): echo $this->header; ?>

<?php endif; ?>
<?php echo $this->body; ?>

<?php if ($this->left): echo $this->left; ?>

<?php endif; ?>
<?php if ($this->right): echo $this->right; ?>

<?php endif; ?>
<?php if ($this->footer): echo $this->footer; ?>

<?php endif; ?>

<?php if (!isset($GLOBALS['objPage'])): ?>--------------------------------------------------------------------------------
{{newsletter::unsubscribe::plain}}
<?php endif; ?>
