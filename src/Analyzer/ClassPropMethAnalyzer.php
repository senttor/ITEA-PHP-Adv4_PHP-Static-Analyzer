<?php

namespace Greeflas\StaticAnalyzer\Analyzer;

use Greeflas\StaticAnalyzer\PhpClassInfo;
use Symfony\Component\Finder\Finder;


final class ClassPropMethAnalyzer
{
    private $className;
    public $propeties_methods = [];
    public static $xxx;
    private static $yyy;
    private static $zzz;

    public function __construct(string $className)
    {
        $this->className = $className;
    }

    /**
     * method for analize CLASS structure.
     *
     * @return array
     *
     * @throws \ReflectionException
     */
    public function analyze(): array
    {
        $finder = Finder::create()
            ->in(__DIR__.'/..')
            ->files()
            ->name('/^'.$this->className.'.php$/');

        // $counter = 0;

        foreach ($finder as $file) {
            $namespace = PhpClassInfo::getFullClassName($file->getPathname());

            try {
                $reflector = new \ReflectionClass($namespace);
            } catch (\ReflectionException $e) {
                continue;
            }
            //Get type class
            if ($reflector->isAbstract()) {
                $propeties_methods['classtype'] = 'absract';
            } elseif ($reflector->isFinal()) {
                $propeties_methods['classtype'] = 'final';
            } else {
                $propeties_methods['classtype'] = 'default';
            }
           //Check propeties accessibility modifiers for current instance
            $propeties_methods['propeties']['public'] = $reflector->getProperties(\ReflectionMethod::IS_PUBLIC) ?? null;
            $propeties_methods['propeties']['protected'] = $reflector->getProperties(\ReflectionMethod::IS_PROTECTED) ?? null;
            $propeties_methods['propeties']['static'] = $reflector->getProperties(\ReflectionMethod::IS_STATIC) ?? null;
            $propeties_methods['propeties']['private'] = $reflector->getProperties(\ReflectionMethod::IS_PRIVATE) ?? null;

          //Check methods accessibility modifiers for current instance
            $propeties_methods['methods']['public'] = $reflector->getMethods(\ReflectionMethod::IS_PUBLIC) ?? null;
            $propeties_methods['methods']['static'] = $reflector->getMethods(\ReflectionMethod::IS_STATIC) ?? null;
            $propeties_methods['methods']['protected'] = $reflector->getMethods(\ReflectionMethod::IS_PROTECTED) ?? null;
            $propeties_methods['methods']['private'] = $reflector->getMethods(\ReflectionMethod::IS_PRIVATE) ?? null;
        }

        //Determine the use of a static modifier
        $propeties_methods['propeties']['pub_static'] = \array_intersect($propeties_methods['propeties']['static'], $propeties_methods['propeties']['public']);
        $propeties_methods['propeties']['prot_static'] = \array_intersect($propeties_methods['propeties']['static'], $propeties_methods['propeties']['protected']);

        $propeties_methods['methods']['pub_static'] = \array_intersect($propeties_methods['methods']['static'], $propeties_methods['methods']['public']);
        $propeties_methods['methods']['priv_static'] = \array_intersect($propeties_methods['methods']['static'], $propeties_methods['methods']['private']);

        return $propeties_methods;
    }
}
