<?php

namespace App\Console\Commands;

use App\AddConversionAction;
use App\AuthenticateInDesktopApplication;
use App\AuthenticateInWebApplication;
use App\GetCampaigns;
use App\Models\Purchase;
use App\Services\ROIDataSourceService;
use App\UploadOfflineConversion;
use Carbon\Carbon;
use Google\Cloud\BigQuery\BigQueryClient;
use Google\Cloud\Storage\StorageClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Dcat\EasyExcel\Excel;
use Google\Ads\GoogleAds\Lib\V8\GoogleAdsClient;
use Google\Ads\GoogleAds\V8\Services\ConversionActionServiceClient;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test {function} {param1?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'test';

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
        //
        $p = $this->argument('function');
        $name = 'test' . $p;
        call_user_func([$this, $name]);
    }

    public function test1()
    {
        // $fp = popen('df -lh| grep -E "^(/)"', 'r');
        // $fp = shell_exec('top -b -n 1');dd($fp);
        $fp = popen('top -b -n 1|grep -E "(Cpu|KiB Mem)"', 'r');
        $rs = '';
        while (!feof($fp)) {
            $rs .= fread($fp, 1024);
        }
        $sys_info = explode("\n", $rs);
        $cpu_info = explode(",", $sys_info[0]);
        $cpu_us = trim(trim($cpu_info[0], '%Cpu(s): '), 'us'); //百分比
        $cpu_sy = trim($cpu_info[1], 'sy');
        $cpu_id = trim($cpu_info[3], 'sy');
        $mem_info = explode(",", $sys_info[1]); //
        $mem_total = trim(trim($mem_info[0], 'KiB Mem : '), ' total');
        $mem_free = trim(trim($mem_info[1], 'free'));
        $mem_used = trim(trim($mem_info[2], 'used'));
        // $mem_usage = round(100 * intval($mem_used) /     intval($mem_total), 2); //百分比
        // pclose($fp);
        // $sysInfo = explode("\n", $rs);
        dump($sys_info);
        // $logsize = filesize(storage_path('logs/laravel-'.date('Y-m-d').'.log'));
        // dump($logsize);
        dump($cpu_info);
        dump($mem_info);
        dump((float)$cpu_us);
        dump((float)$cpu_sy);
        dump((float)$cpu_id);
        dump((float)$mem_total);
        dump((float)$mem_free);
        dump((float)$mem_used);
    }



    public function test2()
    {
        UploadOfflineConversion::main();
    }

    public function test3()
    {
        // AuthenticateInWebApplication::main();
        AuthenticateInDesktopApplication::main();
    }
    public function test4()
    {
        GetCampaigns::main();
        // UploadOfflineConversion::main();
    }
    public function test5()
    {
        AddConversionAction::main();
    }
    public function test6()
    {
        $googleAdsClient = app(GoogleAdsClient::class);
        /** @var  ConversionActionServiceClient $conversionActionServiceClient */
        $conversionActionServiceClient = $googleAdsClient->getConversionActionServiceClient();
        try {
            $formattedResourceName = $conversionActionServiceClient->conversionActionName('5173102433' , '818037502');
            $response = $conversionActionServiceClient->getConversionAction($formattedResourceName);
            dump($response);        
        } finally {
            $conversionActionServiceClient->close();
        }
    }
}
