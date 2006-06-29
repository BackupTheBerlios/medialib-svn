<?php
/**
 * authentication classes for hgkmedialib
 *
 * This file handles the authentication for the hgkmedialib frond- and backend.
 * It contains two handler classes: HgkMediaLib_User and HgkMediaLib_Authentication
 * and the class HGKMediaLib_Struct_User for creating a user struct.
 *
 * HGKMediaLib_Struct_User creates a object that contains the user data requested by frontend.
 * HgkMediaLib_User handles the ldap request.
 * HgkMediaLib_Authentication handles authorisation and session handling as soap handler class
 * and in the HgkMediaLib_Reading class.
 *
 * @package HGKMediaLib
 * @author Franz-Ferdinand Lehnert <franz.lehnert@winet.ch>
 */

require_once('../global/customize.php');
require_once(ROOT_PATH.'/db/HgkMediaLib_DataBase.php');


/**
 * creats a struct used to return a object with user data
 *
 *
 * @package HGKMediaLib
 * @author Franz-Ferdinand Lehnert <franz.lehnert@winet.ch>
 * @version 0.1
 */
class HGKMediaLib_Struct_User
{
    /**
     * first name of a user
     *
     * @var strin
     * @access public
     */
    public $first;

    /**
     * last name of a user
     *
     * @var string
     * @access public
     */
    public $last;

    /**
     * domain name of a user
     *
     * @var string
     * @access public
     */
    public $domain;

    /**
     * email name of a user
     *
     * @var string
     * @access public
     */
    public $email;

    public function __construct($first_name,$last_name,$domain,$email)
    {
        $this->first = $first_name;
        $this->last = $last_name;
        $this->domain = $domain;
        $this->email = $email;
    }
}


/**
 * class for ldap handling
 *
 *
 * @package HGKMediaLib
 * @author Franz-Ferdinand Lehnert <franz.lehnert@winet.ch>
 * @version 0.1
 */
class HgkMediaLib_User
{
    /**
     * domain name
     *
     * @var string
     * @access public
     */
    private $domain;

    /**
     * user name
     *
     * @var string
     * @access public
     */
    private $user_name;

    /**
     * password name
     *
     * @var string
     * @access public
     */
    private $password;

    public function __construct($domain, $user_name, $password)
    {
        $this->domain = $domain;
        $this->user_name = $user_name;
        $this->password = $password;
    }

    // liefert true, wenn benutzername/pwd korrekt
    public function checkPassword()
    {
        return true;
    }

    // liefert array mit domain/gruppe   z.B.
    //"hgkz/studierende","hgkz/dozierende",...
    public function getGroups($domain='')
    {
        $return_array = array(
            'hgkz/users'
        );

        return $return_array;
    }

    public function getUserData()
    {
        $user_data = array(
            'first' => 'test',
            'last' => 'user',
            'institution' => 'hgkz student library',
            'email' => 'test.user@hgkz.ch'
        );
        return $user_data;
    }

    // liefert die domain
    public function getDomain()
    {
        return 'hgkz';
    }
}

/**
 * class used as authemtication handler class for soap
 *
 *
 * @package HGKMediaLib
 * @author Franz-Ferdinand Lehnert <franz.lehnert@winet.ch>
 * @version 0.1
 */
class HGKMediaLib_Authentication extends HgkMediaLib_DataBase
{
    /**
     * db object
     *
     * @var object
     * @access private
     */
    private $user_name;

    /**
     * db object
     *
     * @var object
     * @access private
     */
    private $password;

    /**
     * db object
     *
     * @var object
     * @access private
     */
    private $domain;

    /**
     * array containing user data obtained from ldap
     *
     * @var object
     * @access private
     */
    private $user_data_array;
    /**
     * db object
     *
     * @var object
     * @access private
     */
    private $groups;

    /**
     * db object
     *
     * @var object
     * @access private
     */
    private $db_session_id;

    /**
     * db object
     *
     * @var object
     * @access private
     */
    private $php_session_id;

    /**
     * db object
     *
     * @var object
     * @access private
     */
    private $group_list;

    /**
     * The dropSession() methode unvalidates the given $session ID
     *
     * @param mixed $session id
     * @access public
     * @return boolean
     */
    public function dropSession($session)
    {
        if ($this->getExistingSession($session)) {
            $this->closeSoapSession($_SESSION['db_session']);
            return session_destroy();
           // if (session_destroy()){
           //     return true; 
           //     return $this->getAnonymousSession();
           // }
        } else {
//             return false;
            $this->throwSessionError();
        }
    }


