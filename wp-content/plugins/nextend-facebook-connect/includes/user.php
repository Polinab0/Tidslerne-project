<?php

use NSL\Notices;
use NSL\Persistent\Persistent;

require_once(NSL_PATH . '/includes/userData.php');

class NextendSocialUser {

    /** @var NextendSocialProvider */
    protected $provider;

    protected $data;

    private $userExtraData;

    protected $user_id;

    protected $shouldAutoLogin = false;

    /**
     * NextendSocialUser constructor.
     *
     * @param NextendSocialProvider $provider
     * @param                       $data
     */
    public function __construct($provider, $data) {
        $this->provider = $provider;
        $this->data     = $data;
    }

    /**
     * @param $key
     * $key is like id, email, name, first_name, last_name
     * Returns a single userdata of the current provider or empty string if $key is invalid.
     *
     * @return string
     */
    public function getAuthUserData($key) {
        return $this->provider->getAuthUserData($key);
    }

    /**
     * Connect with a Provider
     * If the user is not logged in
     * - and has no linked social data (in wp_social_users table), prepare them for register. - do not register if the
     * social email is not verified
     * - but if has linked social data, log them in.
     * If the user is logged in, retrieve the user data,
     * - if the user has no linked social data with the selected provider and there is no other user who linked that id
     * , link them and sync the access_token if it is available.
     */
    public function liveConnectGetUserProfile() {

        $user_id = $this->provider->getUserIDByProviderIdentifier($this->getAuthUserData('id'));
        if ($user_id !== null && !get_user_by('id', $user_id)) {
            $this->provider->removeConnectionByUserID($user_id);
            $user_id = null;
        }

        $this->addProfileSyncActions();

        if (!is_user_logged_in()) {
            if ($user_id == null) {
                $this->prepareRegister();
            } else {
                $this->login($user_id);
            }
        } else {
            $current_user = wp_get_current_user();
            if ($user_id === null) {
                // Let's connect the account to the current user!

                if ($this->provider->linkUserToProviderIdentifier($current_user->ID, $this->getAuthUserData('id'))) {
                    Notices::addSuccess(sprintf(__('Your %1$s account is successfully linked with your account. Now you can sign in with %2$s easily.', 'nextend-facebook-connect'), $this->provider->getLabel(), $this->provider->getLabel()));
                } else {
                    Notices::addError(sprintf(__('You have already linked a(n) %s account. Please unlink the current and then you can link another %s account.', 'nextend-facebook-connect'), $this->provider->getLabel(), $this->provider->getLabel()));
                }

            } else if ($current_user->ID != $user_id) {
                Notices::addError(sprintf(__('This %s account is already linked to another user.', 'nextend-facebook-connect'), $this->provider->getLabel()));
            }
        }
    }

