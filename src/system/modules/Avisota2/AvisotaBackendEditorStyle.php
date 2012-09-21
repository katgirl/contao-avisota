<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Avisota newsletter and mailing system
 * Copyright (C) 2010,2011,2012 Tristan Lins
 *
 * Extension for:
 * Contao Open Source CMS
 * Copyright (C) 2005-2012 Leo Feyer
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
 * @copyright  InfinitySoft 2010,2011,2012
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Avisota
 * @license    LGPL
 * @filesource
 */


/**
 * Class AvisotaBackendEditorStyle
 *
 * InsertTag hook class.
 * @copyright  InfinitySoft 2010,2011,2012
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Avisota
 */
class AvisotaBackendEditorStyle extends Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->import('Database');
		$this->import('Input');
	}


	public function hookGetEditorStylesLayout($strEditor)
	{
		if (	$strEditor == 'newsletter'
			&&	$this->Input->get('do') == 'avisota_newsletter'
			&&	$this->Input->get('table') == 'tl_avisota_newsletter_content'
			&&	$this->Input->get('act') == 'edit')
		{
			$strId = $this->Input->get('id');

			$objNewsletter = $this->Database->prepare("
					SELECT
						n.*
					FROM
						`tl_avisota_newsletter` n
					INNER JOIN
						`tl_avisota_newsletter_content` c
					ON
						n.`id`=c.`pid`
					WHERE
						c.`id`=?")
				->execute($strId);

			$objCategory = $this->Database->prepare("
					SELECT
						*
					FROM
						`tl_avisota_newsletter_category`
					WHERE
						`id`=?")
				->execute($objNewsletter->pid);

			if ($objCategory->viewOnlinePage > 0 && 0)
			{
				// the "view online" page does not contains the option to set a layout, use parent page instead
				$objViewOnlinePage = $this->Database->prepare("
						SELECT
							*
						FROM
							`tl_page`
						WHERE
							`id`=?")
					->execute($objCategory->viewOnlinePage);
				$objPage = $this->getPageDetails($objViewOnlinePage->pid);
			}
			elseif ($objCategory->subscriptionPage > 0)
			{
				$objPage = $this->getPageDetails($objCategory->subscriptionPage);
			}
			else
			{
				return false;
			}
			return $objPage->layout;
		}
		return false;
	}
}