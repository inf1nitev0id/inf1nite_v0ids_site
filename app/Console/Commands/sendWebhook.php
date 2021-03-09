<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MahoukaServerUser;
use App\Models\ApiKeys;

class sendWebhook extends Command {
	/**
	* The name and signature of the console command.
	*
	* @var string
	*/
	protected $signature = 'server_rating:sendWebhook';

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
		$users = MahoukaServerUser::getSortedUsers();
		$top = "";
		foreach ($users as $key => $user) {
			if ($user['rate'])
				$top .= ($key + 1)." <@".$user['discord_id']."> - ".$user['rate']."\n";
		}
		$url = "https://canary.discord.com/api/webhooks/".ApiKeys::getKeyString('mahouka-bot-channel-webhook');
		$hookObject = [
			"username" => "Хлебозаменитель",
			"tts" => false,
			"embeds" => [
				[
					"title" => "Рейтинг ".date('d.m.Y H:i'),
					"type" => "rich",
					"description" => $top,
					"color" => hexdec( "FFFFFF" ),
				]
			]
		];
		$postdata = json_encode($hookObject);

		$opts = [
			'http' => [
				'method'  => 'POST',
				'header'  => 'Content-Type: application/json',
				'content' => $postdata
			]
		];
		$context = stream_context_create($opts);
		$this->info(file_get_contents($url, false, $context));
		return 0;
	}
}
