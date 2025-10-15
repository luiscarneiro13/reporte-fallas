<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    |
    | Here you can change the default title of your admin panel.
    |
    | For detailed instructions you can look the title section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'title' => 'Servicios Generales Casmar, C.A',
    'title_prefix' => '',
    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Favicon
    |--------------------------------------------------------------------------
    |
    | Here you can activate the favicon.
    |
    | For detailed instructions you can look the favicon section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_ico_only' => false,
    'use_full_favicon' => false,

    /*
    |--------------------------------------------------------------------------
    | Google Fonts
    |--------------------------------------------------------------------------
    |
    | Here you can allow or not the use of external google fonts. Disabling the
    | google fonts may be useful if your admin panel internet access is
    | restricted somehow.
    |
    | For detailed instructions you can look the google fonts section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'google_fonts' => [
        'allowed' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Logo
    |--------------------------------------------------------------------------
    |
    | Here you can change the logo of your admin panel.
    |
    | For detailed instructions you can look the logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'logo' => '<b>Reporte de fallas</b>',
    // 'logo_img' =>  null,
    'logo_img_class' => 'brand-image img-circle elevation-3',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => 'Reporte de fallas',
    'locale' => 'es',
    /*
    |--------------------------------------------------------------------------
    | Authentication Logo
    |--------------------------------------------------------------------------
    |
    | Here you can setup an alternative logo to use on your login and register
    | screens. When disabled, the admin panel logo will be used instead.
    |
    | For detailed instructions you can look the auth logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'auth_logo' => [
        'enabled' => false,
        'img' => [
            'path' => 'logo.webp',
            'alt' => 'Auth Logo',
            'class' => '',
            'width' => 50,
            'height' => 50,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Preloader Animation
    |--------------------------------------------------------------------------
    |
    | Here you can change the preloader animation configuration.
    |
    | For detailed instructions you can look the preloader section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'preloader' => [
        'enabled' => false,
        'img' => [
            'path' => 'vendor/adminlte/dist/img/sistelconet.png',
            'alt' => 'AdminLTE Preloader Image',
            'effect' => 'animation__shake',
            'width' => 60,
            'height' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Menu
    |--------------------------------------------------------------------------
    |
    | Here you can activate and change the user menu.
    |
    | For detailed instructions you can look the user menu section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'usermenu_enabled' => true,
    'usermenu_header' => false,
    'usermenu_header_class' => 'bg-primary',
    'usermenu_image' => true,
    'usermenu_desc' => true,
    'usermenu_profile_url' => true,

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Here we change the layout of your admin panel.
    |
    | For detailed instructions you can look the layout section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => null,
    'layout_fixed_navbar' => null,
    'layout_fixed_footer' => null,
    'layout_dark_mode' => null,

    /*
    |--------------------------------------------------------------------------
    | Authentication Views Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the authentication views.
    |
    | For detailed instructions you can look the auth classes section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_auth_card' => 'card-outline card-primary',
    'classes_auth_header' => '',
    'classes_auth_body' => '',
    'classes_auth_footer' => 'text-center',
    'classes_auth_icon' => 'fa-fw text-dark',
    'classes_auth_btn' => 'btn-flat btn-primary',

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the admin panel.
    |
    | For detailed instructions you can look the admin panel classes here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_body' => '',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_wrapper' => '',
    'classes_content_header' => '',
    'classes_content' => '',
    'classes_sidebar' => 'sidebar-light-primary elevation-4',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-white navbar-light',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_topnav_container' => 'container',

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar of the admin panel.
    |
    | For detailed instructions you can look the sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'sidebar_mini' => 'lg',
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    /*
    |--------------------------------------------------------------------------
    | Control Sidebar (Right Sidebar)
    |--------------------------------------------------------------------------
    |
    | Here we can modify the right sidebar aka control sidebar of the admin panel.
    |
    | For detailed instructions you can look the right sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Here we can modify the url settings of the admin panel.
    |
    | For detailed instructions you can look the urls section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_route_url' => false,
    'dashboard_url' => 'home',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => false,

    /*
    |--------------------------------------------------------------------------
    | Laravel Mix
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Laravel Mix option for the admin panel.
    |
    | For detailed instructions you can look the laravel mix section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'enabled_laravel_mix' => false,
    'laravel_mix_css_path' => 'css/app.css',
    'laravel_mix_js_path' => 'js/app.js',

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar/top navigation of the admin panel.
    |
    | For detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'menu' => [
        // Navbar items:
        [
            'type'         => 'navbar-search',
            'text'         => 'search',
            'topnav_right' => true,
        ],
        [
            'type'         => 'fullscreen-widget',
            'topnav_right' => true,
        ],
        [
            'text' => 'blog',
            'url'  => 'admin/blog',
            'can'  => 'manage-blog',
            'icon' => 'fas fa-newspaper',
        ],
        // --- Menús Principales ---
        [
            'text' => 'Resumen de fallas',
            'icon' => 'fas fa-tachometer-alt',
            'route'  => 'admin.sucursal.faults.index',
        ],
        [
            'text' => 'Reportar falla',
            'icon' => 'fas fa-bug',
            'route'  => 'admin.sucursal.faults.create',
        ],
        [
            'text' => 'Equipos',
            'icon' => 'fas fa-truck',
            'route'  => 'admin.sucursal.equipment.index',
        ],
        [
            'text' => 'Ejecutores',
            'icon' => 'fas fa-users',
            'route'  => 'admin.sucursal.executors.index',
        ],
        // --- Administración General ---
        [
            'text' => 'Administración',
            'icon' => 'fas fa-cogs',
            'can' => 'Menu Conf Admin',
            'submenu' => [
                [
                    'text' => 'Editar mi Empresa',
                    'icon' => 'fas fa-building',
                    'active' => ['v1/admin/mi-sucursal/edit*'],
                    'url' => '/v1/admin/mi-sucursal/edit',
                ],
                [
                    'text' => 'Divisiones de la Empresa',
                    'icon' => 'fas fa-code-branch',
                    'active' => ['v1/admin/divisions*'],
                    'route' => 'admin.sucursal.divisions.index',
                ],
                [
                    'text' => 'Status de fallas',
                    'icon' => 'fas fa-clipboard-list',
                    'active' => ['v1/admin/status-fallas*'],
                    'route' => 'admin.sucursal.fault.statuses.index',
                ],
                [
                    'text' => 'Status de repuestos',
                    'icon' => 'fas fa-boxes',
                    'active' => ['v1/admin/status-repuestos*'],
                    'route' => 'admin.sucursal.spare.part.statuses.index',
                ],
                [
                    'text' => 'Áreas de Servicio',
                    'icon' => 'fas fa-map-marked-alt',
                    'active' => ['v1/admin/areas-de-servicio*'],
                    'route' => 'admin.sucursal.service.areas.index',
                ],
                [
                    'text' => 'Conf. Admin',
                    'icon' => 'fas fa-sliders-h',
                ],
                [
                    'text' => 'Clientes',
                    'icon' => 'fas fa-users',
                    'active' => ['v1/admin/clientes*'],
                    'route' => 'admin.sucursal.customers.index',
                    'can' => 'Clientes Ver',
                ],
                [
                    'text' => 'Proyectos',
                    'icon' => 'fas fa-project-diagram',
                    'active' => ['v1/admin/proyectos*'],
                    'route' => 'admin.sucursal.projects.index',
                    'can' => 'Proyectos Ver',
                ],
                [
                    'text' => 'Empleados',
                    'icon' => 'fas fa-users',
                    'route'  => 'admin.sucursal.employees.index',
                ],
            ]
        ],
        // --- Gestión de Usuarios ---
        [
            'text' => 'Usuarios de sistema',
            'icon' => 'fas fa-user-friends', // 🧑‍🤝‍🧑 Gestión de Usuarios
            'can' => 'Menu Conf Admin',
            'submenu' => [
                [
                    'text' => 'Administradores',
                    'icon' => 'fas fa-user-shield', // 🛡️ Admin
                    'active' => ['v1/admin/administradores*'],
                    'route'  => 'admin.sucursal.usuarios.administradores.index',
                ],
                [
                    'text' => 'Supervisores',
                    'icon' => 'fas fa-headset', // 🎧 Supervisor / Soporte
                    'active' => ['v1/admin/supervisores*'],
                    'route'  => 'admin.sucursal.usuarios.supervisors.index',
                ],
                [
                    'text' => 'Operadores',
                    'icon' => 'fas fa-wrench', // 🔧 Operador / Técnico
                    'active' => ['v1/admin/operadores*'],
                    'route'  => 'admin.sucursal.usuarios.operators.index',
                ],
            ]
        ],
        // --- Ajustes de Cuenta ---
        [
            'can' => 'Menu Ajustes de Cuenta',
            'text' => 'Ajustes de Cuenta',
            'icon' => 'fas fa-user-cog', // 👤⚙️ Ajustes de Cuenta (Combinado)
            'submenu' => [
                [
                    'text' => 'Perfil',
                    'url'  => 'user/profile',
                    'icon' => 'fas fa-fw fa-user', // 👤 Perfil
                ],
                [
                    'text' => 'Cambiar Contraseña',
                    'url'  => 'admin/settings',
                    'icon' => 'fas fa-fw fa-lock', // 🔒 Contraseña
                ],
            ]
        ],
        // --- Configuración Super Admin ---
        [
            'text' => 'Conf. Super Admin',
            'icon' => 'fas fa-user-secret', // 🕵️ Super Admin / Privilegios
            'can' => 'Menu Conf Super Admin',
            'submenu' => [
                [
                    'text' => 'Roles',
                    'route'  => 'roles.index',
                    'icon' => 'fas fa-user-tag', // 🏷️ Roles
                ],
                [
                    'text' => 'Permisos',
                    'route'  => 'permissions.index',
                    'icon' => 'fas fa-user-lock', // 🔑 Permisos
                ],
                [
                    'text' => 'Usuarios',
                    'icon' => 'fas fa-fw fa-users', // 👥 Usuarios
                    'active' => ['assign_role*'],
                    'submenu' => [
                        [
                            'text' => 'Lista de Usuarios',
                            'icon' => 'fas fa-list', // 📋 Lista
                            'route'  => 'assign_role.index',
                        ],
                        [
                            'text' => 'Nuevo Usuario',
                            'icon' => 'fas fa-user-plus', // ➕ Nuevo Usuario
                            'route'  => 'users.create',
                        ]
                    ]
                ],
                [
                    'text' => 'Sucursales',
                    'icon' => 'fas fa-industry', // 🏭 Sucursales (Fábricas/Oficinas)
                    'active' => ['branches*'],
                    'submenu' => [
                        [
                            'text' => 'Lista de Sucursales',
                            'icon' => 'fas fa-list', // 📋 Lista
                            'route'  => 'branches.index',
                        ],
                        [
                            'text' => 'Nueva Sucursal',
                            'icon' => 'fas fa-plus-circle', // ➕ Nueva Sucursal
                            'route'  => 'branches.create',
                        ]
                    ]
                ],
            ]
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Here we can modify the menu filters of the admin panel.
    |
    | For detailed instructions you can look the menu filters section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    |
    | Here we can modify the plugins used inside the admin panel.
    |
    | For detailed instructions you can look the plugins section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Plugins-Configuration
    |
    */

    'plugins' => [
        'Datatables' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css',
                ],
            ],
        ],
        'Select2' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css',
                ],
            ],
        ],
        'Chartjs' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                ],
            ],
        ],
        'Sweetalert2' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.jsdelivr.net/npm/sweetalert2@11',
                ],
            ],
        ],
        'Pace' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                ],
            ],
        ],
        'Custom' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '/vendor/adminlte/dist/css/custom.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '/vendor/adminlte/dist/js/custom.js',
                ],
            ],
        ],
        'DateRangePicker' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/moment/moment.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/daterangepicker/daterangepicker.js',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'vendor/daterangepicker/daterangepicker.css',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/moment/locale/es.js',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | IFrame
    |--------------------------------------------------------------------------
    |
    | Here we change the IFrame mode configuration. Note these changes will
    | only apply to the view that extends and enable the IFrame mode.
    |
    | For detailed instructions you can look the iframe mode section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/IFrame-Mode-Configuration
    |
    */

    'iframe' => [
        'default_tab' => [
            'url' => null,
            'title' => null,
        ],
        'buttons' => [
            'close' => true,
            'close_all' => true,
            'close_all_other' => true,
            'scroll_left' => true,
            'scroll_right' => true,
            'fullscreen' => true,
        ],
        'options' => [
            'loading_screen' => 1000,
            'auto_show_new_tab' => true,
            'use_navbar_items' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Livewire
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Livewire support.
    |
    | For detailed instructions you can look the livewire here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'livewire' => false,
];
