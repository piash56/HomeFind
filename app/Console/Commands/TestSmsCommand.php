<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SmsService;

class TestSmsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:test {phone}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test SMS functionality by sending a test message';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $phone = $this->argument('phone');

        $this->info("Testing SMS functionality...");
        $this->info("Phone number: {$phone}");

        $smsService = new SmsService();
        $result = $smsService->sendTestSms($phone);

        if ($result) {
            $this->info("✅ Test SMS sent successfully!");
        } else {
            $this->error("❌ Failed to send test SMS. Check logs for details.");
        }

        return 0;
    }
}
