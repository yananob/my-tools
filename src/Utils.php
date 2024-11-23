<?php

declare(strict_types=1);

namespace yananob\mytools;

final class Utils
{
    public function __construct()
    {
    }

    public static function getConfig(string $path, bool $asArray = true): array|object
    {
        $contents = file_get_contents($path);
        if ($contents == false) {
            throw new \Exception("Could not read config file: {$path}");
        }
        $result = json_decode($contents, $asArray);
        if (is_null($result)) {
            throw new \Exception("Failed to parse config file: {$path}");
        }
        return $result;
    }

    public static function invokePrivateMethod(Object $object, string $methodName, ...$params)
    {
        $reflection = new \ReflectionClass($object);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        $result = $method->invoke($object, ...$params);
        return $result;
    }

    public static function sortObjectArrayByProperty(array $ary, string $property): array
    {
        usort(
            $ary,
            function ($a, $b) use ($property) {
                if ($a->$property == $b->$property) return 0;
                return ($a->$property < $b->$property) ? -1 : 1;
            }
        );
        return $ary;
    }
}
