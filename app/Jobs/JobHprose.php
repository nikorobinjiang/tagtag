<?php

namespace App\Jobs;

use App\Libraries\BLogger;

use Exception;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class JobHprose implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * hprose 客户端.
     *
     * @var \Hprose\Client
     */
    protected $client;

    /**
     * 当前操作表日期.
     *
     * @var String
     */
    protected $date;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($date='')
    {
        $this->date = $date;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->client = \Hprose\Client::create('tcp://47.97.173.49:9601', false);
        $this->client->timeout = 1000;
        if ($this->date) {
            $logs = DB::table('ww_ad_log_'.$this->date)->get();
            $logs->each(function ($log) {
                BLogger::scope(['hprose', 'job'])->debug($log);
                $this->client->userLogPageAction($log);
            });
        }
    }

    /**
     * The job failed to process.
     *
     * @param  \Throwable  $e
     * @return void
     */
    public function failed(\Throwable $e)
    {
        BLogger::scope(['hprose', 'job'])->error('JobHprose失败', [
            'e' => $e,
        ]);
    }

    /**
     * 简要描述当前 job 的内容.
     *
     * @return Array
     */
    protected function descMe()
    {
        return ['data' => $this->data];
    }
}