    /**
     * Prepares the registration and registers the user.
     * If the email is not registered yet, checks if register is enabled, call register() function. - Do not register
     * if the email is not verified.
     * If the email is already registered, checks if autolink is enabled, if it is, log
     * the user in.
     * Autolink enabled: if the email is verified, links the current provider account with the existing social account
     * and attempts to log in. Autolink disabled: Add error with already registered email message.
     */
    protected function prepareRegister() {

        $user_id = false;

        $nslLoginUrl = NextendSocialLogin::getLoginUrl();

        $providerUserID = $this->getAuthUserData('id');

        $email = '';
        if (NextendSocialLogin::$settings->get('store_email') == 1) {
            $email = $this->getAuthUserData('email');
        }

        if (empty($email)) {
            $email = '';
        } else {
            $user_id_found = email_exists($email);
            /**
             * email_exists overrides could cause problems during the linking -> we should check if the returned ID is has integer type and if we are able to find an account with that ID.
             */
            if (is_int($user_id_found) && get_userdata($user_id_found)) {
                $user_id = $user_id_found;
            }
        }

        /**
         * Can be used for overriding the account where the social account should be automatically linked to.
         */
        $user_id = apply_filters('nsl_match_social_account_to_user_id', $user_id, $this, $this->provider);

        if ($user_id === false) { // Real register
            if (apply_filters('nsl_is_register_allowed', true, $this->provider)) {
                $this->register($providerUserID, $email);
            } else {
                //unset the persistent data, so if an error happened, the user can re-authenticate with providers (Google) that offer account selector screen
                $this->provider->deleteTokenPersistentData();

                $registerDisabledMessage = apply_filters('nsl_disabled_register_error_message', '');

                $registerDisabledRedirectURL = apply_filters('nsl_disabled_register_redirect_url', '');

                $defaultDisabledMessage = __('User registration is currently not allowed.');

                $proxyPage = NextendSocialLogin::getProxyPage();
                if ($proxyPage) {
                    if (empty($registerDisabledMessage) && $registerDisabledMessage !== false) {
                        /**
                         * There is no custom message and proxy page is used, so we need to inform the user with our own message.
                         */
                        $registerDisabledMessage = $defaultDisabledMessage;
                    }
                } else {
                    if (empty($registerDisabledMessage) && $registerDisabledMessage !== false) {
                        if (!empty($registerDisabledRedirectURL)) {
                            /**
                             * There is no custom message and it is a custom redirect url, so we need to inform the user with our own message.
                             */
                            $registerDisabledMessage = $defaultDisabledMessage;
                        }
                    } else {
                        if (empty($registerDisabledRedirectURL)) {
                            /**
                             * By default WordPress displays an error message if the $_GET['registration'] is set to "disabled"
                             * To avoid displaying the default and the custom error message, the url should not contain it.
                             */
                            $registerDisabledRedirectURL = $nslLoginUrl;
                        }
                    }
                }

                if (!empty($registerDisabledMessage)) {
                    $errors = new WP_Error();
                    $errors->add('registerdisabled', $registerDisabledMessage);
                    Notices::addError($errors->get_error_message());
                }

                if (empty($registerDisabledRedirectURL)) {
                    $registerDisabledRedirectURL = add_query_arg('registration', 'disabled', $nslLoginUrl);
                }

                $this->provider->redirectWithAuthenticationError($registerDisabledRedirectURL);
                exit;
            }
        } else {
            if ($this->autoLink($user_id, $providerUserID)) {
                $this->login($user_id);
            } else {
                $autolinkErrorRedirectURL = apply_filters('nsl_autolink_error_redirect_url', $nslLoginUrl);
                $this->provider->redirectWithAuthenticationError($autolinkErrorRedirectURL);
                exit;
            }
        }

        $this->provider->redirectToLoginForm();
    }

    /**
     * @param $username
     * Makes the username in an appropriate format. Removes white space and some special characters.
     * Also turns it into lowercase. And put a prefix before the username if user_prefix is set.
     * If this formated username is valid returns it, else return false.
     *
     * @return bool|string
     */
    protected function sanitizeUserName($username) {
        if (empty($username)) {
            return false;
        }

        $username = strtolower($username);

        $username = preg_replace('/\s+/', '', $username);

        /**
         * We have to check if the username itself is valid, otherwise we will never use the fallback
         * as the prefix string will result in a valid prefixed username even if the username was empty.
         * Also "Ask Username on registration - When username is empty or invalid" will not be trigger either, since the username won't be invalid.
         *
         * @see NSLDEV-622
         */
        if (empty(sanitize_user($username, true))) {
            return false;
        }

        $sanitized_user_login = sanitize_user($this->provider->settings->get('user_prefix') . $username, true);

        if (empty($sanitized_user_login) || mb_strlen($sanitized_user_login) > 60 || !validate_username($sanitized_user_login)) {
            return false;
        }

        return $sanitized_user_login;
    }

