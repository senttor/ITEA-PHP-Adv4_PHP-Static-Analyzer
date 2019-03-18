<?php

namespace Greeflas\StaticAnalyzer\Command;


use Greeflas\StaticAnalyzer\Analyzer\ClassPropMethAnalyzer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Oksentiuk Viktor oksentiuk.viktor@gmail.com
 */
class ClassAnalizerStat extends Command
{
    /**
     * This method describe input command and set necessary arguments
     *
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
        ->setName('class-analizer-stat')
        ->setDescription('Shows statistic about type/name classes, their number/types  methods,propeties')
        ->addArgument(
            'className',
            InputArgument::REQUIRED,
            'name of class for analyze'
        )
    ;
    }

    /**
     *
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $className = $input->getArgument('className');

        $analyzer = new ClassPropMethAnalyzer($className);
        $res_analyze = $analyzer->analyze();
        //var_dump($res_analyze[0]);die();
        //var_dump(sizeof($res_analyze[0]->getModifier()[0]));die();
        $section = $output->section();
        $section->writeln(\sprintf(
        '<info>Class: %s is %s </info>',
        $className,
        //$res_analyze['classtype']
        $res_analyze[0]->class_type
    ));
        $section->writeln('<info>Properties:</info>');
        $section->writeln(\sprintf(
        '<info>    public: %d %s
    protected: %d %s
    private: %d </info>',
        //count($res_analyze['propeties']['public']),
            \sizeof($res_analyze[0]->getModifier()[0]),
        (($res_analyze[0]->getStaticMod()[0]) ? '('.\sizeof($res_analyze[0]->getStaticMod()[0]).' static)' : ''),
        \sizeof($res_analyze[0]->getModifier()[1]),
       (($res_analyze[0]->getStaticMod()[1]) ? '('.\sizeof($res_analyze[0]->getStaticMod()[1]).' static)' : ''),
        \sizeof($res_analyze[0]->getModifier()[3])
    ));
        $section->writeln('<info>Methods:</info>');
        $section->writeln(\sprintf(
        '<info>    public: %d  %s
    protected: %d
    private: %d %s </info>',
            \sizeof($res_analyze[1]->getModifier()[0]),
            (($res_analyze[1]->getStaticMod()[0]) ? '('.\sizeof($res_analyze[1]->getStaticMod()[0]).' static)' : ''),
            \sizeof($res_analyze[1]->getModifier()[1]),
            \sizeof($res_analyze[1]->getModifier()[3]),
        (($res_analyze[1]->getStaticMod()[2]) ? '('.\sizeof($res_analyze[1]->getStaticMod()[1]).' static)' : '')
    ));
    }
}
