<?php

namespace App\Console\Commands;

use Carbon\Carbon;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ClearLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:log';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $now = Carbon::now()->addMonth(-1);
        $re = '/.*(\d{4}-\d{2}-\d{2})\.log/m';
        $files = Arr::where(Storage::disk('logs')->files(null, true), function($filename) use($re, $now) {
            $matches = [];
            preg_match_all($re, $filename, $matches, PREG_SET_ORDER, 0);

            if (sizeof($matches) != 1 || sizeof($matches[0]) != 2) {
                return false;
            }

            $date = Arr::get($matches, '0.1');
            $diff = Carbon::parse($date)->diffInMonths($now, false);
            return $diff > 0;
        });

        $count = count($files);

        if(Storage::disk('logs')->delete($files)) {
            $this->info(sprintf('Deleted %s %s!', $count, Str::plural('file', $count)));
        } else {
            $this->error('Error in deleting log files!');
        }
    }
}
