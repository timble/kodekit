<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */


/**
 * Grid Template Helper
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa
 */
class KTemplateHelperGrid extends KTemplateHelperAbstract
{
    /**
     * Render a radio field
     *
     * @param 	array 	$config An optional array with configuration options
     * @return	string	Html
     */
    public function radio($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'entity'  		=> null,
            'attribs' => array()
        ));

        if($config->entity->isLockable() && $config->entity->isLocked())
        {
            $html = $this->getTemplate()->renderHelper('behavior.tooltip');
            $html .= '<span class="koowa-tooltip koowa_icon--locked"
                           title="'.$this->getTemplate()->renderHelper('grid.lock_message', array('entity' => $config->entity)).'">
					</span>';
        }
        else
        {
            $column = $config->entity->getIdentityColumn();
            $value  = $this->escape($config->entity->{$column});

            $attribs = $this->buildAttributes($config->attribs);

            $html = '<input type="radio" class="-koowa-grid-checkbox" name="%s[]" value="%s" %s />';
            $html = sprintf($html, $column, $value, $attribs);
        }

        return $html;
    }
    /**
     * Render a checkbox field
     *
     * @param 	array 	$config An optional array with configuration options
     * @return	string	Html
     */
    public function checkbox($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'entity'  		=> null,
            'attribs' => array()
        ))->append(array(
            'column' => $config->entity->getIdentityKey()
        ));

        if($config->entity->isLockable() && $config->entity->isLocked())
        {
            $html = $this->getTemplate()->renderHelper('behavior.tooltip');
            $html .= '<span class="koowa-tooltip koowa_icon--locked"
                           title="'.$this->getTemplate()->renderHelper('grid.lock_message', array('entity' => $config->entity)).'">
					</span>';
        }
        else
        {
            $column = $config->column;
            $value  = $this->escape($config->entity->{$column});

            $attribs = $this->buildAttributes($config->attribs);


            $html = '<input type="checkbox" class="-koowa-grid-checkbox" name="%s[]" value="%s" %s />';
            $html = sprintf($html, $column, $value, $attribs);
        }

        return $html;
    }

    /**
     * Render a search box
     *
     * @param 	array 	$config An optional array with configuration options
     * @return	string	Html
     */
    public function search($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'search'          => null,
            'submit_on_clear' => true,
            'placeholder'     => $this->translate('Find by title or description&hellip;')
        ));

        $html = '';

        if ($config->submit_on_clear)
        {
            $html .= $this->getTemplate()->renderHelper('behavior.jquery');
            $html .= '
            <script>
            (function() {
            var value = '.json_encode($config->search).',
                send = function(event) {
                    if (event.which === 13 || event.type === "blur") {
                        var form = kQuery(this).parents("form");
                        if (form.length) {
                            form[0].submit();
                        }
                    }
                };

            kQuery(function($) {
                $(".search_button").keypress(send).blur(send);
                $(".search_button--empty").click(function(event) {
                    event.preventDefault();

                    var input = $(this).siblings("input");
                    input.val("");

                    if (value) {
                        var form = input.parents("form");
                        if (form.length) {
                            form[0].submit();
                        }
                    }
                });
            });
            })();
            </script>';
        }

        $html .= '<div class="search__container search__container--has_empty_button">';
        $html .= '<label for="search"><i class="icon-search"></i></label>';
        $html .= '<input type="search" name="search" class="search_button" placeholder="'.$config->placeholder.'" value="'.$this->escape($config->search).'" />';
        $html .= '<a class="search_button--empty"><span>X</span></a>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Render a checkall header
     *
     * @param 	array 	$config An optional array with configuration options
     * @return	string	Html
     */
    public function checkall($config = array())
    {
        $html = '<input type="checkbox" class="-koowa-grid-checkall" />';
        return $html;
    }

    /**
     * Render a sorting header
     *
     * @param 	array 	$config An optional array with configuration options
     * @return	string	Html
     */
    public function sort($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'title'   	=> '',
            'column'  	=> '',
            'direction' => 'asc',
            'sort'		=> ''
        ));


        //Set the title
        if(empty($config->title)) {
            $config->title = ucfirst($config->column);
        }

        //Set the direction
        $direction	= strtolower($config->direction);
        $direction 	= in_array($direction, array('asc', 'desc')) ? $direction : 'asc';

        //Set the class
        $class = '';
        if($config->column == $config->sort)
        {
            $direction = $direction == 'desc' ? 'asc' : 'desc'; // toggle
            $class = 'class="-koowa-'.$direction.'"';
        }

        $url = clone $this->getTemplate()->getView()->getUrl();

        $query 				= $url->getQuery(1);
        $query['sort'] 		= $config->column;
        $query['direction'] = $direction;
        $url->setQuery($query);

        $html  = '<a href="'.$url.'" title="'.$this->translate('Click to sort by this column').'"  '.$class.'>';
        $html .= $this->translate($config->title);

        // Mark the current column
        if ($config->column == $config->sort)
        {
            if (strtolower($config->direction) === 'asc') {
                $html .= ' <span class="koowa_icon--sort_up koowa_icon--12"></span>';
            } else {
                $html .= ' <span class="koowa_icon--sort_down koowa_icon--12"></span>';
            }
        }
        else $html .= ' <span class="koowa_icon--sort koowa_icon--12"></span>';

        $html .= '</a>';

        return $html;
    }

    /**
     * Render an enable field
     *
     * @param 	array 	$config An optional array with configuration options
     * @return	string	Html
     */
    public function enable($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'entity'  		=> null,
            'field'		=> 'enabled',
            'clickable' => true
        ))->append(array(
            'enabled'   => (bool) $config->entity->{$config->field},
            'data'		=> array($config->field => $config->entity->{$config->field} ? 0 : 1),
        ))->append(array(
            'alt'       => $config->enabled ? $this->translate('Enabled') : $this->translate('Disabled'),
            'tooltip'   => $config->enabled ? $this->translate('Disable Item') : $this->translate('Enable Item'),
            'color'     => $config->enabled ? '#468847' : '#b94a48',
            'icon'      => $config->enabled ? 'enabled' : 'disabled',
        ));

        if ($config->clickable)
        {
            $data    = htmlentities(json_encode($config->data->toArray()));
            $attribs = 'style="cursor: pointer;color:'.$config->color.'" data-action="edit" data-data="'.$data.'"
                title="'.$config->tooltip.'"';
        }
        else $attribs = 'style="color:'.$config->color.'"';

        $html = '<span class="koowa-tooltip koowa_icon--%s" %s><i>%s</i></span>';
        $html = sprintf($html, $config->icon, $attribs, $config->alt);
        $html .= $this->getTemplate()->renderHelper('behavior.tooltip');

        return $html;
    }

    /**
     * Render a publish field
     *
     * @param 	array 	$config An optional array with configuration options
     * @return	string	Html
     */
    public function publish($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'entity'  		=> null,
            'field'		=> 'enabled',
            'clickable' => true
        ))->append(array(
            'enabled'   => (bool) $config->entity->{$config->field},
        ))->append(array(
            'alt'       => $config->enabled ? $this->translate('Published') : $this->translate('Unpublished'),
            'tooltip'   => $config->enabled ? $this->translate('Unpublish Item') : $this->translate('Publish Item'),
            'color'     => $config->enabled ? '#468847' : '#b94a48',
            'icon'      => $config->enabled ? 'enabled' : 'disabled',
        ));

        return $this->enable($config);
    }

    /**
     * Get the locked information
     *
     * @param  array|KObjectConfig $config An optional configuration array.
     * @throws UnexpectedValueException
     * @return string The locked by "name" "date" message
     */
    public function lock_message($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'entity' => null
        ));

        if (!($config->entity instanceof KModelEntityInterface)) {
            throw new UnexpectedValueException('$config->entity should be a KModelEntityInterface instance');
        }

        $entity = $config->entity;
        $message = '';

        if($entity->isLockable() && $entity->isLocked())
        {
            $user = $this->getObject('user.provider')->load($entity->locked_by);
            $date = $this->getObject('date', array('date' => $entity->locked_on));

            $message = $this->getObject('translator')->translate(
                'Locked by {name} {date}', array('name' => $user->getName(), 'date' => $date->humanize())
            );
        }

        return $message;
    }
}
