<?php
/**
 * @version		$Id: file.php 4738 2012-08-01 21:58:29Z johanjanssens $
 * @package     Koowa_View
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Use to force browser to download a file from the file system
 *
 * <code>
 * // in child view class
 * public function display()
 * {
 *      $this->path = path/to/file');
 *      // OR
 *      $this->output = $file_contents;
 *
 *      $this->filename = foobar.pdf';
 *
 *      // optional:
 *      $this->mimetype    = 'application/pdf';
 *      $this->disposition =  'inline'; // defaults to 'attachment' to force download
 *
 *      return parent::display();
 * }
 * </code>
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Koowa_View
 */
class KViewFile extends KViewAbstract
{
    /**
     * The file path
     *
     * @var string
     */
    public $path = '';

    /**
     * The file name
     *
     * @var string
     */
    public $filename = '';

    /**
     * The file disposition
     *
     * @var string
     */
    public $disposition = 'attachment';
    
    /**
     * Transport method
     *
     * @var string
     */
	public $transport;

    /**
     * File path
     *
     * @var string
     */
    public $file;

    /**
     * File size in bytes
     *
     * @var int
     */
    public $filesize;

    /**
     * Start point when serving a file in chunks as bytes
     *
     * @var int
     */
    public $start_point;

    /**
     * End point when serving a file in chunks as bytes
     *
     * @var int
     */
    public $end_point;

    /**
     * End the request by exiting in the display method if true
     *
     * @var boolean
     */
    public $end_request;

    /**
     * Constructor
     *
     * @param KConfig $config An optional KConfig object with configuration options
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        $this->set($config->toArray());
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KConfig $config An optional KConfig object with configuration options
     * @return  void
     */
    protected function _initialize(KConfig $config)
    {
        $count = count($this->getIdentifier()->path);

        $config->append(array(
            'path'        => '',
            'filename'    => $this->getIdentifier()->path[$count-1].'.'.$this->getIdentifier()->name,
            'disposition' => 'attachment',
            'transport'   => 'php',
            'end_request' => true
        ));

        parent::_initialize($config);
    }

    /**
     * Return the views output
     *
     * @throws KViewException
     * @return void
     */
    public function display()
    {
        if (empty($this->path) && empty($this->output)) {
        	throw new KViewException('No output or path supplied');
        }

    	// For a certain unmentionable browser
    	if(ini_get('zlib.output_compression')) {
    		@ini_set('zlib.output_compression', 'Off');
    	}
    	
    	// fix for IE7/8
    	if(function_exists('apache_setenv')) {
    	    apache_setenv('no-gzip', '1');
    	}

    	// Remove php's time limit
    	if(!ini_get('safe_mode')) {
    		@set_time_limit(0);
    	}

    	// Clear buffer
    	while (@ob_end_clean());

    	$this->filename = basename($this->filename);

    	if (!empty($this->output)) { // File body is passed as string
    		if (empty($this->filename)) {
    			throw new KViewException('No filename supplied');
    		}
    	} elseif (!empty($this->path)) { // File is read from disk
    		if (empty($this->filename)) {
    			$this->filename = basename($this->path);
    		}
    	}

        $transport = '_transport'.ucfirst(strtolower($this->transport));
    	if (!method_exists($this, $transport)) {
    	    throw new KViewException('Transport method is missing');
    	}
    	
    	$this->$transport();

        if ($this->end_request) {
            exit;
        }
    }

