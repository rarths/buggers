<?php
/**
 * Config-file for navigation bar.
 *
 */

$loggedInUser = $this->di->AuthController->getLoggedInUser();

$notice = [];
$login = [];
$notice = [
    'text'  => "Register",
    'title' => "Click here to register",
    'url'   => $this->di->get('url')->create('register'),
    'class' => 'right-panel'
];
$login = [
    'text'  => "Login",
    'title' => "Click here to login",
    'url'   => $this->di->get('url')->create('login'),
    'class' => 'right-panel'
];

if ($loggedInUser) {
    $notice = [
        'text' => "Hello, " . $loggedInUser['name'],
        'url' => $this->di->get('url')->create('users/id/' . $loggedInUser['id']),
        'title' => 'Click here to go to your profile',
        'class' => 'right-panel notice'
    ];
    $login = [
        'text'  => 'Logout',
        'url'   => $this->di->get('url')->create('logout'),
        'title' => 'Click here to logout, maybe bye bye?',
        'class' => 'right-panel login'
    ];
}

return [

    // Use for styling the menu
    'class' => 'navbar',
 
    // Here comes the menu strcture
    'items' => [
 
        // This is a menu item
        'reports' => [
            'text'  =>'Reports',
            'url'   => $this->di->get('url')->create('reports'),
            'title' => 'Let everyone know about your fresh bug!'
        ],

        // This is a menu item
        'tags' => [
            'text'  =>'Tags',
            'url'   => $this->di->get('url')->create('tags'),
            'title' => 'Find reports about specific genre'
        ],

        // This is a menu item
        'users' => [
            'text'  =>'Users',
            'url'   => $this->di->get('url')->create('users'),
            'title' => 'All current active users'
        ],

        // This is a menu item
        'about' => [
            'text'  =>'About',
            'url'   => $this->di->get('url')->create('about'),
            'title' => 'About buggers and the developer',
        ],

        // This is a menu item
        'login' => $login,

        // This is a menu item
        'notice' => $notice,
    ],

 

    /**
     * Callback tracing the current selected menu item base on scriptname
     *
     */
    'callback' => function ($url) {
        if ($url == $this->di->get('request')->getCurrentUrl(false)) {
            return true;
        }
    },


    /**
     * Callback to check if current page is a decendant of the menuitem, this check applies for those
     * menuitems that has the setting 'mark-if-parent' set to true.
     *
     */
    'is_parent' => function ($parent) {
        $route = $this->di->get('request')->getRoute();
        return !substr_compare($parent, $route, 0, strlen($parent));
    },



   /**
     * Callback to create the url, if needed, else comment out.
     *
     */
   /*
    'create_url' => function ($url) {
        return $this->di->get('url')->create($url);
    },
    */
];