    /**
     * throws a soap error: session doesn't exists
     *
     * @param void
     * @access private
     * @return void
     */
    public function throwSessionError()
    {
        throw new SoapFault("Authentication Error","session doesn't exists");
    }


    /**
     * throws a soap error: session doesn't exists
     *
     * @param string session
     * @access private
     * @return boolean true if session exists
     */
    public function getExistingSession($session)
    {
        session_id($session);
        session_start();
        if ((array_key_exists('php_session',$_SESSION)) && ($session == $_SESSION['php_session'])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get session for an unknown, anonymous user
     *
     * @param void
     * @access public
     * @return mixed session id
     */
    public function getAnonymousSession()
    {
        $this->createSession('anonym');
        return $this->php_session_id;
    }

    /**
     * get a list of available domains
     *
     * @param void
     * @access public
     * @return array of string
     */
    public function getDomains()
    {
        $domain_array = array('hgkz');
        return $domain_array;
    }

    /**
     * Authenticate using standard credentials
     *
     * @param string $user_name
     * @param string $password
     * @param string $domain
     * @access public
     * @return mixed session Id
     */
    public function getSession($user_name, $password, $domain)
    {
        if ($user_name == '') {
            throw new SoapFault("Authentication Error","user name missed");
        } else {
            $this->user_name = $user_name;
        }

        if ($password == '') {
            throw new SoapFault("Authentication Error","password missed");
        } else {
            $this->password = $password;
        }

        if ($password == '') {
            throw new SoapFault("Authentication Error","domain name missed");
        } else {
            $this->domain = $domain;
        }

        $user = new HgkMediaLib_User($domain, $user_name, $password);


        //get session
        if (!$is_authorized = $user->checkPassword()) {
            throw new SoapFault("HGKMediaLib_Auth","user is not authorized");
        }

        $this->user_data_array = $user->getUserData();

        $this->createSession('user');

        if (!$this->db_session_id) {
            //throw new SoapFault("HGKMediaLib_Auth","could not create db session");
        }

        return $this->php_session_id;
    }

    /**
     * Get user data (real name, name of institution, etc.)
     *
     * @param mixed $session id
     * @access public
     * @return object HGKMediaLib_Struct_User
     */
    public function getUserData($session)
    {
        if ($this->getExistingSession($session)) {
            $first = $_SESSION['user_data']['first'];
            $last = $_SESSION['user_data']['last'];
            $domain = $_SESSION['domain'];
            $email = $_SESSION['user_data']['email'];
            return $user = new HGKMediaLib_Struct_User($first,$last,$domain,$email);
        } else {
            $this->throwSessionError();
        }
    }

    /**
     * creates a session, writes it in data base (loging) and in $_SESSION array
     *
     * @param string type: user or anonym
     * @return void
     */
    private function createSession($type='user')
    {
        session_start();
        $this->php_session_id = session_id();
        $id = $this->php_session_id;
        if ($type == 'user') {
            $user = $this->user_name;
            $domain = $this->domain;
        } else if ($type == 'anonym') {
            $user = $type;
            $domain = $type;
        }
        $this->db_session_id = $this->setSoapSession($id,$user,$domain);
        $_SESSION = array(
            'php_session' => $this->php_session_id,
            'db_session' => $this->db_session_id,
            'domain' => $this->domain
        );
        if ($type = 'user') {
            $_SESSION['user_data'] = $this->user_data_array;
        }
    }
}

if (DEBUG_FLAG) {
    $user = 'otto';
    $domain = 'hgkz';
    $password = 'test';
    $auth = new HgkMediaLib_Authentication();

    $session_id = $auth->getSession($user, $password, $domain);
    echo 'session_id: '.$session_id.'<br>';
    echo '<pre>';
    print_r($_SESSION);
    echo '</pre>';

    $auth->dropSession('e4bec1a245656f1335e267072bf83bde');
    $user = $auth->getUserData('17d2fb493024c910549b00571d8825fb');
    echo '<pre>';
    print_r($user);
    echo '</pre>';
} else {
    ini_set('soap.wsdl_cache_enabled', '0');
    try {
        $soap = new SoapServer(ROOT_PATH.'soap/wsdl/HgkMediaLib_Authentication.wsdl');
        $soap->setClass("HgkMediaLib_Authentication");
        $soap->handle();
    } catch (SoapFault $f) {
        throw new SoapFault();
    }
}
?>