    /**
     * @param $providerID
     * @param $email
     * Registers the user.
     *
     * @return bool
     */
    protected function register($providerID, $email) {

        NextendSocialLogin::$WPLoginCurrentFlow = 'register';

        $sanitized_user_login = false;

        if (NextendSocialLogin::$settings->get('store_name') == 1) {
            /**
             * First checks provided first_name & last_name if it is not available checks name if it is neither available checks secondary_name.
             */
            $sanitized_user_login = $this->sanitizeUserName($this->getAuthUserData('first_name') . $this->getAuthUserData('last_name'));
            if ($sanitized_user_login === false) {
                $sanitized_user_login = $this->sanitizeUserName($this->getAuthUserData('username'));
                if ($sanitized_user_login === false) {
                    $sanitized_user_login = $this->sanitizeUserName($this->getAuthUserData('name'));
                }
            }
        }

        $email = '';
        if (NextendSocialLogin::$settings->get('store_email') == 1) {
            $email = $this->getAuthUserData('email');
        }
        $userData = array(
            'email'    => $email,
            'username' => $sanitized_user_login
        );

        do_action('nsl_before_register', $this->provider);

        do_action('nsl_' . $this->provider->getId() . '_before_register');

        if (NextendSocialLogin::$settings->get('terms_show') == '1') {

            add_filter('nsl_registration_require_extra_input', array(
                $this,
                'require_extra_input_terms'
            ));
        }


        /** @var array $userData Validated user data */
        $userData = $this->finalizeUserData($userData);

        /**
         * -If neither of the usernames ( first_name & last_name, secondary_name) are appropriate, the fallback username will be combined with and id that was sent by the provider.
         * -In this way we can generate an appropriate username.
         */
        if (empty($userData['username'])) {
            $userData['username'] = sanitize_user($this->provider->settings->get('user_fallback') . md5(uniqid(rand())), true);
        }

        /**
         * If the username is already in use, it will get a number suffix, that is not registered yet.
         */
        $default_user_name = $userData['username'];
        $i                 = 1;
        while (username_exists($userData['username'])) {
            $userData['username'] = $default_user_name . $i;
            $i++;
        }

        /**
         * Generates a random password. And set the default_password_nag to true. So the user get notify about randomly generated password.
         */
        if (empty($userData['password'])) {
            $userData['password'] = wp_generate_password(12, false);

            add_action('user_register', array(
                $this,
                'registerCompleteDefaultPasswordNag'
            ));
        }
        /**
         * Preregister, checks what roles shall be informed about the registration and sends a notification to them.
         */
        do_action('nsl_pre_register_new_user', $this);

        $loginRestriction = NextendSocialLogin::$settings->get('login_restriction');
        if ($loginRestriction) {
            $errors = new WP_Error();

            //Prevent New User Approve registration before NSL registration
            if (class_exists('pw_new_user_approve', false)) {
                remove_action('register_post', array(
                    pw_new_user_approve::instance(),
                    'create_new_user'
                ), 10);
            }

            //Ultimate Member redirects before we update the Avatar, we need to sync before the redirect
            if (class_exists('UM', false)) {
                if ($this->provider->settings->get('sync_profile/login')) {
                    add_action('um_registration_after_auto_login', array(
                        $this,
                        'sync_profile_login'
                    ), 10);
                }
            }

            /*For TML 6.4.17 Register notification integration*/
            do_action('register_post', $userData['username'], $userData['email'], $errors);

            if ($errors->get_error_code()) {
                //unset the persistent data, so if an error happened, the user can re-authenticate with providers (Google) that offer account selector screen
                $this->provider->deleteTokenPersistentData();

                Notices::addError($errors);
                $this->redirectToLastLocationLogin(true);
            }
        }

        /**
         * Eduma theme user priority 1000 to auto log in users. We need to stay under that priority @see https://themeforest.net/item/education-wordpress-theme-education-wp/14058034
         * WooCommerce Follow-Up Emails use priority 10, so we need higher @see https://woocommerce.com/products/follow-up-emails/
         *
         * If there was no error during the registration process,
         * -links the user to the providerIdentifier ( wp_social_users table in database store this link ).
         * -set the roles for the user.
         * -login the user.
         */
        add_action('user_register', array(
            $this,
            'registerComplete'
        ), 31);

        $autoLoginPriority = apply_filters('nsl_autologin_priority', 40);
        add_action('user_register', array(
            $this,
            'doAutoLogin'
        ), $autoLoginPriority);


        $this->userExtraData = $userData;

        $user_data = array(
            'user_login' => wp_slash($userData['username']),
            'user_email' => wp_slash($userData['email']),
            'user_pass'  => $userData['password']
        );

        if (NextendSocialLogin::$settings->get('store_name') == 1) {
            $name = $this->getAuthUserData('name');
            if (!empty($name)) {
                $user_data['display_name'] = $name;
            }

            $first_name = $this->getAuthUserData('first_name');
            if (!empty($first_name)) {
                $user_data['first_name'] = $first_name;
            }

            $last_name = $this->getAuthUserData('last_name');
            if (!empty($last_name)) {
                $user_data['last_name'] = $last_name;
            }
        }

        //Prevent sending the Woocommerce User Email Verification notification if Login restriction is turned off.
        if (class_exists('XLWUEV_Core', false) && !$loginRestriction) {
            remove_action('user_register', array(
                XLWUEV_Woocommerce_Confirmation_Email_Public::instance(),
                'custom_form_user_register'
            ), 10);
            remove_action('woocommerce_created_customer_notification', array(
                XLWUEV_Woocommerce_Confirmation_Email_Public::instance(),
                'new_user_registration_from_registration_form'
            ), 10);
        }

        $externalInsertUserStatus = [
            'isExternalInsertUser' => false,
            'error'                => false
        ];
        /**
         * If the account is created outside of Nextend Social Login, then Nextend Social Login should be prevented from inserting the user again.
         * For this "isExternalInsertUser" needs to be set to true.
         * If an error happens in the external registration, then the error message can be displayed by setting "error" to a WP_ERROR object.
         */
        $externalInsertUserStatus = apply_filters('nsl_register_external_insert_user', $externalInsertUserStatus, $this, $user_data);
        $error                    = $externalInsertUserStatus['error'];

        if (!$externalInsertUserStatus['isExternalInsertUser']) {
            $error = wp_insert_user($user_data);
        }

        if (is_wp_error($error)) {
            $this->provider->deleteTokenPersistentData();
            Notices::addError($error);
            $this->redirectToLastLocationLogin(true);

        } else if ($error === 0) {
            $this->registerError();
            exit;
        }

        //registerComplete will log in user and redirects. If we reach here, the user creation failed.
        return false;
    }

