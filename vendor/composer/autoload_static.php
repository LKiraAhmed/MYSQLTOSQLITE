<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit0f79368380ee2756595a8ff52f03dc6c
{
    public static $prefixLengthsPsr4 = array (
        's' => 
        array (
            'src\\' => 4,
        ),
        'L' => 
        array (
            'LCode\\DatabaseMigrator\\' => 23,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'src\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        'LCode\\DatabaseMigrator\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit0f79368380ee2756595a8ff52f03dc6c::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit0f79368380ee2756595a8ff52f03dc6c::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit0f79368380ee2756595a8ff52f03dc6c::$classMap;

        }, null, ClassLoader::class);
    }
}
