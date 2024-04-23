<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-user',
    description: 'Add a short description for your command',
)]
class CreateUserCommand extends Command
{
    private $em;
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $passwordEncoder)
    {
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');

        $firstName = $helper->ask($input, $output, new Question('Entrez votre prénom: '));
        $lastName = $helper->ask($input, $output, new Question('Entrez votre nom de famille: '));
        $email = $helper->ask($input, $output, new Question('Entrez votre E-mail: '));

        $passwordQuestion = new Question('Entrez votre mot de passe: ');
        $passwordQuestion->setHidden(true);
        $passwordQuestion->setHiddenFallback(false); // Si la saisie masquée n'est pas prise en charge, elle sera traitée comme une saisie normale
        $password = $helper->ask($input, $output, $passwordQuestion);

        $user = new User;
        $user->setEmail($email);
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword(
            $this->passwordEncoder->hashPassword($user, $password)
        );
        $user->setLastName($lastName);
        $user->setFirstName($firstName);
        $user->setVerified(true); // Définir isVerified sur true

        $this->em->persist($user);
        $this->em->flush();

        $output->writeln('L\'Admin a bien été crée !');

        return Command::SUCCESS;
    }
}
