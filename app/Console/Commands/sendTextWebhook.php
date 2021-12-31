<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ApiKeys;

class sendTextWebhook extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server_rating:sendTextWebhook {text}';

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
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        $url        = "https://canary.discord.com/api/webhooks/".ApiKeys::getKeyString('mahouka-bot-channel-webhook');
        $hookObject = [
            "content" => "".$this->argument('text'),
            "tts"     => false,
        ];
        $postdata   = json_encode($hookObject);

        $opts    = [
            'http' => [
                'method'  => 'POST',
                'header'  => 'Content-Type: application/json',
                'content' => $postdata,
            ],
        ];
        $context = stream_context_create($opts);
        $this->info(
            file_get_contents(
                $url,
                false,
                $context
            )
        );
        return 0;
    }
}
