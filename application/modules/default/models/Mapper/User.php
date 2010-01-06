<?php
/**
 * Firal
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://firal.org/licenses/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to firal-dev@googlegroups.com so we can send you a copy immediately.
 *
 * @category   Firal
 * @package    Default_Models
 * @subpackage Mapper
 * @copyright  Copyright (c) 2009 Firal (http://firal.org/)
 * @license    http://firal.org/licenses/new-bsd    New BSD License
 */

/**
 * User model mapper class
 *
 * @category   Firal
 * @package    Default_Models
 * @subpackage Mapper
 * @copyright  Copyright (c) 2009 Firal (http://firal.org/)
 * @license    http://firal.org/licenses/new-bsd    New BSD License
 */
class Default_Model_Mapper_User extends Firal_Model_Mapper_MapperAbstract implements Zend_Auth_Adapter_Interface
{

    /**
     * The user's identity
     *
     * @var string
     */
    protected $_identity;

    /**
     * The user's credentials
     *
     * @var string
     */
    protected $_credentials;

    /**
     * Table name
     *
     * @var string
     */
    protected $_name = 'users';


    /**
     * Fetch a user by its id
     *
     * @param int $id
     *
     * @return Default_Model_User
     */
    public function fetchById($id)
    {
        $db = $this->getAdapter();

        $sql = $db->select()
                  ->from($this->getTableName())
                  ->where('id=?', $id);

        $row = $db->fetchRow($sql);

        if (false === $row) {
            return null;
        }

        return new Default_Model_User($row);
    }

    /**
     * Fetch a user by its name
     *
     * @param string $name
     *
     * @return Default_Model_User
     */
    public function fetchByName($name)
    {
        $db = $this->getAdapter();

        $sql = $db->select()
                  ->from($this->getTableName())
                  ->where('name=?', $name);

        $row = $db->fetchRow($sql);

        if (false === $row) {
            return null;
        }

        return new Default_Model_User($row);
    }

    /**
     * Set user credentials
     *
     * @param string $identity
     * @param string $credentials
     *
     * @return Default_Model_Mapper_User
     */
    public function setCredentials($identity, $credentials)
    {
        $this->_identity    = $identity;
        $this->_credentials = sha1($credentials);

        return $this;
    }

    /**
     * Try to authenticate a user
     *
     * @return Zend_Auth_Result
     */
    public function authenticate()
    {
        if (null === ($user = $this->fetchByName($this->_identity))) {
            $code    = Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND;
            $message = 'Invalid identity';
        } elseif ($user->passwordHash != $this->_credentials) {
            $code    = Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID;
            $message = 'Invalid password';
        } else {
            $code    = Zend_Auth_Result::SUCCESS;
            $message = 'Success';
        }

        return new Zend_Auth_Result($code, $this->_identity, array($message));
    }
}