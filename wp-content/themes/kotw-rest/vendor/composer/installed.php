<?php return array(
    'root' => array(
        'pretty_version' => 'dev-master',
        'version' => 'dev-master',
        'type' => 'wordpress-theme',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'reference' => 'e26cf7210b6bb9ba3b2f857c644553b2fe417012',
        'name' => 'kotw/kotw-rest',
        'dev' => true,
    ),
    'versions' => array(
        'kotw/kotw-rest' => array(
            'pretty_version' => 'dev-master',
            'version' => 'dev-master',
            'type' => 'wordpress-theme',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'reference' => 'e26cf7210b6bb9ba3b2f857c644553b2fe417012',
            'dev_requirement' => false,
        ),
        'kotw/kotw-wp' => array(
            'pretty_version' => 'dev-master',
            'version' => 'dev-master',
            'type' => 'wordpress-muplugin',
            'install_path' => __DIR__ . '/../kotw/kotw-wp',
            'aliases' => array(
                0 => '9999999-dev',
            ),
            'reference' => 'd1d00fa6e7d0f4a12d7541d52bf1864afc87f629',
            'dev_requirement' => false,
        ),
    ),
);
