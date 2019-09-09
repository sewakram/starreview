<?php
include (plugin_dir_path( __FILE__ ) . 'star-prs-facebook/autoload.php');

/*
 * Classes required to call the Facebook API
 * They will be used by our class
 */
use Facebook\Facebook;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Exceptions\FacebookResponseException;

/**
 * Class StarPRSfacebook
 */
class StarPRSfacebook{

    /**
     * Facebook APP ID
     *
     * @var string
     */
    private $app_id = '3536860986339958';

    /**
     * Facebook APP Secret
     *
     * @var string
     */
    private $app_secret = '42179b2728e5032e2df108b45127054f';

    /**
     * Callback URL used by the API
     *
     * @var string
     */
    private $callback_url = 'https://www.consumerthought.com/wp-admin/admin-ajax.php?action=starreview_facebook';

    /**
     * Access token from Facebook
     *
     * @var string
     */
    private $access_token;

    /**
     * Where we redirect our user after the process
     *
     * @var string
     */
    private $redirect_url;

    /**
     * User details from the API
     */
    private $facebook_details;

    /**
     * AlkaFacebook constructor.
     */
    public function __construct()
    {

        // We register our shortcode
        add_shortcode( 'starreview_facebook', array($this, 'renderShortcode') );

        // Callback URL
        add_action( 'wp_ajax_starreview_facebook', array($this, 'apiCallback'));
        add_action( 'wp_ajax_nopriv_starreview_facebook', array($this, 'apiCallback'));
        add_action( 'wp_ajax_starreview_prs_gmail', array($this, 'starreviewGmailApi'));
        add_action( 'wp_ajax_nopriv_starreview_prs_gmail', array($this, 'starreviewGmailApi'));

    }

    /**
     * Render the shortcode [starreview_facebook/]
     *
     * It displays our Login / Register button
     */
    public function renderShortcode() {

        // Start the session
        if(!session_id()) {
            session_start();
        }

        // No need for the button is the user is already logged
        if(is_user_logged_in())
            return;

        // We save the URL for the redirection:
        if(!isset($_SESSION['starreview_facebook_url']))
            $_SESSION['starreview_facebook_url'] = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

        // Different labels according to whether the user is allowed to register or not
        if (get_option( 'users_can_register' )) {
            $button_label = __('Login or Register with Facebook', 'starreview');
        } else {
            $button_label = __('Login with Facebook', 'starreview');
        }

        // HTML markup
        $html = '<div id="starreview-facebook-wrapper">';

        // Messages
        if(isset($_SESSION['starreview_facebook_message'])) {
            $message = $_SESSION['starreview_facebook_message'];
            $html .= '<div id="starreview-facebook-message" class="alert alert-danger">'.$message.'</div>';
            // We remove them from the session
            unset($_SESSION['starreview_facebook_message']);
        }

        $_SESSION['starreview_facebook_url'] = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        
         echo '<input type="hidden" id="urlfacebook" value="'.$this->getLoginUrl().'"/>';
        echo '<div id="fb-root"></div>
            <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.3&appId=3536860986339958&autoLogAppEvents=1"></script>';
        // Button
        $html .= '<div class="fb-login-button" data-max-rows="1" onlogin="starreviewfb_loginCheck" data-width="" data-size="large" data-button-type="login_with" data-show-faces="false" data-auth-type="rerequest" data-auto-logout-link="false" data-use-continue-as="true" data-scope="email,public_profile"></div>';

        $html .= '</div>';

        //$html .= '<a id="login-button" href="https://accounts.google.com/o/oauth2/auth?scope=' . urlencode('https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email') . '&redirect_uri=' . urlencode(CLIENT_REDIRECT_URL) . '&response_type=code&client_id=' . CLIENT_ID . '&access_type=online">Login with Google</a>';

        // Write it down
        return $html;

    }

    /**
     * Init the API Connection
     *
     * @return Facebook
     */
    private function initApi() {

        $facebook = new Facebook([
            'app_id' => $this->app_id,
            'app_secret' => $this->app_secret,
            'default_graph_version' => 'v2.2',
            'persistent_data_handler' => 'session'
        ]);

        return $facebook;

    }

    /**
     * Login URL to Facebook API
     *
     * @return string
     */
    private function getLoginUrl() {

        if(!session_id()) {
            session_start();
        }

        $fb = $this->initApi();

        $helper = $fb->getRedirectLoginHelper();

        // Optional permissions
        $permissions = ['email'];

        $url = $helper->getLoginUrl($this->callback_url, $permissions);

        return esc_url($url);

    }

