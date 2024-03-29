<?php

/*
 * This file is part of the Symfony MakerBundle package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Maker;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\InflectorFactory;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Doctrine\DoctrineHelper;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Renderer\FormTypeRenderer;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\Util\UseStatementGenerator;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Validator\Validation;

/**
 * @author Sadicov Vladimir <sadikoff@gmail.com>
 */
final class MakeInboreRepository extends AbstractMaker
{
    private Inflector $inflector;
    private string $controllerClassName;
    private bool $generateTests = false;
    private Generator $generator;


    public function __construct(private DoctrineHelper $doctrineHelper, private FormTypeRenderer $formTypeRenderer)
    {
        $this->inflector = InflectorFactory::create()->build();
    }

    public static function getCommandName(): string
    {
        return 'make:inbore-repository';
    }

    public static function getCommandDescription(): string
    {
        return 'Creates InRORe Repository for Doctrine entity class';
    }

    /**
     * {@inheritdoc}
     */
    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->addArgument('entity-class', InputArgument::OPTIONAL, sprintf('The class name of the entity to create Repository (e.g. <fg=yellow>%s</>)', Str::asClassName(Str::getRandomTerm())))
            ->setHelp(file_get_contents(__DIR__.'/Resources/help/MakeInboreRepository.txt'))
        ;

        $inputConfig->setArgumentAsNonInteractive('entity-class');
    }

    /**
     * @return void
     */
    public function interact(InputInterface $input, ConsoleStyle $io, Command $command): void
    {
        if (null === $input->getArgument('entity-class')) {
            $argument = $command->getDefinition()->getArgument('entity-class');

            $entities = $this->doctrineHelper->getEntitiesForAutocomplete();

            $question = new Question($argument->getDescription());
            $question->setAutocompleterValues($entities);

            $value = $io->askQuestion($question);

            $input->setArgument('entity-class', $value);
        }

        $defaultControllerClass = Str::asClassName(sprintf('%s Repository', $input->getArgument('entity-class')));

        $this->controllerClassName = $io->ask(
            sprintf('Choose a name for your Repository class (e.g. <fg=yellow>%s</>)', $defaultControllerClass),
            $defaultControllerClass
        );
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $this->generator = $generator;
        $entityClassDetails = $generator->createClassNameDetails(
            Validator::entityExists($input->getArgument('entity-class'), $this->doctrineHelper->getEntitiesForAutocomplete()),
            'Entity\\'
        );

        $entityDoctrineDetails = $this->doctrineHelper->createDoctrineDetails($entityClassDetails->getFullName());

        $repositoryVars = [];
        $repositoryClassName = EntityManagerInterface::class;
        
                
        if (null !== $entityDoctrineDetails->getRepositoryClass()) {
            $repositoryClassDetails = $generator->createClassNameDetails(
                '\\'.$entityDoctrineDetails->getRepositoryClass(),
                'Repository\\Core\\',
                'Repository'
            );
            $repositoryClassName = $repositoryClassDetails->getFullName();
            $repositoryVars = [
                'repository_full_class_name' => $repositoryClassName,
                'repository_class_name' => $repositoryClassDetails->getShortName(),
                'repository_var' => lcfirst($this->inflector->singularize($repositoryClassDetails->getShortName())),
            ];
        } else {
            $repositoryClassDetails = $generator->createClassNameDetails(
                 $entityClassDetails->getShortName().'Repository',
                 'Repository\\Core\\',
                 'Repository'
             ); 
            $repositoryClassName = $repositoryClassDetails->getFullName();
            $repositoryVars = [
                'repository_full_class_name' => $repositoryClassName,
                'repository_class_name' => $repositoryClassDetails->getShortName(),
                'repository_var' => lcfirst($this->inflector->singularize($repositoryClassDetails->getShortName())),
            ];
        }

        $entityVarPlural = lcfirst($this->inflector->pluralize($entityClassDetails->getShortName()));
        $entityVarSingular = lcfirst($this->inflector->singularize($entityClassDetails->getShortName()));

        $entityTwigVarPlural = Str::asTwigVariable($entityVarPlural);
        $entityTwigVarSingular = Str::asTwigVariable($entityVarSingular);


        $generator->generateController(
            $repositoryClassDetails->getFullName(),
            __DIR__.'/Resources/skeleton/crud/repository/InboreRepository.tpl.php',
            array_merge([
                    'entity_full_class_name' => $entityClassDetails->getFullName(),
                    'entity_class_name' => $entityClassDetails->getShortName(),
                    'entity_var_plural' => $entityVarPlural,
                    'entity_var_singular' => $entityVarSingular,
                    'entity_identifier' => $entityDoctrineDetails->getIdentifier(),
                ],
                $repositoryVars
            )
        );
           

        $generator->writeChanges();

        $this->writeSuccessMessage($io);

        $io->text(sprintf('Next: Check your new Repository by going to <fg=yellow>%s/</>', Str::asRoutePath($repositoryClassDetails->getRelativeNameWithoutSuffix())));
    }

    /**
     * {@inheritdoc}
     */
    public function configureDependencies(DependencyBuilder $dependencies): void
    {
        $dependencies->addClassDependency(
            Route::class,
            'router'
        );

        $dependencies->addClassDependency(
            AbstractType::class,
            'form'
        );

        $dependencies->addClassDependency(
            Validation::class,
            'validator'
        );

        $dependencies->addClassDependency(
            TwigBundle::class,
            'twig-bundle'
        );

        $dependencies->addClassDependency(
            DoctrineBundle::class,
            'orm'
        );

        $dependencies->addClassDependency(
            CsrfTokenManager::class,
            'security-csrf'
        );
    }

}
