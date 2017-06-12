<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GearmanJob;
use Gearman;

/**
 * Gearman crop image worker
 */
class CropImage extends Command
{
    /**
     * @inheritdoc
     */
    protected $name = 'worker:crop-image';

    /**
     * @inheritdoc
     */
    protected $description = 'Worker for cropping image';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Gearman::runWorker('crop_image', function (GearmanJob $job) {
            $workload = Gearman::deserializeWorkload($job->workload());
            $imagePath = $workload['image_path'];
            if (empty($imagePath)) {
                return Gearman::serializeWorkload(['status' => 'error', 'message' => 'No image']);
            }

            // Do some job...

            return Gearman::serializeWorkload(['status' => 'success', 'foo' => 'bar']);
        });
    }
}
