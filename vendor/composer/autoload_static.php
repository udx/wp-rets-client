<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit767149ad881f3a01e76e00619c0a3d05
{
    public static $classMap = array (
        'UsabilityDynamics\\Job' => __DIR__ . '/..' . '/usabilitydynamics/lib-utility/lib/class-job.php',
        'UsabilityDynamics\\Loader' => __DIR__ . '/..' . '/usabilitydynamics/lib-utility/lib/class-loader.php',
        'UsabilityDynamics\\Structure' => __DIR__ . '/..' . '/usabilitydynamics/lib-utility/lib/class-structure.php',
        'UsabilityDynamics\\Term' => __DIR__ . '/..' . '/usabilitydynamics/lib-utility/lib/class-term.php',
        'UsabilityDynamics\\Utility' => __DIR__ . '/..' . '/usabilitydynamics/lib-utility/lib/class-utility.php',
        'UsabilityDynamics\\Utility\\Guid_Fix' => __DIR__ . '/..' . '/usabilitydynamics/lib-utility/lib/class-guid-fix.php',
        'UsabilityDynamics\\WPRETSC\\Bootstrap' => __DIR__ . '/../..' . '/lib/classes/class-Bootstrap.php',
        'UsabilityDynamics\\WP\\Bootstrap' => __DIR__ . '/..' . '/usabilitydynamics/lib-wp-bootstrap/lib/classes/class-bootstrap.php',
        'UsabilityDynamics\\WP\\Bootstrap_Plugin' => __DIR__ . '/..' . '/usabilitydynamics/lib-wp-bootstrap/lib/classes/class-bootstrap-plugin.php',
        'UsabilityDynamics\\WP\\Bootstrap_Theme' => __DIR__ . '/..' . '/usabilitydynamics/lib-wp-bootstrap/lib/classes/class-bootstrap-theme.php',
        'UsabilityDynamics\\WP\\Dashboard' => __DIR__ . '/..' . '/usabilitydynamics/lib-wp-bootstrap/lib/classes/class-dashboard.php',
        'UsabilityDynamics\\WP\\Errors' => __DIR__ . '/..' . '/usabilitydynamics/lib-wp-bootstrap/lib/classes/class-errors.php',
        'UsabilityDynamics\\WP\\Scaffold' => __DIR__ . '/..' . '/usabilitydynamics/lib-wp-bootstrap/lib/classes/class-scaffold.php',
        'UsabilityDynamics\\WP\\TGMPA_List_Table' => __DIR__ . '/..' . '/usabilitydynamics/lib-wp-bootstrap/lib/classes/class-tgm-list-table.php',
        'UsabilityDynamics\\WP\\TGM_Bulk_Installer' => __DIR__ . '/..' . '/usabilitydynamics/lib-wp-bootstrap/lib/classes/class-tgm-bulk-installer.php',
        'UsabilityDynamics\\WP\\TGM_Bulk_Installer_Skin' => __DIR__ . '/..' . '/usabilitydynamics/lib-wp-bootstrap/lib/classes/class-tgm-bulk-installer.php',
        'UsabilityDynamics\\WP\\TGM_Plugin_Activation' => __DIR__ . '/..' . '/usabilitydynamics/lib-wp-bootstrap/lib/classes/class-tgm-plugin-activation.php',
        'UsabilityDynamics\\WP\\Utility' => __DIR__ . '/..' . '/usabilitydynamics/lib-wp-bootstrap/lib/classes/class-utility.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInit767149ad881f3a01e76e00619c0a3d05::$classMap;

        }, null, ClassLoader::class);
    }
}