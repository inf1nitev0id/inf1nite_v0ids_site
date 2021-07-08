<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MahoukaServerUser;
use App\Models\MahoukaServerRating;
use App\Models\ApiKeys;

class writeRating extends Command {
	/**
	* The name and signature of the console command.
	*
	* @var string
	*/
	protected $signature = 'server_rating:write {time : 0 - morning, 1 - evening}';

	/**
	* The console command description.
	*
	* @var string
	*/
	protected $description = 'Записывает рейтинг, полученный через API бота Tatsu.';

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
		$start = microtime(true);
		$time = intval($this->argument('time'));
		if (!($time == 0 || $time == 1)) {
			$this->info("Аргумент time должены быть равен 0 или 1.");
			return 0;
		}

		$url = 'https://api.tatsu.gg/v1/guilds/763030341103255582/rankings/all';
		$options = [
			'http' => [
				'header' => "Authorization: ".ApiKeys::getKeyString('tatsu'),
				'method' => 'GET',
			]
		];
		$context = stream_context_create($options);
		try {
			$data = json_decode(file_get_contents($url, false, $context))->rankings;
		} catch (\ErrorException $error) {
			$this->info($error->getMessage());
			return 0;
		}
		$bar = $this->output->createProgressBar(count($data));
		$bar->start();

		$options = [
			'http' => [
				'header' => "Authorization: Bot ".ApiKeys::getKeyString('bot'),
				'method' => 'GET',
			]
		];
		$date = date('Y-m-d');
		$context = stream_context_create($options);
		$upsert_data = [];
		foreach ($data as $user) {
			$url = "https://discord.com/api/v8/users/$user->user_id";
			$counter = 0;
			while (true) {
				try {
					$user_data = json_decode(file_get_contents($url, false, $context));
					break;
				} catch (\ErrorException $error) {
					preg_match("/ [0-9]{3} /" ,$error->getMessage(), $matches);
					if (intval($matches[0] ?? 429) == 429 && $counter < 3) {
						$counter++;
						sleep(30);
					} else {
						$this->info("\n".$error->getMessage());
						return 0;
					}
				}
			}
			$db_user = MahoukaServerUser::select('id', 'name')->where('discord_id', '=', $user->user_id)->first();
			if ($db_user) {
				if ($db_user->name != $user_data->username) {
					$db_user->name = $user_data->username;
					$db_user->save();
				}
			} else {
				$db_user = new MahoukaServerUser;
				$db_user->name = $user_data->username;
				$db_user->discord_id = $user->user_id;
				$db_user->join_date = $time ? $date : date('Y-m-d', strtotime('yesterday'));;
				$db_user->save();
			}
			$upsert_data[] = [
				'user_id' => $db_user->id,
				'rate' => $user->score,
				'date' => $date,
				'time' => $time,
			];
			$bar->advance();
		}
		MahoukaServerRating::upsert($upsert_data, ['user_id', 'date', 'time'], ['rate']);
		$bar->finish();
		$finish = round((microtime(true) - $start) * 10) / 10;
		$this->info("\nRating for $date $time writed in $finish seconds!");
		return 0;
	}
}