    /**
     * By setting the default_password_nag to true, will inform the user about random password usage.
     */
    public function registerCompleteDefaultPasswordNag($user_id) {
        update_user_option($user_id, 'default_password_nag', true, true);
    }


    /**
     * @param $user_id
     * Retrieves the name, first_name, last_name and update the user data.
     * Also set a reminder to change the generated password.
     * Links the user with the provider. Set their roles. Send notification about the registration to the selected
     * roles. Logs the user in.
     *
     * @return bool
     */
    public function registerComplete($user_id) {
        if (is_wp_error($user_id) || $user_id === 0) {
            /** Registration failed */
            $this->registerError();

            return false;
        }

        if (class_exists('WooCommerce', false)) {
            if (NextendSocialLogin::$settings->get('store_name') == 1) {
                $first_name = $this->getAuthUserData('first_name');
                if (!empty($first_name)) {
                    update_user_meta($user_id, 'billing_first_name', $first_name);
                }

                $last_name = $this->getAuthUserData('last_name');
                if (!empty($last_name)) {
                    update_user_meta($user_id, 'billing_last_name', $last_name);
                }
            }
        }

        $this->provider->linkUserToProviderIdentifier($user_id, $this->getAuthUserData('id'), true);

        do_action('nsl_registration_store_extra_input', $user_id, $this->userExtraData);

        do_action('nsl_register_new_user', $user_id, $this->provider);
        do_action('nsl_' . $this->provider->getId() . '_register_new_user', $user_id, $this->provider);

        $this->provider->deleteLoginPersistentData();

        do_action('register_new_user', $user_id);

        //BuddyPress - add register activity to accounts registered with social login
        if (class_exists('BuddyPress', false)) {
            if (bp_is_active('activity')) {
                if (!function_exists('bp_core_new_user_activity')) {
                    require_once(buddypress()->plugin_dir . '/bp-members/bp-members-activity.php');
                }
                bp_core_new_user_activity($user_id);
            }
        }

        /*Ultimate Member Registration integration -> Registration notificationhoz*/
        $loginRestriction = NextendSocialLogin::$settings->get('login_restriction');
        if (class_exists('UM', false) && $loginRestriction) {
            //Necessary to clear the UM user cache that was generated by: um\core\User:set_gravatar
            UM()
                ->user()
                ->remove_cache($user_id);
            add_filter('um_get_current_page_url', array(
                $this,
                'um_get_loginpage'
            ));
            $um_registration_timestamp = current_time('timestamp');
            $um_registration_args      = array(
                'submitted' => array(
                    'timestamp' => $um_registration_timestamp
                ),
                'timestamp' => $um_registration_timestamp
            );
            $um_registration_form_data = array(
                'custom_fields' => ""
            );
            /**
             * Ultimate Member reads the data out of this meta field when it displays the user registration date at  the Users tabe > Info.
             */
            update_user_meta($user_id, 'timestamp', $um_registration_timestamp);
            do_action('um_user_register', $user_id, $um_registration_args, $um_registration_form_data);
        }


        //Woocommerce User Email Verification integration - By default it blocks login with NSL
        if (class_exists('XLWUEV_Core', false) && !$loginRestriction) {
            update_user_meta($user_id, 'wcemailverified', 'true');
        }

        $this->shouldAutoLogin = true;

        return true;
    }


