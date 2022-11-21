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
use Doctrine\Common\Inflector\Inflector as LegacyInflector;
use Doctrine\Inflector\InflectorFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Doctrine\DoctrineHelper;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Validator\Validation;
use Symfony\Bundle\MakerBundle\Util\ClassNameDetails;

/**
 * @author Sadicov Vladimir <sadikoff@gmail.com>
 */
final class MakeInboreEmbed extends AbstractMaker
{
    private $doctrineHelper;

    private $formTypeRenderer;

    private $inflector;

    private $controllerClassName;

    public function __construct(DoctrineHelper $doctrineHelper)
    {
        $this->doctrineHelper = $doctrineHelper;

        if (class_exists(InflectorFactory::class)) {
            $this->inflector = InflectorFactory::create()->build();
        }
    }

    public static function getCommandName(): string
    {
        return 'make:inbore-embed';
    }

    public static function getCommandDescription(): string
    {
        return 'Creates InRORe Embed Type for Doctrine N-N entity class';
    }

    /**
     * {@inheritdoc}
     */
    public function configureCommand(Command $command, InputConfiguration $inputConfig)
    {
        $command
            ->addArgument('entity-class', InputArgument::OPTIONAL, sprintf('The class name of the entity to create EmbedType Class (e.g. <fg=yellow>%s</>)', Str::asClassName(Str::getRandomTerm())))
            ->setHelp(file_get_contents(__DIR__.'/Resources/help/MakeInboreEmbed.txt'))
        ;

        $inputConfig->setArgumentAsNonInteractive('entity-class');
    }

    /**
     * @return void
     */
    public function interact(InputInterface $input, ConsoleStyle $io, Command $command)
    {
        if (null === $input->getArgument('entity-class')) {
            $argument = $command->getDefinition()->getArgument('entity-class');

            $entities = $this->doctrineHelper->getEntitiesForAutocomplete();

            $question = new Question($argument->getDescription());
            $question->setAutocompleterValues($entities);

            $value = $io->askQuestion($question);

            $input->setArgument('entity-class', $value);
        }

        $defaultControllerClass = Str::asClassName(sprintf('%s Controller', $input->getArgument('entity-class')));

    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
        $this->generator = $generator;
        $entityClassDetails = $generator->createClassNameDetails(
            Validator::entityExists($input->getArgument('entity-class'), $this->doctrineHelper->getEntitiesForAutocomplete()),
            'Entity\\'
        );

        $entityDoctrineDetails = $this->doctrineHelper->createDoctrineDetails($entityClassDetails->getFullName());


        $iter = 0;
        do {
            $formClassDetails = $generator->createClassNameDetails(
                $entityClassDetails->getRelativeNameWithoutSuffix().($iter ?: '').'EmbedType',
                'Form\\EmbedTypes\\',
                'Type'
            );
            ++$iter;
        } while (class_exists($formClassDetails->getFullName()));

  
        // genere la classe NomentiteEmbedType à partir d'un template EmbedType.tpl.php définit dans le dossier Resources/skeleton/form 
        //$this->formTypeRenderer->render(
        $this->render(
            $formClassDetails,
            $entityDoctrineDetails->getFormFields(),
            $entityClassDetails
        );


        $generator->writeChanges();

        $this->writeSuccessMessage($io);

        $io->text(sprintf('Next: Check your new EmbedType Class by going to <fg=yellow>%s/</>', Str::asRoutePath($formClassDetails->getRelativeNameWithoutSuffix())));
    }

    /**
     * {@inheritdoc}
     */
    public function configureDependencies(DependencyBuilder $dependencies)
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
            'orm-pack'
        );

        $dependencies->addClassDependency(
            CsrfTokenManager::class,
            'security-csrf'
        );

        $dependencies->addClassDependency(
            ParamConverter::class,
            'annotations'
        );
    }

    private function pluralize(string $word): string
    {
        if (null !== $this->inflector) {
            return $this->inflector->pluralize($word);
        }

        return LegacyInflector::pluralize($word);
    }

    private function singularize(string $word): string
    {
        if (null !== $this->inflector) {
            return $this->inflector->singularize($word);
        }

        return LegacyInflector::singularize($word);
    }
    
    
    // FormType render 
    public function render(ClassNameDetails $formClassDetails, array $formFields, ClassNameDetails $boundClassDetails = null, array $constraintClasses = [], array $extraUseClasses = [])
    {
        $fieldTypeUseStatements = [];
        $fields = [];
        foreach ($formFields as $name => $fieldTypeOptions) {
            $fieldTypeOptions = $fieldTypeOptions ?? ['type' => null, 'options_code' => null];

            if (isset($fieldTypeOptions['type'])) {
                $fieldTypeUseStatements[] = $fieldTypeOptions['type'];
                $fieldTypeOptions['type'] = Str::getShortClassName($fieldTypeOptions['type']);
            }

            $fields[$name] = $fieldTypeOptions;
        }

        $mergedTypeUseStatements = array_unique(array_merge($fieldTypeUseStatements, $extraUseClasses));
        sort($mergedTypeUseStatements);

        $this->generator->generateClass(
            $formClassDetails->getFullName(),
            __DIR__.'/Resources/skeleton/form/EmbedType.tpl.php',
            [
                'bounded_full_class_name' => $boundClassDetails ? $boundClassDetails->getFullName() : null,
                'bounded_class_name' => $boundClassDetails ? $boundClassDetails->getShortName() : null,
                'form_fields' => $fields,
                'field_type_use_statements' => $mergedTypeUseStatements,
                'constraint_use_statements' => $constraintClasses,
            ]
        );
    }
}
