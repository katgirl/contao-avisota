<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  InfinitySoft 2010
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Avisota
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */


/**
 * Class Avisota
 *
 * Parent class for newsletter content elements.
 * @copyright  InfinitySoft 2010
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Avisota
 */
class Avisota extends Backend
{

	public function importRecipients()
	{
		return 'importRecipients';
	}
	
	
	/**
	 * Generate and print out the preview.
	 */
	public function preview()
	{
		$this->import('BackendUser', 'User');
		
		// get preview mode
		$mode = $this->Input->get('mode');
		if (!$mode)
		{
			$mode = NL_HTML;
		}
		$_SESSION['tl_avisota_preview_mode'] = $mode;
		
		// get personalized state
		if ($this->Input->get('personalized'))
		{
			$_SESSION['tl_avisota_preview_personalized'] = $personalized = ($this->Input->get('personalized') == 'no' ? '' : $this->Input->get('personalized'));
		}
		else
		{
			$personalized = $_SESSION['tl_avisota_preview_personalized'];
		}
		
		// find the newsletter
		$intId = $this->Input->get('id');
		
		$objNewsletter = $this->Database->prepare("
				SELECT
					*
				FROM
					`tl_avisota_newsletter`
				WHERE
					`id`=?")
			->execute($intId);
		
		if (!$objNewsletter->next())
		{
			$this->redirect('contao/main.php?act=tl_error');
		}
		
		// find the newsletter category
		$objCategory = $this->Database->prepare("
				SELECT
					*
				FROM
					`tl_avisota_newsletter_category`
				WHERE
					`id`=?")
			->execute($objNewsletter->pid);
		
		if (!$objCategory->next())
		{
			$this->redirect('contao/main.php?act=tl_error');
		}
		
		// build the recipient data array
		$arrRecipient = $this->getPreviewRecipient($mode, $personalized);
		
		// generate the preview
		switch ($mode)
		{
		case NL_HTML:
			header('Content-Type: text/html; charset=utf-8');
			echo $this->generateHtml($objNewsletter, $objCategory, $arrRecipient, $personalized);
			exit(0);
			
		case NL_PLAIN:
			header('Content-Type: text/plain; charset=utf-8');
			echo $this->generatePlain($objNewsletter, $objCategory, $arrRecipient, $personalized);
			exit(0);
		}
		return 'preview';
	}

	
	/**
	 * Show preview and send the Newsletter.
	 * 
	 * @return string
	 */
	public function send()
	{
		$intId = $this->Input->get('id');
		
		$objNewsletter = $this->Database->prepare("
				SELECT
					*
				FROM
					`tl_avisota_newsletter`
				WHERE
					`id`=?")
			->execute($intId);
		
		if (!$objNewsletter->next())
		{
			$this->redirect('contao/main.php?do=avisota_newsletter');
		}
		
		$objCategory = $this->Database->prepare("
				SELECT
					*
				FROM
					`tl_avisota_newsletter_category`
				WHERE
					`id`=?")
			->execute($objNewsletter->pid);
		
		if (!$objCategory->next())
		{
			$this->redirect('contao/main.php?do=avisota_newsletter');
		}
		
		$objTemplate = new BackendTemplate('be_avisota_send');
		$objTemplate->import('BackendUser', 'User');
		
		// add category data to template
		$objTemplate->setData($objCategory->row());
		
		// add newsletter data to template
		$objTemplate->setData($objNewsletter->row());
		
		// build from
		$strFrom = '';
		if ($objCategory->sender)
		{
			$strFrom = $objCategory->sender;
		}
		else
		{
			$strFrom = $GLOBALS['TL_CONFIG']['adminEmail'];
		}
		if ($objCategory->senderName)
		{
			$strFrom = sprintf('%s <%s>', $objCategory->senderName, $strFrom);
		}
		$objTemplate->from = $strFrom;
		
		return $objTemplate->parse();
	}
	
	
	/**
	 * Generate the newsletter content.
	 * 
	 * @param Database_Result $objNewsletter
	 * @param Database_Result $objCategory
	 * @param array $arrRecipient
	 * @param string $personalized
	 * @param string $mode
	 * @return string
	 */
	protected function generateContent(Database_Result &$objNewsletter, Database_Result &$objCategory, $arrRecipient, $personalized, $mode)
	{
		$strContent = '';
		
		$objContent = $this->Database->prepare("
				SELECT
					*
				FROM
					`tl_avisota_newsletter_content`
				WHERE
						`pid`=?
					AND `invisible`=''
				ORDER BY
					`sorting`")
			->execute($objNewsletter->id);
		
		while ($objContent->next())
		{
			$strContent .= $this->generateNewsletterElement($objContent, $mode, $arrRecipient, $personalized);
		}
		
		return $strContent;
	}
	
	
	/**
	 * Generate the html newsletter.
	 * 
	 * @param Database_Result $objNewsletter
	 * @param Database_Result $objCategory
	 * @param array $arrRecipient
	 * @param string $personalized
	 * @return string
	 */
	protected function generateHtml(Database_Result &$objNewsletter, Database_Result &$objCategory, $arrRecipient, $personalized)
	{
		$objTemplate = new FrontendTemplate($objNewsletter->template_html);
		$objTemplate->body = $this->generateContent($objNewsletter, $objCategory, $arrRecipient, $personalized, NL_HTML);
		return $this->replaceNewsletterInsertTags($objTemplate->parse(), $arrRecipient);
	}
	
	
	/**
	 * Generate the plain text newsletter.
	 * 
	 * @param Database_Result $objNewsletter
	 * @param Database_Result $objCategory
	 * @param array $arrRecipient
	 * @param string $personalized
	 * @return string
	 */
	protected function generatePlain(Database_Result &$objNewsletter, Database_Result &$objCategory, $arrRecipient, $personalized)
	{
		$objTemplate = new FrontendTemplate($objNewsletter->template_plain);
		$objTemplate->body = $this->generateContent($objNewsletter, $objCategory, $arrRecipient, $personalized, NL_PLAIN);
		return $this->replaceNewsletterInsertTags($objTemplate->parse(), $arrRecipient);
	}
	
	
	/**
	 * Replace the recipient insert tags and invoke the Controller::replaceInsertTags.
	 * 
	 * @param string $strBuffer
	 * @param array $arrRecipient
	 * @return string
	 */
	protected function replaceNewsletterInsertTags($strBuffer, $arrRecipient)
	{
		$tags = preg_split('/{{recipient::([^}]+)}}/', $strBuffer, -1, PREG_SPLIT_DELIM_CAPTURE);
		
		$strBuffer = '';
		$arrCache = array();
		
		for($_rit=0; $_rit<count($tags); $_rit=$_rit+2)
		{
			$strBuffer .= $tags[$_rit];
			$strKey = $tags[$_rit+1];
 
			if (isset($arrRecipient[strtolower($strKey)]))
			{
				$strBuffer .= $arrRecipient[strtolower($strKey)];
			}
		}
		
		$strBuffer = $this->replaceInsertTags($strBuffer);
		return $strBuffer;
	}
	
	
	/**
	 * Generate a content element return it as plain text string
	 * @param integer
	 * @return string
	 */
	public function getNewsletterElement($intId, $mode = NL_HTML)
	{
		if (!strlen($intId) || $intId < 1)
		{
			return '';
		}

		$this->import('Database');

		$objElement = $this->Database->prepare("
				SELECT
					*
				FROM
					tl_avisota_newsletter_content
				WHERE
					id=?")
			->limit(1)
			->execute($intId);

		if ($objElement->numRows < 1)
		{
			return '';
		}
		
		$strBuffer = $this->generateNewsletterElement($objElement, $mode, $this->getPreviewRecipient($mode, $objElement->personalize), $objElement->personalize);
		$strBuffer = $this->replaceNewsletterInsertTags($strBuffer, $arrRecipient);
		return $strBuffer;
	}

	
	/**
	 * Generate a content element return it as plain text string
	 * @param integer
	 * @return string
	 */
	public function generateNewsletterElement($objElement, $mode = NL_HTML, $arrRecipient = null, $personalized = '')
	{
		if ($objElement->personalize == 'private' && $personalized != 'private')
		{
			return '';
		}
		
		$strClass = $this->findNewsletterElement($objElement->type);

		// Return if the class does not exist
		if (!$this->classFileExists($strClass))
		{
			$this->log('Newsletter content element class "'.$strClass.'" (newsletter content element "'.$objElement->type.'") does not exist', 'Avisota getNewsletterElement()', TL_ERROR);
			return '';
		}

		$objElement->typePrefix = 'nle_';
		$objElement = new $strClass($objElement);
		switch ($mode)
		{
		case NL_HTML:
			$strBuffer = $objElement->generateHTML();
			break;
		
		case NL_PLAIN:
			$strBuffer = $objElement->generatePlain();
			break;
		}
		
		// HOOK: add custom logic
		if (isset($GLOBALS['TL_HOOKS']['getNewsletterElement']) && is_array($GLOBALS['TL_HOOKS']['getNewsletterElement']))
		{
			foreach ($GLOBALS['TL_HOOKS']['getNewsletterElement'] as $callback)
			{
				$this->import($callback[0]);
				$strBuffer = $this->$callback[0]->$callback[1]($objElement, $strBuffer, $mode);
			}
		}
		
		return $strBuffer;
	}
	
	
	/**
	 * Find a newsletter content element in the TL_NLE array and return its value
	 * @param string
	 * @return mixed
	 */
	protected function findNewsletterElement($strName)
	{
		foreach ($GLOBALS['TL_NLE'] as $v)
		{
			foreach ($v as $kk=>$vv)
			{
				if ($kk == $strName)
				{
					return $vv;
				}
			}
		}

		return '';
	}

	
	/**
	 * Get a dummy recipient array.
	 */
	public function getPreviewRecipient($mode, $personalized)
	{
		$arrRecipient = array();
		if ($personalized == 'anonymous')
		{
			$arrRecipient = $GLOBALS['TL_LANG']['tl_avisota_newsletter']['anonymous'];
			$arrRecipient['email'] = $this->User->email;
		}
		elseif ($personalized == 'private')
		{
			$arrRecipient = $this->User->getData();
			
			$objMember = $this->Database->prepare("
					SELECT
						*
					FROM
						`tl_member`
					WHERE
						`email`=?")
				->execute($this->User->email);
			if ($objMember->next())
			{
				$arrRecipient = array_merge
				(
					$arrRecipient,
					$objMember->row()
				);
			}
			else
			{
				$arrName = explode(' ', $arrRecipient['name'], 2);
				$arrRecipient['first_name'] = $arrName[0];
				$arrRecipient['last_name'] = $arrName[1];
			}
		}
		else
		{
			$arrRecipient = array
			(
				'email' => $this->User->email
			);
		}
		
		// add the salutation
		if (isset($GLOBALS['TL_LANG']['tl_avisota_newsletter']['salutation_' . $arrRecipient['gender']]))
		{
			$arrRecipient['salutation'] = $GLOBALS['TL_LANG']['tl_avisota_newsletter']['salutation_' . $arrRecipient['gender']];
		}
		else
		{
			$arrRecipient['salutation'] = $GLOBALS['TL_LANG']['tl_avisota_newsletter']['salutation'];
		}
		
		// add the unsubscribe url
		// TODO unsubscribe_url
		$arrRecipient['unsubscribe_url'] = 'http://unsubscribe.me/?email=' . $arrRecipient['email'] . '&category=' . $objCategory->alias;
		switch ($mode)
		{
		case NL_HTML:
			$arrRecipient['unsubscribe'] = sprintf('<a href="%s">%s</a>', $arrRecipient['unsubscribe_url'], $GLOBALS['TL_LANG']['tl_avisota_newsletter']['unsubscribe']);
			break;
		
		case NL_PLAIN:
			$arrRecipient['unsubscribe'] = sprintf("%s\n[%s]", $GLOBALS['TL_LANG']['tl_avisota_newsletter']['unsubscribe'], $arrRecipient['unsubscribe_url']);
			break;
		}
		
		return $arrRecipient;
	}
}

?>