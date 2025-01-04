<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    webdav
 */

/*CQC: No check*/

namespace webdav_commandr_fs {
    /**
     * Base node-class.
     *
     * The node class implements the method used by both the File and the Directory classes
     *
     * @copyright Copyright (C) 2007-2013 Rooftop Solutions. All rights reserved.
     * @author Evert Pot (http://www.rooftopsolutions.nl/)
     * @license http://code.google.com/p/sabredav/wiki/License Modified BSD License
     */
    abstract class Node implements \Sabre\DAV\INode
    {
        /**
         * The path to the current node
         *
         * @var string
         */
        protected $path;

        /**
         * The Commandr-fs object we are chaining to
         *
         * @var object
         */
        protected $commandr_fs;

        /**
         * Sets up the node, expects a full path name
         *
         * @param string $path
         */
        public function __construct($path)
        {
            $this->path = $path;

            require_code('commandr_fs');
            $this->commandr_fs = new \commandr_fs();
        }

        /**
         * Returns the name of the node
         *
         * @return string
         */
        public function getName()
        {
            list(, $name) = \Sabre\Uri\split($this->path);
            return $name;
        }

        /**
         * Renames the node
         *
         * @param string $name The new name
         * @return void
         */
        public function setName($name)
        {
            list($parentPath,) = \Sabre\Uri\split($this->path);
            list(, $newName) = \Sabre\Uri\split($name);

            $parsedOldPath = $this->commandr_fs->_pwd_to_array($this->path);

            $newPath = $parentPath . '/' . $newName;
            $parsedNewPath = $this->commandr_fs->_pwd_to_array($newPath);

            if ($this->commandr_fs->_is_file($parsedOldPath)) {
                // File
                $test = $this->commandr_fs->move_file($parsedOldPath, $parsedNewPath);
            } elseif ($this->commandr_fs->_is_dir($parsedOldPath)) {
                // Directory
                $test = $this->commandr_fs->move_directory($parsedOldPath, $parsedNewPath);
            } else {
                throw new \Sabre\DAV\Exception\NotFound('Error renaming/moving ' . $name);
            }

            if ($test === false) {
                throw new \Sabre\DAV\Exception\Forbidden('Error renaming/moving ' . $name);
            }

            $GLOBALS['COMMANDR_FS_LISTING_CACHE'] = [];

            $this->path = $newPath;
        }

        /**
         * Returns the last modification time, as a unix timestamp
         *
         * @return ?int The last modification time (null: no path specified)
         */
        public function getLastModified()
        {
            if ($this->path == '') {
                return null;
            }

            list($currentPath, $currentName) = \Sabre\Uri\split($this->path);
            $parsedCurrentPath = $this->commandr_fs->_pwd_to_array($currentPath);

            $listing = $this->_listingWrap($parsedCurrentPath);
            foreach ($listing[0] + $listing[1] as $l) {
                list($filename, $filetype, $filesize, $filetime) = $l;
                if ($filename == $currentName) {
                    return $filetime;
                }
            }

            throw new \Sabre\DAV\Exception\NotFound('Could not find ' . $this->path);

            return null;
        }

        /**
         * Returns a directory listing
         *
         * @param array $parsedPath Directory listing
         * @return array
         */
        protected function _listingWrap($parsedPath)
        {
            cms_profile_start_for('webdav_commandr_fs->Node->_listingWrap');
            $listings = $this->commandr_fs->listing($parsedPath);
            cms_profile_end_for('webdav_commandr_fs->Node->_listingWrap');
            return $listings;
        }
    }

    /**
     * Directory class.
     *
     * @copyright Copyright (C) 2007-2013 Rooftop Solutions. All rights reserved.
     * @author Evert Pot (http://www.rooftopsolutions.nl/)
     * @license http://code.google.com/p/sabredav/wiki/License Modified BSD License
     */
    class Directory extends Node implements \Sabre\DAV\ICollection
    {
        /**
         * Creates a new file in the directory
         *
         * @param string $name Name of the file
         * @param resource|string $data Initial payload
         * @return null|string
         */
        public function createFile($name, $data = null)
        {
            $newPath = $this->path . '/' . $name;

            $parsedNewPath = $this->commandr_fs->_pwd_to_array($newPath);

            if (is_resource($data)) {
                ob_start();
                fpassthru($data);
                $data = ob_get_clean();
            }

            $test = $this->commandr_fs->write_file($parsedNewPath, ($data === null) ? '' : $data);

            if ($test === false) {
                throw new \Sabre\DAV\Exception\Forbidden('Could not create ' . $name);
            }

            $GLOBALS['COMMANDR_FS_LISTING_CACHE'] = [];
        }

