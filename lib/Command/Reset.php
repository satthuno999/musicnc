<?php
/**
 * MUSIC KMA
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the LICENSE.md file.
 *
 * @author S P A R K <binh9aqktk@gmail.com>
 * @copyright 2022-2023 S P A R K
 */
 
namespace OCA\musicnc\Command;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use OCA\musicnc\Controller\DbController;

class Reset extends Command {
	private $userManager;
    private $DBController;

    public function __construct(\OCP\IUserManager $userManager, DbController $DBController)
    {
		$this->userManager = $userManager;
        $this->DBController = $DBController;
		parent::__construct();
	}
	
	protected function configure() {
		$this
			->setName('musicnc:reset')
			->setDescription('reset audio player library')
			->addArgument(
					'user_id',
					InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
					'reset the whole library of the given user(s)'
			)
			->addOption(
					'all',
					null,
					InputOption::VALUE_NONE,
					'reset the whole library of all known users'
			)
		;
	}
	
	protected function execute(InputInterface $input, OutputInterface $output) {
		if ($input->getOption('all')) {
			$users = $this->userManager->search('');
		} else {
			$users = $input->getArgument('user_id');
		}

		if (count($users) === 0) {
			$output->writeln("<error>Please specify a valid user id to reset, \"--all\" to scan for all users<error>");
            return 1;
		}
		
		foreach ($users as $userId) {
			if (is_object($userId)) $user = $userId;
			else $user = $this->userManager->get($userId);

			if ($user === null) {
				$output->writeln("<error>User $userId does not exist</error>");
			} else {
				$userId = $user->getUID();
				$output->writeln("<info>Reset library for $userId</info>");
                $this->DBController->resetMediaLibrary($userId, $output);
			}
		}
        return 0;
	}
}