    /**
     * Reads the file by chunks and serves it
     *
     * Supports HTTP_RANGE headers
     *
     * @return int Number of bytes served
     * @throws KViewException
     */
    protected function _transportPhp()
    {
        if (empty($this->filesize)) {
            $this->filesize = $this->path ? filesize($this->path) : strlen($this->output);
        }
    
        $this->start_point = 0;
        $this->end_point = max($this->filesize-1, 0);
    
        $this->_setHeaders();

        $range = KRequest::get('server.HTTP_RANGE', 'string');
        if ($range && preg_match('/^bytes=((\d*-\d*,? ?)+)$/', $range))
        {
            // Partial download
            $parts = explode('-', substr($range, strlen('bytes=')));
            $this->start_point = $parts[0];
            if (!empty($parts[0])) {
                $this->start_point = $parts[0];
            }
    
            if (!empty($parts[1]) && $parts[1] <= $this->end_point) {
                $this->end_point = $parts[1];
            }
                
            if ($this->start_point > $this->filesize) {
                throw new KViewException('Invalid start point given in range header');
            }
    
            header('HTTP/1.0 206 Partial Content');
            header('Status: 206 Partial Content');
            header('Accept-Ranges: bytes');
            header('Content-Range: bytes '.$this->start_point.'-'.$this->end_point.'/'.$this->filesize);
            header('Content-Length: '.($this->end_point - $this->start_point + 1), true);
        }
    
        flush();
    
        if ($this->output)
        {
            $this->file = tmpfile();
            fwrite($this->file, $this->output);
            fseek($this->file, 0);
        }
        else {
            $this->file = fopen($this->path, 'rb');
        }

        if ($this->file === false) {
            throw new KViewException('Cannot open file');
        }

        if ($this->start_point > 0) {
            fseek($this->file, $this->start_point);
        }
    
        //serve data chunk and update download progress log
        $count = 0;
        $start = $this->start_point;
        while (!feof($this->file) && $start <= $this->end_point)
        {
            //calculate next chunk size
            $chunk_size = 1*(1024*1024);
            if ($start + $chunk_size > $this->end_point + 1) {
                $chunk_size = $this->end_point - $start + 1;
            }
    
            //get data chunk
            $buffer = fread($this->file, $chunk_size);
            if ($buffer === false) {
                throw new KViewException('Could not read file');
            }
    
            echo $buffer;
            flush();
            $count += strlen($buffer);
        }
    
        if (!empty($this->file) && is_resource($this->file)) {
            fclose($this->file);
        }
    
        return $count;
    }

    /**
     * Transports file using Apache Sendfile module
     *
     * @return void
     * @throws KViewException
     */
    protected function _transportApache()
    {
        if (!empty($this->output)) {
            $this->_transportPhp();
            return;
        }
        elseif (empty($this->path)) {
            throw new KViewException('File path is missing');
        }
    
        $this->_setHeaders();
        header('X-Sendfile: '.$this->path);
    }

    /**
     * Transports file using Nginx
     *
     * @return void
     * @throws KViewException
     */
    protected function _transportNginx()
    {
        if (!empty($this->output)) {
            $this->_transportPhp();
            return;
        }
        elseif (empty($this->path)) {
            throw new KViewException('File path is missing');
        }
    
        $this->_setHeaders();
        $path = preg_replace('/'.preg_quote(JPATH_ROOT, '/').'/', '', $this->path, 1);
        header('X-Accel-Redirect: '.$path);
    }

    /**
     * Transports file using Lighttpd
     *
     * @return void
     * @throws KViewException
     */
    protected function _transportLighttpd()
    {
        if (!empty($this->output)) {
            $this->_transportPhp();
            return;
        }
        elseif (empty($this->path)) {
            throw new KViewException('File path is missing');
        }
    
        $this->_setHeaders();
        header('X-LIGHTTPD-send-file: '.$this->path); // For v1.4
        header('X-Sendfile: '.$this->path); // For v1.5
    }

    /**
     * Set the appropriate headers for serving the file
     *
     * @return void
     */
    protected function _setHeaders()
    {
        if ($this->mimetype) {
            header('Content-type: '.$this->mimetype);
        }
    
        $this->_setDisposition();
    
        header('Content-Transfer-Encoding: binary');
        header('Accept-Ranges: bytes');
    
        // Prevent caching
        // Pragma and cache-control needs to be empty for IE on SSL.
        // See: http://support.microsoft.com/default.aspx?scid=KB;EN-US;q316431
        $agent = isset($_SERVER['HTTP_USER_AGENT']) ? trim($_SERVER['HTTP_USER_AGENT']) : null;
        if ($agent && preg_match('#(?:MSIE |Internet Explorer/)(?:[0-9.]+)#', $agent)
                   && (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        ) {
            header('Pragma: ');
            header('Cache-Control: ');
        }
        else
        {
            header('Pragma: no-store,no-cache');
            header('Cache-Control: no-cache, no-store, must-revalidate, max-age=-1');
            header('Cache-Control: post-check=0, pre-check=0', false);

        }
        header('Expires: Mon, 14 Jul 1789 12:30:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    
        if (!empty($this->filesize)) {
            header('Content-Length: '.$this->filesize);
        }
    
    }    

    /**
     * Set the disposition headers
     *
     * @return void
     */
    protected function _setDisposition()
    {
        // @TODO :Content-Disposition: inline; filename="foo"; modification-date="'.$date.'"; size=123;
        if(isset($this->disposition) && $this->disposition == 'inline') {
            header('Content-Disposition: inline; filename="'.$this->filename.'"');
        } else {
            header('Content-Description: File Transfer');
            //header('Content-type: application/force-download');
            header('Content-Disposition: attachment; filename="'.$this->filename.'"');
        }
    }
}
