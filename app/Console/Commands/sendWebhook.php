<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MahoukaServerUser;
use App\Models\MahoukaServerRating;
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

	private $chars = [
		'original' => ['*', '~', '_', '`'],
		'replace' => ['\\*', '\\~', '\\_', '\\`'],
	];

	/**
	* Execute the console command.
	*
	* @return int
	*/
	public function handle() {
		$users = MahoukaServerUser::getSortedUsers(null, false, true);
		$lastRate = MahoukaServerRating::getLastRate();
		$hookObject = [
			'username' => "Хлебозаменитель",
			'content' => ($lastRate['time'] ? "Вечерний" : "Утренний")." рейтинг ".$lastRate['date'],
			'tts' => false,
		];
		$string = "";
		$title = "";
		$count = 1;
		foreach ($users as $key => $user) {
			if ($user['rate']) {
				$username = str_replace($this->chars['original'], $this->chars['replace'], $user['name']);
				$row = ($key + 1).".	**".$username."** - ".$user['rate']."\n";
				if (strlen($string) + strlen($row) >= 2048) {
					if ($count == 1) $title = "Часть $count";
					$hookObject['embeds'][] = [
						'title' => $title,
						'description' => $string,
						'type' => "rich",
						'color' => hexdec( "FFFFFF" ),
					];
					$title = "Часть ".++$count;
					$string = "";
				}
				$string .= $row;
			}
		}
		$hookObject['embeds'][] = [
			'title' => $title,
			'description' => $string,
			'type' => "rich",
			'color' => hexdec( "FFFFFF" ),
		];
		// $url = "https://canary.discord.com/api/webhooks/".ApiKeys::getKeyString('test-server-webhook');
		$url = "https://canary.discord.com/api/webhooks/".ApiKeys::getKeyString('mahouka-bot-channel-webhook');
		$opts = [
			'http' => [
				'method'  => 'POST',
				'header'  => 'Content-Type: application/json',
				'content' => json_encode($hookObject)
			]
		];
		$context = stream_context_create($opts);
		$this->info(file_get_contents($url, false, $context));
		return 0;
	}
}
