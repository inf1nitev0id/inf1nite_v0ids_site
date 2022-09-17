<?php

namespace App\Console\Commands;

use App\Classes\DiscordMessage;
use Illuminate\Console\Command;
use App\Models\MahoukaServerUser;
use App\Models\MahoukaServerRating;
use App\Models\ApiKeys;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

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
    protected $description = 'Отправляет рейтинг на дискорд-сервер через вебхук.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    private array $chars = [
        'original' => ['*', '~', '_', '`'],
        'replace'  => ['\\*', '\\~', '\\_', '\\`'],
    ];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int {
        $lastRateDate = MahoukaServerRating::getLastRate();
		$rating = MahoukaServerRating::where('date', '=', $lastRateDate['date'])
			->where('time', '=', $lastRateDate['time'])
			->orderBy('rate', 'DESC')->get();
		$ratingStrings = [];
		foreach ($rating as $key => $rate) {
			$username = str_replace(
				$this->chars['original'],
				$this->chars['replace'],
				$rate->user->name
			);
			$ratingStrings[] = ($key + 1).". **$username** - $rate->rate";
		}

		$prevRateDate = MahoukaServerRating
			::where(DB::raw("CONCAT(date, ' ', time)"), '<', $lastRateDate['date'].' '.$lastRateDate['time'])
			->orderBy('date', 'DESC')->orderBy('time', 'DESC')->first();
		$prevRating = MahoukaServerRating::where('date', '=', $prevRateDate['date'])
			->where('time', '=', $prevRateDate['time'])->get();
		$prevRatingArray = [];
		foreach ($prevRating as $rate) {
			$prevRatingArray[$rate->user->id] = $rate;
		}
		$changes = [];
		foreach ($rating as $rate) {
			$diff = $rate->rate - (isset($prevRatingArray[$rate->user->id]) ? $prevRatingArray[$rate->user->id]->rate : 0);
			unset($prevRatingArray[$rate->user->id]);
			if ($diff) {
				$changes[] = [
					'name' => $rate->user->name,
					'diff' => $diff,
				];
			}
		}
		foreach ($prevRatingArray as $rate) {
			$changes[] = [
				'name' => $rate->user->name,
				'diff' => -$rate->rate,
			];
		}
		usort($changes, static function ($a, $b) {
			if ($a['diff'] === $b['diff']) {
				return 0;
			} else {
				return $a['diff'] < $b['diff'] ? 1 : -1;
			}
		});
		$changesStrings = [];
		foreach ($changes as $key => $change) {
			$diff = ($change['diff'] > 0 ? '+' : '').$change['diff'];
			$changesStrings[] = ($key + 1).". **{$change['name']}** - $diff";
		}

		$message = new DiscordMessage();
		$message->username = 'Хлебозаменитель';
		$message->content = ($lastRateDate['time'] ? "Вечерний" : "Утренний")." рейтинг ".$lastRateDate['date'];
		$message->setBigEmbedText(implode("\n", $ratingStrings));
		$message->setEmbedColor(0xFFFFFF);
		$key = ApiKeys::getKeyString('mahouka-bot-channel-webhook');
		$message->sendWebhook($key);
		if ($changesStrings) {
			$message->content = "Изменения в рейтинге";
			$message->setBigEmbedText(implode("\n", $changesStrings));
			$message->setEmbedColor(0x88FF88);
			$message->sendWebhook($key);
		}
        return 0;
    }
}