    private function registerError() {
        /** @var $wpdb WPDB */ global $wpdb;

        $isDebug = NextendSocialLogin::$settings->get('debug') == 1;
        if ($isDebug) {
            if ($wpdb->last_error !== '') {
                echo "<div id='error'><p class='wpdberror'><strong>WordPress database error:</strong> [" . esc_html($wpdb->last_error) . "]<br /><code>" . esc_html($wpdb->last_query) . "</code></p></div>";
            }
        }

        $this->provider->deleteLoginPersistentData();

        if ($isDebug) {
            exit;
        }
    }

    protected function login($user_id) {
        /** @var $wpdb WPDB */ global $wpdb;

        $user = new WP_User($user_id);

        $loginRestriction = NextendSocialLogin::$settings->get('login_restriction');
        if ($loginRestriction) {
            $userOrError = apply_filters('authenticate', $user, $user->get('user_login'), '');
            if (is_wp_error($userOrError)) {
                Notices::addError($userOrError);
                do_action('wp_login_failed', $user->get('user_login'), $userOrError);

                $loginDisabledRedirectURL = apply_filters('nsl_disabled_login_redirect_url', NextendSocialLogin::getLoginUrl());
                $this->provider->redirectWithAuthenticationError($loginDisabledRedirectURL);

                return $userOrError;
            }

            /**
             * Other plugins use this hook to prevent log in
             */
            $userOrError = apply_filters('wp_authenticate_user', $user, '');
            if (is_wp_error($userOrError)) {
                Notices::addError($userOrError);
                do_action('wp_login_failed', $user->get('user_login'), $userOrError);

                $loginDisabledRedirectURL = apply_filters('nsl_disabled_login_redirect_url', NextendSocialLogin::getLoginUrl());
                $this->provider->redirectWithAuthenticationError($loginDisabledRedirectURL);

                return $userOrError;
            }
        }


        $this->user_id = $user_id;

        $isLoginAllowed = apply_filters('nsl_' . $this->provider->getId() . '_is_login_allowed', true, $this->provider, $user_id);

        if ($isLoginAllowed) {

            wp_set_current_user($user_id);

            $secure_cookie = is_ssl();
            $secure_cookie = apply_filters('secure_signon_cookie', $secure_cookie, array());
            global $auth_secure_cookie; // XXX ugly hack to pass this to wp_authenticate_cookie

            $auth_secure_cookie = $secure_cookie;
            wp_set_auth_cookie($user_id, true, $secure_cookie);
            $user_info = get_userdata($user_id);

            $this->provider->logLoginDate($user_id);

            $addStrongerRedirect = NextendSocialLogin::$settings->get('redirect_prevent_external') == 1 || $this->provider->hasFixedRedirect();
            if ($addStrongerRedirect) {
                /**
                 * If another plugin tries to redirect in wp_login action, we will intercept and use our redirects
                 */
                add_filter('wp_redirect', array(
                    $this,
                    'wp_redirect_filter'
                ), 10000000);

                /**
                 * Fix: WishList Member exits before our redirects.
                 */
                if (class_exists('WishListMember', false)) {
                    add_filter('wishlistmember_login_redirect_override', '__return_true');
                }
            }

            do_action('nsl_before_wp_login');
            do_action('wp_login', $user_info->user_login, $user_info);

            if ($addStrongerRedirect) {
                /**
                 * Remove redirect interception when not needed anymore
                 */
                remove_filter('wp_redirect', array(
                    $this,
                    'wp_redirect_filter'
                ), 10000000);
            }

            $this->finishLogin();
        } else {
            $this->provider->deleteLoginPersistentData();
            $loginDisabledMessage     = apply_filters('nsl_disabled_login_error_message', '');
            $loginDisabledRedirectURL = apply_filters('nsl_disabled_login_redirect_url', '');
            $errors                   = new WP_Error();
            $errors->add('logindisabled', $loginDisabledMessage);
            if (!empty($loginDisabledMessage)) {
                Notices::clear();
                Notices::addError($errors->get_error_message());
            }
            do_action('wp_login_failed', $user->get('user_login'), $errors);

            if (!empty($loginDisabledRedirectURL)) {
                $this->provider->redirectWithAuthenticationError($loginDisabledRedirectURL);
            }

        }

        $this->provider->redirectToLoginForm();
    }

