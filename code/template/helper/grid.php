<?php
/**
 * Kodekit - http://timble.net/kodekit
 *
 * @copyright   Copyright (C) 2007 - 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     MPL v2.0 <https://www.mozilla.org/en-US/MPL/2.0>
 * @link        https://github.com/timble/kodekit for the canonical source repository
 */

namespace Kodekit\Library;

/**
 * Grid Template Helper
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Template\Helper
 */
class TemplateHelperGrid extends TemplateHelperAbstract implements TemplateHelperParameterizable
{
    /**
     * Render a radio field
     *
     * @param   array   $config An optional array with configuration options
     * @return  string  Html
     */
    public function radio($config = array())
    {
        $config = new ObjectConfigJson($config);
        $config->append(array(
            'entity'  => null,
            'attribs' => array()
        ));

        if($config->entity->isLockable() && $config->entity->isLocked())
        {
            $html = $this->createHelper('behavior')->tooltip();
            $html .= '<span class="k-icon-lock-locked"
                            data-k-tooltip
                            title="'.$this->creaateHelper('grid')->lock_message(array('entity' => $config->entity)).'">
                    </span>';
        }
        else
        {
            $column = $config->entity->getIdentityColumn();
            $value  = StringEscaper::attr($config->entity->{$column});

            $attribs = $this->buildAttributes($config->attribs);

            $html = '<input type="radio" class="k-js-grid-checkbox" name="%s[]" value="%s" %s />';
            $html = sprintf($html, $column, $value, $attribs);
        }

        return $html;
    }
    /**
     * Render a checkbox field
     *
     * @param array $config An optional array with configuration options
     * @return  string  Html
     */
    public function checkbox($config = array())
    {
        $config = new ObjectConfigJson($config);
        $config->append(array(
            'entity'  => null,
            'attribs' => array()
        ))->append(array(
            'column' => $config->entity->getIdentityKey()
        ));

        if($config->entity->isLockable() && $config->entity->isLocked())
        {
            $html = $this->createHelper('behavior')->tooltip();
            $html .= '<span class="k-icon-lock-locked"
                            data-k-tooltip
                            title="'.$this->createHelper('grid')->lock_message(array('entity' => $config->entity)).'">
                    </span>';
        }
        else
        {
            $column = $config->column;
            $value  = StringEscaper::attr($config->entity->{$column});

            $attribs = $this->buildAttributes($config->attribs);

            $html = '<input type="checkbox" class="k-js-grid-checkbox" name="%s[]" value="%s" %s />';
            $html = sprintf($html, $column, $value, $attribs);
        }

        return $html;
    }

    /**
     * Render a search box
     *
     * @param   array   $config An optional array with configuration options
     * @return  string  Html
     */
    public function search($config = array())
    {
        $config = new ObjectConfigJson($config);
        $config->append(array(
            'search'          => null,
            'submit_on_clear' => true,
            'placeholder'     => $this->getObject('translator')->translate('Find by title or description&hellip;')
        ));

        $html = '';

        if ($config->submit_on_clear)
        {
            $html .= $this->createHelper('behavior')->jquery();
            $html .= '
            <script>
            (function() {
            var value = '.json_encode($config->search).',
                submitForm = function(form) {
                    if (form.length) {
                        var controller = form.data("controller");
                        if (typeof controller === "object" && controller !== null
                                && controller.grid && typeof controller.grid.uncheckAll !== "undefined") {
                            controller.grid.uncheckAll();
                        }

                        form[0].submit();
                    }
                },
                send = function(event) {
                    var v = kQuery(this).val();

                    if (v) {
                        kQuery(".k-search__empty").addClass("k-is-visible");
                    } else {
                        kQuery(".k-search__empty").removeClass("k-is-visible");
                    }

                    if (event.which === 13 || (event.type === "blur" && (v || value) && v != value)) {
                        event.preventDefault();
                        submitForm(kQuery(this).parents("form"));
                    }
                };

            kQuery(function($) {
                $(".k-search__field").keypress(send).blur(send);
                var empty_button = $(".k-search__empty");
                
                if (value) {
                    empty_button.addClass("k-is-visible");
                }
                
                empty_button.click(function(event) {
                    event.preventDefault();

                    var input = $(this).siblings("input");
                    input.val("");

                    if (value) {
                        submitForm(input.parents("form"));
                    }
                });
            });
            })();
            </script>';
        }

        $html .= '<div class="k-search k-search--has-both-buttons">';
        $html .= '<label for="k-search-input">' . $this->getObject('translator')->translate('Search') . '</label>';
        $html .= '<input id="k-search-input" type="search" name="search" class="k-search__field" placeholder="'.$config->placeholder.'" value="'.StringEscaper::attr($config->search).'" />';
        $html .= '<button type="submit" class="k-search__submit">';
        $html .= '<span class="k-icon-magnifying-glass" aria-hidden="true"></span>';
        $html .= '<span class="k-visually-hidden">' . $this->getObject('translator')->translate('Search') . '</span>';
        $html .= '</button>';
        $html .= '<button type="button" class="k-search__empty">';
        $html .= '<span class="k-search__empty-area">';
        $html .= '<span class="k-icon-x" aria-hidden="true"></span>';
        $html .= '<span class="k-visually-hidden">' . $this->getObject('translator')->translate('Clear search') . '</span>';
        $html .= '</span>';
        $html .= '</button>';

        if ($config->search) {
            $html .= '<div class="k-scopebar__item-label k-scopebar__item-label--numberless"></div>';
        }

        $html .= '</div>';



        return $html;
    }

