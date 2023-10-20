<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitUmpESh
{
    public static $prefixLengthsPsr4 = array (
        'U' => 
        array (
            'UmpESh\\Admin\\' => 13,
            'UmpESh\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'UmpESh\\Admin\\' => 
        array (
            0 => __DIR__ . '/../..' . '/classes/admin',
        ),
        'UmpESh\\' => 
        array (
            0 => __DIR__ . '/../..' . '/classes',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitUmpESh::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitUmpESh::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