    public function doAutoLogin($user_id) {
        if ($this->shouldAutoLogin) {
            $this->login($user_id);
        }
    }

    public function wp_redirect_filter($redirect) {
        $this->finishLogin();
        exit;
    }

    protected function finishLogin() {

        do_action('nsl_login', $this->user_id, $this->provider);
        do_action('nsl_' . $this->provider->getId() . '_login', $this->user_id, $this->provider, $this->data);

        $this->redirectToLastLocationLogin();
    }

    /**
     * Redirect the user to
     * -the Fixed redirect url if it is set
     * -where the login happened if redirect is specified in the url
     * -the Default redirect url if it is set, and if redirect was not specified in the url
     *
     * @param bool $notice
     */
    public function redirectToLastLocationLogin($notice = false) {

        if (NextendSocialLogin::$settings->get('redirect_prevent_external') == 0) {
            add_filter('nsl_' . $this->provider->getId() . 'default_last_location_redirect', array(
                $this,
                'loginLastLocationRedirect'
            ), 9, 2);
        }

        $this->provider->redirectToLastLocation($notice);
    }

    /**
     * @param $redirect_to
     * @param $requested_redirect_to
     * Modifies where the user shall be redirected, after successful login.
     *
     * @return mixed|void
     */
    public function loginLastLocationRedirect($redirect_to, $requested_redirect_to) {
        return apply_filters('login_redirect', $redirect_to, $requested_redirect_to, wp_get_current_user());
    }

    /**
     * @param $user_id
     * @param $providerUserID
     * If autoLink is enabled, it links the current account with the provider.
     *
     * @return bool
     */
    public function autoLink($user_id, $providerUserID) {
        $emailIsNotVerifiedMessage = apply_filters('nsl_auto_link_error_message_email_not_verified', sprintf(__('We found a user with your %1$s email address but it looks like its email address is not verified.%2$sPlease login to your existing account using your password and link your %1$s account to it manually!', 'nextend-facebook-connect'), $this->provider->getLabel(), '<br>'));
        $linkFailedMessage         = apply_filters('nsl_already_linked_error_message', sprintf(__('We found a user with your %1$s email address. Unfortunately, it belongs to a different account, so we are unable to log you in. Please use the linked account or log in with your password!', 'nextend-facebook-connect'), $this->provider->getLabel()));

        $isAutoLinkAllowed = apply_filters('nsl_' . $this->provider->getId() . '_auto_link_allowed', true, $this->provider, $user_id);

        if (!$isAutoLinkAllowed) {
            $this->provider->deleteLoginPersistentData();

            return false;
        }

        if (!$this->provider->getProviderEmailVerificationStatus()) {
            $this->provider->deleteLoginPersistentData();
            Notices::addError($emailIsNotVerifiedMessage);

            return false;
        }

        $isLinkSuccessful = $this->provider->linkUserToProviderIdentifier($user_id, $providerUserID);

        if ($isLinkSuccessful) {
            return $isLinkSuccessful;
        }

        $this->provider->deleteLoginPersistentData();
        Notices::addError($linkFailedMessage);

        return false;
    }

