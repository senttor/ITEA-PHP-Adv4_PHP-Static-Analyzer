<?php

namespace Greeflas\StaticAnalyzer\Command;

/*use Symfony\Component\Console{
    Command\Command,
    Input\InputArgument,
    Input\InputInterface,
    Output\OutputInterface,
};*/

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
     * Describe command
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
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \ReflectionException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $className = $input->getArgument('className');

        $analyzer = new ClassPropMethAnalyzer($className);
        $res_analyze = $analyzer->analyze();
        $section = $output->section();
        $section->writeln(\sprintf(
        '<info>Class: %s is %s </info>',
        $className,
        $res_analyze['classtype']
    ));
        $section->writeln('<info>Properties:</info>');
        $section->writeln(\sprintf(
        '<info>    public: %d %s
    protected: %d %s
    private: %d </info>',
        count($res_analyze['propeties']['public']),
        (($res_analyze['propeties']['pub_static']) ? '('.count($res_analyze['propeties']['pub_static']).' static)' : ''),
        count($res_analyze['propeties']['protected']),
       (($res_analyze['propeties']['prot_static']) ? '('.count($res_analyze['propeties']['prot_static']).' static)' : ''),
        count($res_analyze['propeties']['private'])
    ));

        $section->writeln('<info>Methods:</info>');
        $section->writeln(\sprintf(
        '<info>    public: %d  %s
    protected: %d
    private: %d %s </info>',
        count($res_analyze['methods']['public']),
        (($res_analyze['methods']['pub_static']) ? '('.count($res_analyze['methods']['pub_static']).' static)' : ''),
        count($res_analyze['methods']['protected']),
        count($res_analyze['methods']['private']),
        (($res_analyze['methods']['priv_static']) ? '('.count($res_analyze['methods']['priv_static']).' static)' : '')
    ));
    }
}
