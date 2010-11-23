<div id="tl_buttons">
<a href="<?php echo $this->getReferer(true) ?>" class="header_back" title="<?php echo specialchars($GLOBALS['TL_LANG']['MSC']['backBT']) ?>" accesskey="b"><?php echo $GLOBALS['TL_LANG']['MSC']['backBT'] ?></a>
</div>

<h2 class="sub_headline"><?php echo $GLOBALS['TL_LANG']['tl_avisota_newsletter']['headline'] ?></h2>

<div class="tl_formbody_edit">
<table class="prev_header" summary="" cellpadding="0" cellspacing="0">
  <tbody><tr class="row_0">
    <td class="col_0"><?php echo $GLOBALS['TL_LANG']['tl_avisota_newsletter']['from'] ?></td>
    <td class="col_1"><?php echo $this->from ?></td>
  </tr>
  <tr class="row_1">
    <td class="col_0"><?php echo $GLOBALS['TL_LANG']['tl_avisota_newsletter']['subject'][0] ?></td>
    <td class="col_1"><?php echo $this->subject ?></td>
  </tr>
  <tr class="row_2">
    <td class="col_0"><?php echo $GLOBALS['TL_LANG']['tl_avisota_newsletter']['template_html'][0] ?>&nbsp;</td>
    <td class="col_1"><?php echo $this->template_html ?></td>
  </tr>
  <tr class="row_3">
    <td class="col_0"><?php echo $GLOBALS['TL_LANG']['tl_avisota_newsletter']['template_plain'][0] ?>&nbsp;</td>
    <td class="col_1"><?php echo $this->template_plain ?></td>
  </tr>
</tbody></table>
</div>

<form action="contao/main.php" id="tl_avisota_newsletter_preview" target="preview" class="tl_form" method="get">
<input name="do" value="avisota_newsletter" type="hidden">
<input name="table" value="tl_avisota_newsletter" type="hidden">
<input name="key" value="preview" type="hidden">
<input name="id" value="<?php echo $this->id ?>" type="hidden">
<div class="tl_formbody_edit preview">

<div class="tl_tbox block">
<div class="w50">
  <h3><label for="ctrl_preview_mode"><?php echo $GLOBALS['TL_LANG']['tl_avisota_newsletter']['preview_mode'][0] ?></label></h3>
  <select name="mode" id="preview_mode" class="tl_select" onfocus="Backend.getScrollOffset();" onchange="this.form.submit();">
    <option value="<?php echo NL_HTML ?>"<?php if ($_SESSION['tl_avisota_preview_mode'] == NL_HTML): ?> selected="selected"<?php endif ?>><?php echo $GLOBALS['TL_LANG']['tl_avisota_newsletter']['preview_mode'][2] ?></option>
    <option value="<?php echo NL_PLAIN ?>"<?php if ($_SESSION['tl_avisota_preview_mode'] == NL_PLAIN): ?> selected="selected"<?php endif ?>><?php echo $GLOBALS['TL_LANG']['tl_avisota_newsletter']['preview_mode'][3] ?></option>
  </select>
  <p class="tl_help tl_tip"><?php echo $GLOBALS['TL_LANG']['tl_avisota_newsletter']['preview_mode'][1] ?></p>
</div>

<div class="w50">
  <h3><label for="ctrl_preview_personalized"><?php echo $GLOBALS['TL_LANG']['tl_avisota_newsletter']['preview_personalized'][0] ?></label></h3>
  <select name="personalized" id="preview_personalized" class="tl_select" onfocus="Backend.getScrollOffset();" onchange="this.form.submit();">
    <option value="no"><?php echo $GLOBALS['TL_LANG']['tl_avisota_newsletter']['preview_personalized'][2] ?></option>
    <option value="anonymous"<?php if ($_SESSION['tl_avisota_preview_personalized'] == 'anonymous'): ?> selected="selected"<?php endif ?>><?php echo $GLOBALS['TL_LANG']['tl_avisota_newsletter']['preview_personalized'][3] ?></option>
    <option value="private"<?php if ($_SESSION['tl_avisota_preview_personalized'] == 'private'): ?> selected="selected"<?php endif ?>><?php echo $GLOBALS['TL_LANG']['tl_avisota_newsletter']['preview_personalized'][4] ?></option>
  </select>
  <p class="tl_help tl_tip"><?php echo $GLOBALS['TL_LANG']['tl_avisota_newsletter']['preview_personalized'][1] ?></p>
</div>
</div>

<noscript>
<div class="tl_submit_container">
<input name="live" class="tl_submit" accesskey="l" value="<?php echo $GLOBALS['TL_LANG']['tl_avisota_newsletter']['live'] ?>" type="submit" />
</div>
</noscript>
<iframe class="tl_avisota_newsletter_preview" id="preview" name="preview" scrolling="auto" width="100%" height="600"
	src="contao/main.php?do=avisota_newsletter&amp;table=tl_avisota_newsletter&amp;key=preview&amp;id=1&amp;mode=<?php echo $_SESSION['tl_avisota_preview_mode'] ?>"></iframe>
</div>
</form>

<form action="contao/main.php" id="tl_avisota_newsletter_send" class="tl_form" method="get">
<input name="do" value="avisota_newsletter" type="hidden">
<input name="table" value="tl_avisota_newsletter" type="hidden">
<input name="key" value="send" type="hidden">
<input name="id" value="<?php echo $this->id ?>" type="hidden">
<div class="tl_formbody_edit">

<div class="tl_tbox block">
<div class="w50">
  <h3><label for="ctrl_recipient"><?php echo $GLOBALS['TL_LANG']['tl_avisota_newsletter']['sendPreviewTo'][0] ?></label></h3>
  <input name="recipient" id="ctrl_recipient" value="<?php echo $this->User->email ?>" class="tl_text" onfocus="Backend.getScrollOffset();" type="text">
  <p class="tl_help tl_tip"><?php echo $GLOBALS['TL_LANG']['tl_avisota_newsletter']['sendPreviewTo'][1] ?></p>
</div>
</div>
</div>

<div class="tl_formbody_submit">

<div class="tl_submit_container">
<input name="preview" class="tl_submit" accesskey="p" value="<?php echo $GLOBALS['TL_LANG']['tl_avisota_newsletter']['preview'] ?>" type="submit" />
<input style="float:right" id="send" class="tl_submit" accesskey="s" value="<?php echo $GLOBALS['TL_LANG']['tl_avisota_newsletter']['headline'] ?>" onclick="return confirm('<?php echo specialchars($GLOBALS['TL_LANG']['tl_avisota_newsletter']['sendConfirm']) ?>')" type="submit" />
</div>

</div>
</form>