    public function starreviewGmailApi()
    {
       require_once('google-login-api.php');
       if(isset($_GET['code'])) {
            try {
                $gapi = new GoogleLoginApi();
                
                // Get the access token 
                $data = $gapi->GetAccessToken(CLIENT_ID, CLIENT_REDIRECT_URL, CLIENT_SECRET, $_GET['code']);
                
                // Get user information
                $user_info = $gapi->GetUserProfileInfo($data['access_token']);
                //print_r($user_info);die();
            }
            catch(Exception $e) {
                echo $e->getMessage();
                exit();
            }
        }
    }
    /**
     * API call back running whenever we hit /wp-admin/admin-ajax.php?action=alka_facebook
     * This code handles the Login / Regsitration part
     */
    public function apiCallback() {

        if(!session_id()) {
            session_start();
        }

        global $wp;
        $current_url = get_site_url( add_query_arg( array(), $wp->request ),'https' );
        // Set the Redirect URL:
        $this->redirect_url = (isset($_SESSION['starreview_facebook_url'])) ? $_SESSION['starreview_facebook_url'] : $current_url.'#respond';

        // We start the connection
        $fb = $this->initApi();

        // We save the token in our instance
        $this->access_token = $this->getToken($fb);

        // We get the user details
        $this->facebook_details = $this->getUserDetails($fb);

        // We first try to login the user
        $this->loginUser();

        // Otherwise, we create a new account
        $this->createUser();

        // Redirect the user
        header("Location: ".$this->redirect_url, true);
        die();

    }

    /**
     * Get a TOKEN from the Facebook API
     * Or redirect back if there is an error
     *
     * @param $fb Facebook
     * @return string - The Token
     */
    private function getToken($fb) {

        // Assign the Session variable for Facebook
        $_SESSION['FBRLH_state'] = $_GET['state'];

        // Load the Facebook SDK helper
        $helper = $fb->getRedirectLoginHelper();

        // Try to get an access token
        try {
            $accessToken = $helper->getAccessToken();
        }
        // When Graph returns an error
        catch(FacebookResponseException $e) {
            $error = __('Graph returned an error: ','alkaweb'). $e->getMessage();
            $message = array(
                'type' => 'error',
                'content' => $error
            );
        }
        // When validation fails or other local issues
        catch(FacebookSDKException $e) {
            $error = __('Facebook SDK returned an error: ','starreview'). $e->getMessage();
            $message = array(
                'type' => 'error',
                'content' => $error
            );
        }

        // If we don't got a token, it means we had an error
        if (!isset($accessToken)) {
            // Report our errors
            $_SESSION['starreview_facebook_message'] = $message;
            // Redirect
            header("Location: ".$this->redirect_url, true);
            die();
        }

        return $accessToken->getValue();

    }

    /**
     * Get user details through the Facebook API
     *
     * @link https://developers.facebook.com/docs/facebook-login/permissions#reference-public_profile
     * @param $fb Facebook
     * @return \Facebook\GraphNodes\GraphUser
     */
    private function getUserDetails($fb)
    {

        try {
            $response = $fb->get('/me?fields=id,name,first_name,last_name,email,link', $this->access_token);
        } catch(FacebookResponseException $e) {
            $message = __('Graph returned an error: ','starreview'). $e->getMessage();
            $message = array(
                'type' => 'error',
                'content' => $error
            );
        } catch(FacebookSDKException $e) {
            $message = __('Facebook SDK returned an error: ','starreview'). $e->getMessage();
            $message = array(
                'type' => 'error',
                'content' => $error
            );
        }

        // If we caught an error
        if (isset($message)) {
            // Report our errors
            $_SESSION['starreview_facebook_message'] = $message;
            // Redirect
            header("Location: ".$this->redirect_url, true);
            die();
        }

        return $response->getGraphUser();

    }

    /**
     * Login an user to WordPress
     *
     * @link https://codex.wordpress.org/Function_Reference/get_users
     * @return bool|void
     */
    private function loginUser() {

        // We look for the `eo_facebook_id` to see if there is any match
        $wp_users = get_users(array(
            'meta_key'     => 'starreview_facebook_id',
            'meta_value'   => $this->facebook_details['id'],
            'number'       => 1,
            'count_total'  => false,
            'fields'       => 'id',
        ));

        if(empty($wp_users[0])) {
            return false;
        }

        // Log the user ?
        wp_set_auth_cookie( $wp_users[0] );
        wp_set_current_user($wp_users[0]);
        //do redirect  here....
        wp_safe_redirect($this->redirect_url.'#respond');
        die();

    }

    /**
     * Create a new WordPress account using Facebook Details
     */
    private function createUser() {


        $fb_user = $this->facebook_details;

        // Create an username
        $username = sanitize_user(str_replace(' ', '_', strtolower($this->facebook_details['name'])));

        // Creating our user
        $new_user = wp_create_user($username, wp_generate_password(), $fb_user['email']);

        if(is_wp_error($new_user)) {
            // Report our errors
            $_SESSION['starreview_facebook_message'] = $new_user->get_error_message();
            // Redirect
            header("Location: ".$this->redirect_url, true);
            die();
        }

        // Setting the meta
        update_user_meta( $new_user, 'first_name', $fb_user['first_name'] );
        update_user_meta( $new_user, 'last_name', $fb_user['last_name'] );
        update_user_meta( $new_user, 'user_url', $fb_user['link'] );
        update_user_meta( $new_user, 'starreview_facebook_id', $fb_user['id'] );
        $user_id = (int) $new_user;
        global $wpdb;
        $wpdb->query('UPDATE wp_users SET user_status = 1 WHERE ID = '.$user_id);
        // Log the user ?
        wp_set_auth_cookie( $new_user );
        wp_set_current_user($new_user);
        //do redirect  here....
        wp_safe_redirect($this->redirect_url.'#respond');
        die();

    }


}
new StarPRSfacebook();