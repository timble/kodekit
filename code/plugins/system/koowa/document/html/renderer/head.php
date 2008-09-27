<?php
/**
 * @version     $Id$
 * @category	Koowa
 * @package     Koowa_Document
 * @copyright   Copyright (C) 2007 - 2008 Joomlatools. All rights reserved.
 * @license     GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link        http://www.koowa.org
 */

/**
 * Head renderer
 *
 * @author		Johan Janssens <johan@joomlatools.org>
 * @category	Koowa
 * @package		Koowa_Document
 * @subpackage	Html
 */
class KDocumentHtmlRendererHead extends KDocumentRenderer
{
	/**
	 * Renders the document head and returns the results as a string
	 *
	 * @param string 	$name		(unused)
	 * @param array 	$params		Associative array of values
	 * @return string	The output of the script
	 */
	public function render( $head, array $params = array(), $content = null )
	{
		ob_start();

		echo $this->renderHead($this->_doc);

		$contents = ob_get_contents();
		ob_end_clean();

		return $contents;
	}

	/**
	 * Generates the head html and return the results as a string
	 *
	 * @return string
	 */
	public function renderHead($document)
	{
		$strHtml = '';
		
		// Get the head data from the document
		$data = $document->getHeadData();

		// Generate base tag (need to happen first)
		$base = $document->getBase();
		if(!empty($base)) {
			$strHtml .= '	<base href="'.$document->getBase().'" />'.PHP_EOL;
		}

		// Generate META tags (needs to happen as early as possible in the head)
		foreach ($data['metaTags'] as $type => $tag)
		{
			foreach ($tag as $name => $content)
			{
				if ($type == 'http-equiv') {
					$strHtml .= '	<meta http-equiv="'.$name.'" content="'.$content.'" />'.PHP_EOL;
				} elseif ($type == 'standard') {
					$strHtml .= '	<meta name="'.$name.'" content="'.$content.'" />'.PHP_EOL;
				}
			}
		}

		$strHtml .= '	<meta name="description" content="'.$document->getDescription().'" />'.PHP_EOL;
		$strHtml .= '	<meta name="generator" content="'.$document->getGenerator().'" />'.PHP_EOL;

		$strHtml .= '	<title>'.htmlspecialchars($document->getTitle()).'</title>'.PHP_EOL;

		// Generate link declarations
		foreach ($data['links'] as $link) 
		{
			$attribs = KHelperArray::toString($link['attribs']);
			$strHtml .= '	<link href="'.$link['href'].'" '
				.$link['relType'].'="'.$link['relation'].'" '.$attribs.' />'.PHP_EOL;
		}
		
		// Generate style sheet links and declarations in FIFO order
		foreach ($data['styles'] as $style) 
		{
			switch($style['type']) 
			{
				case 'linked':
					$strHtml .= '	<link rel="stylesheet" href="'
							.$style['src'].'" type="'.$style['mime'].'"';
					if (!is_null($style['media'])){
						$strHtml .= ' media="'.$style['media'].'" ';
					}
					if ($temp = KHelperArray::toString($style['attribs'])) {
						$strHtml .= ' '.$temp;;
					}
					$strHtml .= ' />'.PHP_EOL;
					break;			
				case 'declared':
					$strHtml .= '	<style type="'.$style['mime'].'">'.PHP_EOL;

					// This is for full XHTML support.
					if ($document->getMimeEncoding() == 'text/html' ) {
						$strHtml .= '		<!--';
					} else {
						$strHtml .= '		<![CDATA[';
					}
		
					$strHtml .= $content;
		
					// See above note
					if ($document->getMimeEncoding() == 'text/html' ) {
						$strHtml .= '		-->'.PHP_EOL;
					} else {
						$strHtml .= '		]]>'.PHP_EOL;
					}
					$strHtml .= '	</style>'.PHP_EOL;
					break;
			}
		}

		// Generate script file links and declarations in FIFO order
		foreach ($data['scripts'] as $script) 
		{
			switch($script['type']) 
			{
				case 'linked':
					$strHtml .= '	<script type="'.$script['mime']
								.'" src="'.$script['src'].'"></script>'.PHP_EOL;
					break;			
				case 'declared':
					$strHtml .= '	<script type="'.$script['mime'].'">'.PHP_EOL;
	
					// This is for full XHTML support.
					if ($document->getMimeEncoding() != 'text/html' ) {
						$strHtml .= '		<![CDATA[';
					}
		
					$strHtml .= $script['content'];
		
					// See above note
					if ($document->getMimeEncoding() != 'text/html' ) {
						$strHtml .= '		// ]]>'.PHP_EOL;
					}
					$strHtml .= '	</script>'.PHP_EOL;
					break;
			}
		}


		foreach($data['custom'] as $custom) {
			$strHtml .= $custom;
		}

		return $strHtml;
	}
}