    /**
     * @return NextendSocialProvider
     */
    public function getProvider() {
        return $this->provider;
    }

    /**
     * @param $userData
     *
     * @return array
     * @throws NSLContinuePageRenderException
     */
    public function finalizeUserData($userData) {

        $data = new NextendSocialUserData($userData, $this, $this->provider);

        return $data->toArray();
    }

    public function require_extra_input_terms($askExtraData) {

        add_action('nsl_registration_form_end', array(
            $this,
            'registration_form_terms'
        ), 10000);

        return true;
    }

    public function registration_form_terms($userData) {
        ?>
        <p>
            <?php
            $terms = $this->provider->settings->get('terms');

            if (empty($terms)) {
                $terms = NextendSocialLogin::getPrivacyTerms();
            }

            $terms = __($terms, 'nextend-facebook-connect');

            if (function_exists('get_privacy_policy_url')) {
                $terms = str_replace('#privacy_policy_url', get_privacy_policy_url(), $terms);
            }

            echo $terms;

            ?>
        </p>
        <?php
    }

    public function syncProfileUser($user_id) {
        $this->provider->syncProfile($user_id, $this->provider, $this->data);
    }

    public function um_get_loginpage($page_url) {
        return um_get_core_page('login');
    }

    public function addProfileSyncActions() {
        if ($this->provider->settings->get('sync_profile/register')) {


            add_action('nsl_' . $this->provider->getId() . '_register_new_user', array(
                $this,
                'sync_profile_register_new_user'
            ), 10);
        }

        if ($this->provider->settings->get('sync_profile/login')) {
            add_action('nsl_' . $this->provider->getId() . '_login', array(
                $this,
                'sync_profile_login'
            ), 10);
        }

        if ($this->provider->settings->get('sync_profile/link')) {
            add_action('nsl_' . $this->provider->getId() . '_link_user', array(
                $this,
                'sync_profile_link_user'
            ), 10, 3);
        }
    }

    public function removeProfileSyncActions() {

        /** Prevent multiple profile sync in the same request */
        remove_action('nsl_' . $this->provider->getId() . '_register_new_user', array(
            $this,
            'sync_profile_register_new_user'
        ));

        remove_action('nsl_' . $this->provider->getId() . '_login', array(
            $this,
            'sync_profile_login'
        ));

        remove_action('nsl_' . $this->provider->getId() . '_link_user', array(
            $this,
            'sync_profile_link_user'
        ));
    }


    /**
     * @param $user_id
     */
    public function sync_profile_register_new_user($user_id) {

        $this->syncProfileUser($user_id);

        $this->removeProfileSyncActions();
    }

    /**
     * @param $user_id
     */
    public function sync_profile_login($user_id) {

        $this->syncProfileUser($user_id);

        $this->removeProfileSyncActions();
    }

    /**
     * @param $user_id
     * @param $providerIdentifier
     * @param $isRegister
     */
    public function sync_profile_link_user($user_id, $providerIdentifier, $isRegister) {

        /**
         * When the registration happens with social login, the linking happens before we trigger the register specific action.
         * This could make the profile being synced even if the registration specific action is disabled.
         */
        if (!$isRegister) {
            $this->syncProfileUser($user_id);

            $this->removeProfileSyncActions();
        }
    }
}