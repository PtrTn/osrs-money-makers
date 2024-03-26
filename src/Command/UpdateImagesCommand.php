<?php

namespace App\Command;

use App\Entity\Item;
use App\Repository\ItemRepository;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:update:images',
    description: 'Updates image files for all items in the database',
)]
class UpdateImagesCommand extends Command
{
    private const IMAGE_BASE_URL = 'https://secure.runescape.com/m=itemdb_oldschool/1711365526242_obj_sprite.gif?id=';
    private const IMAGE_PATH = 'public' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'items';

    private string $projectRoot;

    public function __construct(
        private HttpClientInterface $httpClient,
        private ItemRepository      $itemRepository,
        private LoggerInterface     $logger,
        private Filesystem          $filesystem,
        #[Autowire('%kernel.project_dir%')] string $projectRoot
    )
    {
        parent::__construct();
        $this->projectRoot = $projectRoot;
    }

    public function configure(): void
    {
        $this->addOption('refresh', 'r', InputOption::VALUE_OPTIONAL, 'Refresh existing images too', false);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $refresh = $input->getOption('refresh');
        $io = new SymfonyStyle($input, $output);

        $io->info('Fetching item data from database');
        $itemsFromDatabase = $this->itemRepository->findAllAsCollection();
        $itemIds = $itemsFromDatabase->map(fn (Item $item) =>  $item->getGameId());

        $io->info('Downloading latest item images');
        $additions = 0;
        $updates = 0;
        $failures = 0;

        $progressBar = $io->createProgressBar($itemIds->count());
        $progressBar->setFormat('very_verbose');
        $chunks = array_chunk($itemIds->toArray(), 50);
        foreach ($chunks as $chunk) {
            $responses = [];
            foreach ($chunk as $itemId) {
                $destination = join(DIRECTORY_SEPARATOR, [$this->projectRoot, self::IMAGE_PATH, $itemId . '.gif']);
                if ($refresh === false && $this->filesystem->exists($destination)) {
                    continue;
                }

                $responses[] = $this->httpClient->request(
                    'GET',
                    self::IMAGE_BASE_URL . $itemId,
                    ['user_data' => $itemId, 'extra' => ['trace_content' => false]]
                );
            }
            foreach ($responses as $response) {
                $itemId = $response->getInfo('user_data');
                try {
                    if ($response->getStatusCode() === 429) {
                        $io->error('Reached api limit, aborting');
                        return Command::FAILURE;
                    }
                    if ($response->getStatusCode() !== 200) {
                        $this->logger->warning('Unable to find image', ['itemId' => $itemId]);
                        $failures++;
                        continue;
                    }
                    $stream = $response->toStream();
                    $destination = join(DIRECTORY_SEPARATOR, [$this->projectRoot, self::IMAGE_PATH, $itemId . '.gif']);
                    $this->filesystem->dumpFile($destination, $stream);
                } catch(Exception | ExceptionInterface $exception) {
                    $this->logger->warning('Unable to save image contents to file', ['itemId' => $itemId, 'exception' => $exception]);
                    $failures++;
                    continue;
                }

                $this->filesystem->exists($destination) ? $updates++ : $additions++;
                $progressBar->advance();
            }
        }

        $progressBar->finish();
        $io->success(sprintf('Successfully added %s images and updated %s images. Skipped %s images due to errors', $additions, $updates, $failures));

        return Command::SUCCESS;
    }
}
