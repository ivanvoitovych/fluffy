<?php

namespace Fluffy\Domain\Hubs;

use DotDi\DependencyInjection\IServiceProvider;
use Exception;
use ReflectionMethod;
use Viewi\Common\JsonMapper;

class HubRunner
{
    public static function run(HubRouteItem $hubRoute, $data, IServiceProvider $services)
    {

        [$class, $method] = $hubRoute->action;
        $instance = $services->get($class, $hubRoute->params);
        if ($instance === null) {
            throw new Exception("Can't resolve an instance of {$class}");
        }

        $r = new ReflectionMethod($class, $method);
        $arguments = $r->getParameters();
        $inputs = [];
        $stdObject = is_object($data) ? $data : null;
        // $binaryData = false;
        $inputContent = $data;
        // if ($inputContent && $stdObject === null) {
        //     // binary/string data
        //     $binaryData = true;
        // }
        // print_r([$arguments, $stdObject]);

        foreach ($arguments as $argument) {
            $argName = $argument->getName();
            $argumentValue = isset($match['params'][$argName])
                ? $match['params'][$argName]
                : (isset($params[$argName]) ? $params[$argName] : null);
            // parse json body
            if ($argumentValue === null) {
                if ($argument->hasType() && !$argument->getType()->isBuiltin()) {
                    $typeName = $argument->getType()->getName();
                    if (class_exists($typeName)) {
                        $argumentValue = $services->get($typeName);
                        if ($argumentValue === null && $stdObject !== null) {
                            $argumentValue = JsonMapper::Instantiate($typeName, $stdObject);
                        }
                        // print_r([$argumentValue, $typeName]);
                    }
                } else if (isset($stdObject->$argName)) {
                    $argumentValue = $stdObject->$argName;
                } else if ($argName === 'data') {
                    $argumentValue = $stdObject;
                }
            }
            if ($argumentValue === null && $argName === 'data') {
                $argumentValue = $inputContent;
            } else if ($argumentValue === null && $argument->isDefaultValueAvailable()) {
                $argumentValue = $argument->getDefaultValue();
            }
            $inputs[] = $argumentValue;
        }
        return $instance->$method(...$inputs);
    }
}
