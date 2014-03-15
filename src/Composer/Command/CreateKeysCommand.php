<?php

/*
 * This file is part of Composer.
 *
 * (c) Nils Adermann <naderman@naderman.de>
 *     Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Composer\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Util\Openssl;

/**
 * @author Pádraic Brady <padraic.brady@gmail.com>
 */
class CreateKeysCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('create-keys')
            ->setDescription('Create and export a set of developer private and public keys for signing packages')
            ->setDefinition(array(
                new InputOption('prefix', 'x', InputOption::VALUE_REQUIRED, 'Include a custom file prefix, e.g. foo for foo-private.pem', 'composer'),
                new InputOption('passphrase', 'p', InputOption::VALUE_REQUIRED, 'Set a passphrase for the exported private key', null),
                new InputArgument('directory', InputArgument::REQUIRED, 'Directory in which to save the exported keys'),
            ))
            ->setHelp(<<<EOT
The create-keys command generates a pair of RSA private and public keys
which can be used to sign packages (e.g. when tagging releases via git).
The private key should be kept offline. The public key should be added
to your repository and published online (e.g. a Github readme). It is
strongly recommended that you set a passphrase to protect the private
key in the event that it is lost or stolen.

EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getArgument('directory');
        if (!is_dir($path) || !is_writeable($path)) {
            $output->writeln('<error>The specified path does not exist or is not writeable: '.$path.'</error>');
            return 1;
        }

        if (empty($input->getOption('passphrase')) || strlen($input->getOption('passphrase')) == 0) {
            $output->writeln('<warning>You have not specified a passphrase so that the private key can be encrypted!</warning>');
        }

        $prefix = rtrim($input->getOption('prefix'), '-') . '-';
        $openssl = new Openssl;
        $openssl->createKeys($input->getOption('passphrase'));

        $privateName = $prefix . 'private.pem';
        $publicName = $prefix . 'public.pem';
        $openssl->exportPrivateKey($path . DIRECTORY_SEPARATOR . $privateName);
        $openssl->exportPublicKey($path . DIRECTORY_SEPARATOR . $publicName);

        $output->writeln('Private key created at: '. $path . DIRECTORY_SEPARATOR . $privateName);
        $output->writeln('Public key created at: '. $path . DIRECTORY_SEPARATOR . $publicName);
    }
}
