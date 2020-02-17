<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitc196385642630abd034b013cc762f769
{
    public static $prefixLengthsPsr4 = array (
        'F' => 
        array (
            'Falcon\\' => 7,
        ),
        'C' => 
        array (
            'Composer\\Installers\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Falcon\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        'Composer\\Installers\\' => 
        array (
            0 => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitc196385642630abd034b013cc762f769::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitc196385642630abd034b013cc762f769::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}