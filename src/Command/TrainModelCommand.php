<?php

namespace App\Command;

use App\Repository\TimeStreamRepository;
use Rubix\ML\Datasets\Unlabeled;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Rubix\ML\AnomalyDetectors\LocalOutlierFactor;
use Rubix\ML\PersistentModel;
use Rubix\ML\Graph\Trees\BallTree;
use Rubix\ML\Kernels\Distance\Euclidean;
use Rubix\ML\Persisters\Filesystem;

class TrainModelCommand extends Command
{
    protected static $defaultName = 'app:train-model';

    private $timeStreamRepository;

    public function __construct(TimeStreamRepository $timeStreamRepository)
    {
        $this->timeStreamRepository = $timeStreamRepository;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Trains the anomaly detection model.')
            ->setHelp('This command allows you to train an anomaly detection model...')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $timeStreams = $this->timeStreamRepository->findAll();

        $samples = [];
        foreach ($timeStreams as $timeStream) {
            $duration = null;
            if ($timeStream->getStartTime() && $timeStream->getEndTime()) {
                // Assuming getStartTime and getEndTime return DateTime objects
                $interval = $timeStream->getStartTime()->diff($timeStream->getEndTime());
                $duration = $interval->days * 24 * 60; // Convert to minutes
                $duration += $interval->h * 60;
                $duration += $interval->i;
            }

            $samples[] = [
                $timeStream->getUserId(),
                $duration,
                $timeStream->getIsStartTime() ? 1 : 0, // Convert boolean to integer
            ];
        }

        $dataset = new Unlabeled($samples);

        // Using LOF without specifying a tree for brute force method
        $estimator = new PersistentModel(new LocalOutlierFactor(20, 0.1, new BallTree(30, new Euclidean())), new Filesystem('model.rbx'));

        $estimator->train($dataset);
        
        $estimator->save();

        // Now that the model is trained, you can save it, make predictions, etc.

        $output->writeln('The anomaly detection model has been trained.');

        return Command::SUCCESS;
    }
}