        /**
         * Creates a new subdirectory
         *
         * @param string $name
         * @return void
         */
        public function createDirectory($name)
        {
            $newPath = $this->path . '/' . $name;

            $parsedNewPath = $this->commandr_fs->_pwd_to_array($newPath);

            $test = $this->commandr_fs->make_directory($parsedNewPath);

            if ($test === false) {
                throw new \Sabre\DAV\Exception\Forbidden('Could not create ' . $name);
            }

            $GLOBALS['COMMANDR_FS_LISTING_CACHE'] = [];
        }

        /**
         * Returns a specific child node, referenced by its name
         *
         * This method must throw \Sabre\DAV\Exception\NotFound if the node does not
         * exist.
         *
         * @param string $name
         * @return \Sabre\DAV\INode
         */
        public function getChild($name)
        {
            $path = $this->path . '/' . $name;

            $parsedPath = $this->commandr_fs->_pwd_to_array($path);

            if ($name == '') {
                return new Directory('');
            }

            if ($this->commandr_fs->_is_dir($parsedPath)) {
                return new Directory($path);
            } elseif ($this->commandr_fs->_is_file($parsedPath)) {
                return new File($path);
            }

            throw new \Sabre\DAV\Exception\NotFound('Could not find ' . $name);
        }

        /**
         * Returns an array with all the child nodes
         *
         * @return \Sabre\DAV\INode[]
         */
        public function getChildren()
        {
            cms_profile_start_for('webdav_commandr_fs->Directory->getChildren');

            $listing = $this->_listingWrap($this->commandr_fs->_pwd_to_array($this->path));

            $nodes = [];
            foreach ($listing[0] as $l) {
                list($filename, $filetype, $filesize, $filetime) = $l;

                $_path = $this->path . '/' . $filename;

                $node = new Directory($_path);
                $nodes[] = $node;
            }
            foreach ($listing[1] as $l) {
                list($filename, $filetype, $filesize, $filetime) = $l;

                $_path = $this->path . '/' . $filename;

                $node = new File($_path);
                $nodes[] = $node;
            }

            cms_profile_end_for('webdav_commandr_fs->Directory->getChildren', integer_format(count($nodes)) . ' nodes');

            return $nodes;
        }

        /**
         * Checks if a child exists.
         *
         * @param string $name
         * @return bool
         */
        public function childExists($name)
        {
            cms_profile_start_for('webdav_commandr_fs->Directory->childExists');

            $listing = $this->_listingWrap($this->commandr_fs->_pwd_to_array($this->path));

            $nodes = [];
            foreach ($listing[0] + $listing[1] as $l) {
                list($filename, $filetype, $filesize, $filetime) = $l;

                if ($filename == $name) {
                    cms_profile_end_for('webdav_commandr_fs->Directory->childExists');
                    return true;
                }
            }

            cms_profile_end_for('webdav_commandr_fs->Directory->childExists');

            return false;
        }

        /**
         * Deletes all files in this directory, and then itself
         *
         * @return void
         */
        public function delete()
        {
            $parsedPath = $this->commandr_fs->_pwd_to_array($this->path);

            $test = $this->commandr_fs->remove_directory($parsedPath);

            if ($test === false) {
                throw new \Sabre\DAV\Exception\Forbidden('Could not delete ' . $this->path);
            }

            $GLOBALS['COMMANDR_FS_LISTING_CACHE'] = [];
        }
    }

    /**
     * File class.
     *
     * @copyright Copyright (C) 2007-2013 Rooftop Solutions. All rights reserved.
     * @author Evert Pot (http://www.rooftopsolutions.nl/)
     * @license http://code.google.com/p/sabredav/wiki/License Modified BSD License
     */
    class File extends Node implements \Sabre\DAV\IFile
    {
        /**
         * Updates the data
         *
         * @param resource $data
         * @return void
         */
        public function put($data)
        {
            $parsedPath = $this->commandr_fs->_pwd_to_array($this->path);

            if (is_resource($data)) {
                ob_start();
                fpassthru($data);
                $data = ob_get_clean();
            }

            $test = $this->commandr_fs->write_file($parsedPath, ($data === null) ? '' : $data);

            if ($test === false) {
                throw new \Sabre\DAV\Exception\Forbidden('Could not save ' . $this->path);
            }
        }

        /**
         * Returns the data
         *
         * @return string
         */
        public function get()
        {
            $parsedPath = $this->commandr_fs->_pwd_to_array($this->path);

            $test = $this->commandr_fs->read_file($parsedPath);

            if ($test === false) {
                throw new \Sabre\DAV\Exception\NotFound('Could not find ' . $this->path);
            }

            return $test;
        }

