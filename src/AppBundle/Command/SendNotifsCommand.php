<?php

namespace AppBundle\Command;

use AppBundle\Service\NotifsSender;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class SendNotifsCommand extends ContainerAwareCommand {

    protected function configure () {
        // On set le nom de la commande
        $this->setName('app:sendnotifscommand');

        // On set la description
        $this->setDescription("Permet de lancer l'envoi de notifications aux utilisateurs");

        // On set l'aide
        $this->setHelp("Lancez la commande php bin/console app:sendnotifscommand -h");
    }

    public function execute (InputInterface $input, OutputInterface $output) {
        $output->writeln($this->getContainer()->get('notifsSender')->submit());
    }
}