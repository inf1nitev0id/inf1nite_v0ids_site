<?php

namespace App\Console\Commands;

use App\Classes\DiscordMessage;
use Illuminate\Console\Command;
use App\Models\ApiKeys;
use Illuminate\Support\Facades\App;

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
    protected $description = 'Отправляет текстовое сообщение на дискорд-сервер через вебхук.';

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
    public function handle(): int {
		$message = new DiscordMessage();
		$message->setBigEmbedText($this->argument('text').' '.$this->argument('text'));
		$message->content = $this->argument('text');
		$message->sendWebhook(ApiKeys::getKeyString('mahouka-bot-channel-webhook'));
        return 0;
    }
}