        /**
         * Delete the current file
         *
         * @return void
         */
        public function delete()
        {
            $parsedPath = $this->commandr_fs->_pwd_to_array($this->path);

            $test = $this->commandr_fs->remove_file($parsedPath);

            if ($test === false) {
                throw new \Sabre\DAV\Exception\Forbidden('Could not delete ' . $this->path);
            }

            $GLOBALS['COMMANDR_FS_LISTING_CACHE'] = [];
        }

        /**
         * Returns the size of the node, in bytes
         *
         * @return int
         */
        public function getSize()
        {
            $server = ServerRegistry::getServer();
            if (!$server) {
                var_dump($server);
                exit;
            }

            cms_profile_start_for('webdav_commandr_fs->File->getSize');

            // Determine if we absolutely have to calculate the file size
            $must_calculate_size = false;
            $request = $server->httpRequest;
            if (in_array($request->getMethod(), ['GET', 'HEAD'])) {
                $uri = $request->getPath();
                $node = $server->tree->getNodeForPath($uri);
                if ($node instanceof \Sabre\DAV\IFile) {
                    $must_calculate_size = true;
                }
            }
            if ($request->getMethod() === 'PROPFIND') {
				$uri = $request->getPath();
				$node = $server->tree->getNodeForPath($uri);

				// For PROPFIND, we are likely listing directory contents, but we could be downloading
				if ($node instanceof \Sabre\DAV\ICollection) {
				} else {
					$must_calculate_size = true;
				}
            }
            $rangeHeader = $request->getHeader('Range');
            if ($rangeHeader) {
                $must_calculate_size = true;
            }
            if (in_array($request->getMethod(), ['COPY', 'MOVE'])) {
                $must_calculate_size = true;
            }

            list($currentPath, $currentName) = \Sabre\Uri\split($this->path);
            $parsedPath = $this->commandr_fs->_pwd_to_array($this->path);
            $parsedCurrentPath = $this->commandr_fs->_pwd_to_array($currentPath);

            $listing = $this->_listingWrap($parsedCurrentPath);
            foreach ($listing[1] as $l) {
                list($filename, $filetype, $filesize, $filetime) = $l;
                if ($filename == $currentName) {
                    if ($filesize === null) {
                        $filesize = $this->commandr_fs->get_file_size($parsedPath, $must_calculate_size);
                    }
                    cms_profile_end_for('webdav_commandr_fs->File->getSize');
                    return $filesize;
                }
            }

            cms_profile_end_for('webdav_commandr_fs->File->getSize');

            throw new \Sabre\DAV\Exception\NotFound('Could not find ' . $this->path);

            return 0;
        }

        /**
         * Returns the ETag for a file
         *
         * An ETag is a unique identifier representing the current version of the file. If the file changes, the ETag MUST change.
         * The ETag is an arbitrary string, but MUST be surrounded by double-quotes.
         *
         * Return null if the ETag can not effectively be determined
         *
         * @return mixed
         */
        public function getETag()
        {
            return null;
        }

        /**
         * Returns the mime-type for a file
         *
         * If null is returned, we'll assume application/octet-stream
         *
         * @return mixed
         */
        public function getContentType()
        {
            return null;
        }
    }

    class Auth extends \Sabre\DAV\Auth\Backend\AbstractBasic
    {
        /**
         * Validates a username and password
         *
         * This method should return true or false depending on if login
         * succeeded.
         *
         * @param string $username
         * @param string $password
         * @return bool
         */
        public function validateUserPass($username, $password)
        {
            if (empty($username)) {
                return false;
            }

            $result = $GLOBALS['FORUM_DRIVER']->authorise_login($username, null, $password);
            if ($result['id'] === null) { // Failure, try blank password (as some clients don't let us input a blank password, so the real password could be blank)
                $password = '';
                $result = $GLOBALS['FORUM_DRIVER']->authorise_login($username, null, $password);
            }
            if ($result['id'] !== null) {
                require_code('users_inactive_occasionals');
                create_session($result['id']);
                return $GLOBALS['FORUM_DRIVER']->is_super_admin($result['id']);
            }
            return false;
        }
    }

    class ServerRegistry {
        private static ?\Sabre\DAV\Server $server = null;

        public static function setServer(\Sabre\DAV\Server $server): void {
            self::$server = $server;
        }

        public static function getServer(): ?\Sabre\DAV\Server {
            return self::$server;
        }
    }
}
