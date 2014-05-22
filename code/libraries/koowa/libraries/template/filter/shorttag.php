<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Shorttag Template Filter
 *
 * Filter for short_open_tags support
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Template
 */
class KTemplateFilterShorttag extends KTemplateFilterAbstract implements KTemplateFilterCompiler
{
    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  KObjectConfig $config An optional ObjectConfig object with configuration options
     * @return void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'priority' => self::PRIORITY_HIGH,
        ));

        parent::_initialize($config);
    }

    /**
	 * Convert <?= ?> to long-form <?php echo ?> when needed
	 *
	 * @param string
	 * @return KTemplateFilterShorttag
	 */
	public function compile(&$text)
	{
        if (!ini_get('short_open_tag'))
        {
           /**
         	* We could also convert <%= like the real T_OPEN_TAG_WITH_ECHO
         	* but that's not necessary.
         	*
         	* It might be nice to also convert PHP code blocks <? ?> but
         	* let's quit while we're ahead.  It's probably better to keep
         	* the <?php for larger code blocks but that's your choice.  If
          	* you do go for it, explicitly check for <?xml as this will
         	* probably be the biggest headache.
         	*/

        	// convert "<?=" to "<?php echo"
       	 	$find = '/\<\?\s*=\s*(.*?)/';
        	$replace = "<?php echo \$1";
        	$text = preg_replace($find, $replace, $text);

        	// convert "<?" to "<?php"
        	$find = '/\<\?(?:php)?\s*(.*?)/';
        	$replace = "<?php \$1";
        	$text = preg_replace($find, $replace, $text);
        }

        return $this;
	}
}
