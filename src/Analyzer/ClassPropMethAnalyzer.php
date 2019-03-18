<?php

namespace Greeflas\StaticAnalyzer\Analyzer;

use Greeflas\StaticAnalyzer\PhpClassInfo;
use Symfony\Component\Finder\Finder;


final class ClassPropMethAnalyzer
{
    private $className;
    public $propeties_methods = [];
    public $propeties;
    public $methods;
    protected $public_modifier;
    protected $protected_modifier;
    protected $private_modifier;
    protected $static_modifier;
    protected $is_public;
    protected $is_protected;
    protected $is_private;
    protected $is_static;
    protected $pub_static;
    protected $prot_static;
    protected $priv_static;
    public $class_type;


    public function __construct(string $className)
    {
        $this->className = $className;
        $this->is_public = \ReflectionMethod::IS_PUBLIC;
        $this->is_protected = \ReflectionMethod::IS_PROTECTED;
        $this->is_private = \ReflectionMethod::IS_PRIVATE;
        $this->is_static = \ReflectionMethod::IS_STATIC;

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
        $propeties = new ClassPropMethAnalyzer($this->className);
        $methods = new ClassPropMethAnalyzer($this->className);
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
                $propeties->class_type = 'absract';
            } elseif ($reflector->isFinal()) {
                $propeties->class_type = 'final';
            } else {
                $propeties->class_type = 'default';
            }
            $propeties->setPropetyModifier($reflector);
            $methods->setMethodsModifier($reflector);

        }

           $propeties->setStaticMod();
           $methods->setStaticMod();

        return array($propeties, $methods);
    }
    public function setPropetyModifier( \ReflectionClass $obj): void
    {
        $this->public_modifier  = $obj->getProperties($this->is_public) ?? null;
        $this->protected_modifier  = $obj->getProperties($this->is_protected) ?? null;
        $this->static_modifier  = $obj->getProperties($this->is_static) ?? null;
        $this->private_modifier  = $obj->getProperties($this->is_private) ?? null;
    }
    public function setMethodsModifier(\ReflectionClass $obj): void
    {
        $this->public_modifier  = $obj->getMethods($this->is_public) ?? null;
        $this->protected_modifier  = $obj->getMethods($this->is_protected) ?? null;
        $this->static_modifier  = $obj->getMethods($this->is_static) ?? null;
        $this->private_modifier  = $obj->getMethods($this->is_private) ?? null;
    }

    public function setStaticMod()
    {
        $this->pub_static = \array_intersect($this->static_modifier, $this->public_modifier);
        $this->prot_static = \array_intersect($this->static_modifier, $this->protected_modifier);
        $this->priv_static = \array_intersect($this->static_modifier, $this->private_modifier);
    }

    public function getModifier()
    {
        return  array($this->public_modifier, $this->protected_modifier, $this->static_modifier, $this->private_modifier);
    }

    public function getStaticMod()
    {
        return array($this->pub_static, $this->prot_static, $this->priv_static);
    }
}
