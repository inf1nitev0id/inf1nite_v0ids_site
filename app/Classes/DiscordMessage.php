<?php

namespace App\Classes;

use App\Models\ApiKeys;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\NoReturn;

/**
 * Класс для отправки сообщений в Discord через API
 */
class DiscordMessage {
	const CONTENT_LIMIT = 2000;
	const EMBED_DESCRIPTION_LIMIT = 4096;
	const EMBED_COUNT_LIMIT = 10;
	const EMBED_SUM_LIMIT = 6000;

	public string $username = '';
	public string $content = '';
	public array $embeds = [];

	/**
	 * Помещает текст в эмбеды
	 *
	 * @param string $text
	 *
	 * @return void
	 */
	public function setBigEmbedText(string $text): void {
		if (Str::length($text) <= self::EMBED_DESCRIPTION_LIMIT) {
			$this->embeds = [[
				'description' => $text,
				'type'        => 'rich',
			]];
		} else {
			$this->embeds = [];
			if ($text) {
				$textParts = self::splitTextByLength($text, self::EMBED_DESCRIPTION_LIMIT);
				$count     = count($textParts);
				if ($count > 1) {
					for ($i = 0; $i < $count; $i++) {
						$this->embeds[] = [
							'title'       => 'Часть '.($i + 1),
							'description' => $textParts[$i],
							'type'        => 'rich',
						];
					}
				} else {
					$this->embeds[] = [
						'description' => $text,
						'type'        => 'rich',
					];
				}
			}
		}
	}

	/**
	 * Делит текст на фрагменты не более указанной длины
	 *
	 * @param $text
	 * @param $length
	 *
	 * @return string[]
	 */
	private function splitTextByLength($text, $length): array {
		$result = [];
		if (Str::length($text) > $length) {
			$textPart = '';
			foreach (preg_split('/\r?\n/', $text) as $string) {
				if (Str::length($string) <= $length) {
					$newTextPart = $textPart.($textPart ? "\n" : '').$string;
					if (Str::length($newTextPart) > $length) {
						$result[] = $textPart;
						$textPart = $string;
					} else {
						$textPart = $newTextPart;
					}
				} else {
					if ($textPart) {
						$result[] = $textPart;
					}
					do {
						if (preg_match('/^(.{1,'.$length.'})\s/', $string, $matches)) {
							$result[] = trim($matches[1]);
							$string = Str::substr($string, Str::length($matches[0]));
						} else {
							$result[] = Str::substr($string, 0, $length);
							$string = Str::substr($string, $length);
						}
					} while (Str::length($string) > $length);
					$textPart = trim($string);
				}
			}
			if ($textPart) {
				$result[] = $textPart;
			}
		} else {
			$result[] = $text;
		}
		return $result;
	}

	/**
	 * Устанавливает цвет для всех существующих на данный момент эмбедов
	 *
	 * @param int $colorHex
	 *
	 * @return void
	 */
	public function setEmbedColor(int $colorHex): void {
		foreach ($this->embeds as &$embed) {
			$embed['color'] = $colorHex;
		}
	}

	/**
	 * @param $key
	 *
	 * @return void
	 */
	public function sendWebhook($key) {
		$messageBase = [];
		if ($this->username) {
			$messageBase['username'] = $this->username;
		}
		$message = $messageBase;
		if ($this->content) {
			if (Str::length($this->content) > self::CONTENT_LIMIT) {
				$contentParts = self::splitTextByLength($this->content, self::CONTENT_LIMIT);
				$lastKey = array_key_last($contentParts);
				foreach ($contentParts as $key => $part) {
					$message['content'] = $part;
					if ($key !== $lastKey) {
						self::makeWebhookRequest($key, $message);
						$message = $messageBase;
					}
				}
			} else {
				$message['content'] = $this->content;
			}
		}
		if ($this->embeds) {
			$length = 0;
			foreach ($this->embeds as $embed) {
				$embedLength = Str::length($embed['title'] ?? '') + Str::length($embed['description'] ?? '');
				if (count($message['embeds'] ?? []) >= self::EMBED_COUNT_LIMIT || $length + $embedLength > self::EMBED_SUM_LIMIT) {
					self::makeWebhookRequest($key, $message);
					$message = $messageBase;
					$length = 0;
				}
				$message['embeds'][] = $embed;
				$length += $embedLength;
			}
		}
		if (!empty($message['content']) || !empty($message['embeds'])) {
			self::makeWebhookRequest($key, $message);
		}
	}

	/**
	 * @param $key
	 * @param $data
	 *
	 * @return void
	 */
	private static function makeWebhookRequest($key, $data): void {
		$response = Http::withBody(json_encode($data), 'application/json')
			->post('https://discord.com/api/webhooks/'.(App::environment('production') ? $key : ApiKeys::getKeyString('test-server-webhook')));
//		dd($response->status(), $response->body());
	}
}