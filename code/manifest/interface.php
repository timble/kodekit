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
 * Manifest Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Manifest
 */
interface ManifestInterface extends ObjectInterface
{
    /**
     * Get the name
     *
     * @return string
     */
    public function getName();

    /**
     * Get the description
     *
     * @return string
     */
    public function getDescription();

    /**
     * Get the version
     *
     * See @link http://semver.org/spec/v2.0.0.html
     *
     * @return string
     */
    public function getVersion();

    /**
     * Get the license
     *
     * @return string
     */
    public function getLicense();

    /**
     * Get the copyright
     *
     * @return string
     */
    public function getCopyright();

    /**
     * Get the homepage
     *
     * @return string
     */
    public function getHomepage();

    /**
     * Get the homepage
     *
     * @return array
     */
    public function getAuthors();

}