    /**
     * Render a checkall header
     *
     * @param   array   $config An optional array with configuration options
     * @return  string  Html
     */
    public function checkall($config = array())
    {
        $html = '<input type="checkbox" class="k-js-grid-checkall" />';
        return $html;
    }

    /**
     * Render a sorting header
     *
     * @param   array   $config An optional array with configuration options
     * @return  string  Html
     */
    public function sort($config = array())
    {
        $config = new ObjectConfigJson($config);
        $config->append(array(
            'title'     => '',
            'column'    => '',
            'direction' => 'asc',
            'sort'      => '',
            'url'       => null
        ));

        $translator = $this->getObject('translator');

        //Set the title
        if(empty($config->title)) {
            $config->title = ucfirst($config->column);
        }

        //Set the direction
        $direction  = strtolower($config->direction);
        $direction  = in_array($direction, array('asc', 'desc')) ? $direction : 'asc';

        if($config->column == $config->sort)
        {
            $direction = $direction == 'desc' ? 'asc' : 'desc'; // toggle
        }

        //Set the query in the route
        if(!$config->url instanceof HttpUrlInterface) {
            $config->url = HttpUrl::fromString($config->url);
        }

        $config->url->query['sort']      = $config->column;
        $config->url->query['direction'] = $direction;

        $html  = '<a href="'.$config->url.'" data-k-tooltip=\'{"container":".k-ui-container","delay":{"show":500,"hide":50}}\' data-original-title="'.$translator->translate('Click to sort by this column').'">';
        $html .= $translator->translate($config->title);

        if ($config->column == $config->sort)
        {
            $direction = $direction == 'desc' ? 'descending' : 'ascending'; // toggle
            $html .= '<span class="k-sort-'.$direction.'" aria-hidden="true"></span>';
            $html .= '<span class="k-visually-hidden">'.$direction.'</span>';
        }

        $html .= '</a>';

        return $html;
    }

    /**
     * Render an enable field
     *
     * @param   array   $config An optional array with configuration options
     * @return  string  Html
     */
    public function enable($config = array())
    {
        $translator = $this->getObject('translator');

        $config = new ObjectConfigJson($config);
        $config->append(array(
            'entity'    => null,
            'field'     => 'enabled',
            'clickable' => true
        ))->append(array(
            'enabled'   => (bool) $config->entity->{$config->field},
            'data'      => array($config->field => $config->entity->{$config->field} ? 0 : 1),
        ))->append(array(
            'alt'       => $config->enabled ? $translator->translate('Enabled') : $translator->translate('Disabled'),
            'tooltip'   => $config->enabled ? $translator->translate('Disable Item') : $translator->translate('Enable Item'),
            'color'     => $config->enabled ? '#468847' : '#b94a48',
            'icon'      => $config->enabled ? 'enabled' : 'disabled',
        ));

        $class  = $config->enabled ? 'k-table__item--state-published' : 'k-table__item--state-unpublished';

        $tooltip = '';

        if ($config->clickable)
        {
            $data    = htmlentities(json_encode($config->data->toArray()));
            $tooltip = 'data-k-tooltip=\'{"container":".k-ui-container","delay":{"show":500,"hide":50}}\'
            style="cursor: pointer"
            data-action="edit" 
            data-data="'.$data.'" 
            data-original-title="'.$config->tooltip.'"';
        }

        $html = '<span class="k-table__item--state '.$class.'" '.$tooltip.'>'.$config->alt.'</span>';
        $html .= $this->createHelper('behavior')->tooltip();

        return $html;
    }

    /**
     * Render a publish field
     *
     * @param   array   $config An optional array with configuration options
     * @return  string  Html
     */
    public function publish($config = array())
    {
        $translator = $this->getObject('translator');

        $config = new ObjectConfigJson($config);
        $config->append(array(
            'entity'    => null,
            'field'     => 'enabled',
            'clickable' => true
        ))->append(array(
            'enabled'   => (bool) $config->entity->{$config->field},
        ))->append(array(
            'alt'       => $config->enabled ? $translator->translate('Published') : $translator->translate('Unpublished'),
            'tooltip'   => $config->enabled ? $translator->translate('Unpublish Item') : $translator->translate('Publish Item'),
            'color'     => $config->enabled ? '#468847' : '#b94a48',
            'icon'      => $config->enabled ? 'enabled' : 'disabled',
        ));

        return $this->enable($config);
    }

    /**
     * Get the locked information
     *
     * @param  array|ObjectConfig $config An optional configuration array.
     * @throws \UnexpectedValueException
     * @return string The locked by "name" "date" message
     */
    public function lock_message($config = array())
    {
        $config = new ObjectConfigJson($config);
        $config->append(array(
            'entity' => null
        ));

        if (!($config->entity instanceof ModelEntityInterface)) {
            throw new \UnexpectedValueException('$config->entity should be a ModelEntityInterface instance');
        }

        $entity = $config->entity;
        $message = '';

        if($entity->isLockable() && $entity->isLocked())
        {
            $user = $entity->getLocker();
            $date = $this->getObject('date', array('date' => $entity->locked_on));

            $message = $this->getObject('translator')->translate(
                'Locked by {name} {date}', array('name' => $user->getName(), 'date' => $date->humanize())
            );
        }

        return $message;
    }
}
