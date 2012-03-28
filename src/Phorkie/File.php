<?php
namespace Phorkie;

class File
{
    /**
     * Full path to the file
     *
     * @var string
     */
    public $path;

    /**
     * Repository this file belongs to
     *
     * @var string
     */
    public $repo;

    /**
     * Maps file extensions to MIME Types
     *
     * @var array
     */
    public static $arMimeTypeMap = array(
        'css'  => 'text/css',
        'htm'  => 'text/html',
        'html' => 'text/html',
        'js'   => 'application/javascript',
        'php'  => 'text/x-php',
        'txt'  => 'text/plain',
        'xml'  => 'text/xml',
    );

    /**
     * Maps file extensions to geshi types
     *
     * @var array
     */
    public static $arTypeMap = array(
        'htm'  => 'xml',
        'html' => 'xml',
    );

    public function __construct($path, Repository $repo = null)
    {
        $this->path = $path;
        $this->repo = $repo;
    }

    /**
     * Get filename relative to the repository path
     *
     * @return string
     */
    public function getFilename()
    {
        return basename($this->path);
    }

    /**
     * Return the full path to the file
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get file extension without dot
     *
     * @return string
     */
    public function getExt()
    {
        return substr($this->path, strrpos($this->path, '.') + 1);
    }

    /**
     * Returns the type of the file, as used by Geshi
     *
     * @return string
     */
    public function getType()
    {
        $ext = $this->getExt();
        if (isset(static::$arTypeMap[$ext])) {
            $ext = static::$arTypeMap[$ext];
        }

        return $ext;
    }

    public function getContent()
    {
        return file_get_contents($this->path);
    }

    public function getHighlightedContent()
    {
        /**
         * Yes, geshi needs to be in your include path
         * We use the mediawiki geshi extension package.
         */
        require 'MediaWiki/geshi/geshi/geshi.php';
        $geshi = new \GeSHi($this->getContent(), $this->getType());
        $geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
        $geshi->set_header_type(GESHI_HEADER_DIV);
        return $geshi->parse_code();
    }

    public function getMimeType()
    {
        $ext = $this->getExt();
        if (!isset(static::$arMimeTypeMap[$ext])) {
            return null;
        }
        return static::$arMimeTypeMap[$ext];
    }

    /**
     * Get a link to the file
     *
     * @param string $type Link type. Supported are:
     *                     - "raw"
     *                     - "display"
     *
     * @return string
     */
    public function getLink($type)
    {
        if ($type == 'raw') {
            return '/' . $this->repo->id . '/raw/' . $this->getFilename();
        }
        throw new Exception('Unknown type');
    }
}

?>