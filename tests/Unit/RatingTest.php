<?php

namespace Tests\Unit;

use App\Classes\DiscordMessage;
use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;

/**
 * Тестирование методов для работы с рейтином
 */
class RatingTest extends TestCase
{
    /**
     * Тестирование разбиения длинного текста на эмбеды
     *
     * @return void
     */
    public function test_big_text_to_embed(): void
    {
		// короткий тест короче 4096 символов
		$shortText = 'Short text';
		$discordMessage = new DiscordMessage();
		$discordMessage->setBigEmbedText($shortText);
		$this->assertEquals([[
			'description' => $shortText,
			'type' => 'rich',
		]], $discordMessage->embeds, 'Создание эмбеда из короткого текста');

		// длина каждой строки меньше 4096 символов, сумма первых четырёх так же меньше 4096, но сумма всех пяти больше
		/** @noinspection SpellCheckingInspection */
		$mediumStrings = [
			'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Congue nisi vitae suscipit tellus. Vulputate eu scelerisque felis imperdiet proin fermentum leo vel. Felis donec et odio pellentesque diam volutpat commodo sed egestas. Volutpat sed cras ornare arcu dui. Vitae ultricies leo integer malesuada nunc. In hac habitasse platea dictumst vestibulum rhoncus est. Accumsan sit amet nulla facilisi morbi tempus iaculis urna id. Egestas tellus rutrum tellus pellentesque eu tincidunt tortor. Enim eu turpis egestas pretium. Sem et tortor consequat id porta nibh venenatis cras. Imperdiet massa tincidunt nunc pulvinar sapien et ligula ullamcorper malesuada.',
			'Ac odio tempor orci dapibus ultrices in iaculis nunc sed. Leo duis ut diam quam nulla porttitor massa id. Dapibus ultrices in iaculis nunc sed augue lacus. A condimentum vitae sapien pellentesque habitant. Ornare massa eget egestas purus. Vulputate odio ut enim blandit. Tincidunt eget nullam non nisi est sit amet facilisis magna. Tempor id eu nisl nunc. Quisque sagittis purus sit amet volutpat consequat mauris nunc congue. Feugiat nisl pretium fusce id velit. Molestie a iaculis at erat. Dictumst vestibulum rhoncus est pellentesque elit ullamcorper dignissim. Vestibulum lorem sed risus ultricies tristique nulla aliquet. Egestas dui id ornare arcu odio ut sem. Enim nec dui nunc mattis enim ut tellus. Sed elementum tempus egestas sed sed risus pretium. Posuere sollicitudin aliquam ultrices sagittis orci a scelerisque purus semper. Congue quisque egestas diam in. Consectetur purus ut faucibus pulvinar elementum integer enim neque volutpat. Id venenatis a condimentum vitae sapien.',
			'Mattis ullamcorper velit sed ullamcorper morbi tincidunt ornare. Ante in nibh mauris cursus mattis molestie. Amet commodo nulla facilisi nullam vehicula ipsum. Velit laoreet id donec ultrices tincidunt arcu. Praesent tristique magna sit amet purus gravida quis. Facilisi cras fermentum odio eu feugiat pretium nibh ipsum consequat. Aliquam id diam maecenas ultricies mi eget. Dignissim convallis aenean et tortor at risus viverra adipiscing at. Lectus urna duis convallis convallis. Pellentesque id nibh tortor id aliquet lectus proin nibh nisl. Nunc aliquet bibendum enim facilisis. In iaculis nunc sed augue lacus viverra.',
			'Nibh cras pulvinar mattis nunc sed blandit libero. Dictumst vestibulum rhoncus est pellentesque elit ullamcorper dignissim cras. Lacus laoreet non curabitur gravida arcu ac tortor dignissim convallis. Molestie at elementum eu facilisis. Sed enim ut sem viverra aliquet. Sit amet mauris commodo quis imperdiet massa tincidunt nunc pulvinar. Fringilla est ullamcorper eget nulla facilisi etiam. Et netus et malesuada fames ac turpis egestas. Aliquam faucibus purus in massa tempor nec feugiat nisl. Odio eu feugiat pretium nibh ipsum consequat nisl vel. Arcu risus quis varius quam quisque id. Morbi enim nunc faucibus a pellentesque sit amet porttitor eget. Metus aliquam eleifend mi in nulla posuere sollicitudin aliquam ultrices. Tortor at auctor urna nunc id cursus metus aliquam eleifend. Donec pretium vulputate sapien nec sagittis aliquam. Risus nullam eget felis eget nunc lobortis mattis aliquam. Quam adipiscing vitae proin sagittis nisl rhoncus mattis rhoncus. Amet commodo nulla facilisi nullam.',
			'Convallis a cras semper auctor. Gravida cum sociis natoque penatibus et magnis. Senectus et netus et malesuada fames ac turpis egestas sed. Nibh cras pulvinar mattis nunc sed blandit libero. Dui id ornare arcu odio ut sem nulla pharetra diam. Proin nibh nisl condimentum id venenatis a condimentum vitae sapien. Erat velit scelerisque in dictum non consectetur a. Imperdiet dui accumsan sit amet nulla facilisi. Egestas egestas fringilla phasellus faucibus scelerisque eleifend donec pretium vulputate. Tempus iaculis urna id volutpat lacus laoreet non. Ullamcorper malesuada proin libero nunc consequat interdum varius sit amet. Arcu felis bibendum ut tristique et egestas. Accumsan in nisl nisi scelerisque eu. Volutpat diam ut venenatis tellus in metus vulputate eu. Nullam eget felis eget nunc lobortis mattis aliquam faucibus.',
		];
		$longTextEmbeds = [
			[
				'title' => 'Часть 1',
				'description' => implode("\n", array_slice($mediumStrings, 0, 4)),
				'type' => 'rich',
			],
			[
				'title' => 'Часть 2',
				'description' => $mediumStrings[4],
				'type' => 'rich',
			],
		];
		$longTextLF = implode("\n", $mediumStrings);
		$longTextCRLF = implode("\r\n", $mediumStrings);
		$discordMessage->setBigEmbedText($longTextLF);
		$this->assertEquals($longTextEmbeds, $discordMessage->embeds, 'Создание эмбедов из длинного текста (LF)');
		$discordMessage->setBigEmbedText($longTextCRLF);
		$this->assertEquals($longTextEmbeds, $discordMessage->embeds, 'Создание эмбедов из длинного текста (CRLF)');

		// строка длиннее DiscordMessage::EMBED_DESCRIPTION_LIMIT символов без переносов из двух частей
		/** @noinspection SpellCheckingInspection */
		$longStringPart1 = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Congue nisi vitae suscipit tellus. Vulputate eu scelerisque felis imperdiet proin fermentum leo vel. Felis donec et odio pellentesque diam volutpat commodo sed egestas. Volutpat sed cras ornare arcu dui. Vitae ultricies leo integer malesuada nunc. In hac habitasse platea dictumst vestibulum rhoncus est. Accumsan sit amet nulla facilisi morbi tempus iaculis urna id. Egestas tellus rutrum tellus pellentesque eu tincidunt tortor. Enim eu turpis egestas pretium. Sem et tortor consequat id porta nibh venenatis cras. Imperdiet massa tincidunt nunc pulvinar sapien et ligula ullamcorper malesuada. Ac odio tempor orci dapibus ultrices in iaculis nunc sed. Leo duis ut diam quam nulla porttitor massa id. Dapibus ultrices in iaculis nunc sed augue lacus. A condimentum vitae sapien pellentesque habitant. Ornare massa eget egestas purus. Vulputate odio ut enim blandit. Tincidunt eget nullam non nisi est sit amet facilisis magna. Tempor id eu nisl nunc. Quisque sagittis purus sit amet volutpat consequat mauris nunc congue. Feugiat nisl pretium fusce id velit. Molestie a iaculis at erat. Dictumst vestibulum rhoncus est pellentesque elit ullamcorper dignissim. Vestibulum lorem sed risus ultricies tristique nulla aliquet. Egestas dui id ornare arcu odio ut sem. Enim nec dui nunc mattis enim ut tellus. Sed elementum tempus egestas sed sed risus pretium. Posuere sollicitudin aliquam ultrices sagittis orci a scelerisque purus semper. Congue quisque egestas diam in. Consectetur purus ut faucibus pulvinar elementum integer enim neque volutpat. Id venenatis a condimentum vitae sapien. Mattis ullamcorper velit sed ullamcorper morbi tincidunt ornare. Ante in nibh mauris cursus mattis molestie. Amet commodo nulla facilisi nullam vehicula ipsum. Velit laoreet id donec ultrices tincidunt arcu. Praesent tristique magna sit amet purus gravida quis. Facilisi cras fermentum odio eu feugiat pretium nibh ipsum consequat. Aliquam id diam maecenas ultricies mi eget. Dignissim convallis aenean et tortor at risus viverra adipiscing at. Lectus urna duis convallis convallis. Pellentesque id nibh tortor id aliquet lectus proin nibh nisl. Nunc aliquet bibendum enim facilisis. In iaculis nunc sed augue lacus viverra. Nibh cras pulvinar mattis nunc sed blandit libero. Dictumst vestibulum rhoncus est pellentesque elit ullamcorper dignissim cras. Lacus laoreet non curabitur gravida arcu ac tortor dignissim convallis. Molestie at elementum eu facilisis. Sed enim ut sem viverra aliquet. Sit amet mauris commodo quis imperdiet massa tincidunt nunc pulvinar. Fringilla est ullamcorper eget nulla facilisi etiam. Et netus et malesuada fames ac turpis egestas. Aliquam faucibus purus in massa tempor nec feugiat nisl. Odio eu feugiat pretium nibh ipsum consequat nisl vel. Arcu risus quis varius quam quisque id. Morbi enim nunc faucibus a pellentesque sit amet porttitor eget. Metus aliquam eleifend mi in nulla posuere sollicitudin aliquam ultrices. Tortor at auctor urna nunc id cursus metus aliquam eleifend. Donec pretium vulputate sapien nec sagittis aliquam. Risus nullam eget felis eget nunc lobortis mattis aliquam. Quam adipiscing vitae proin sagittis nisl rhoncus mattis rhoncus. Amet commodo nulla facilisi nullam. Convallis a cras semper auctor. Gravida cum sociis natoque penatibus et magnis. Senectus et netus et malesuada fames ac turpis egestas sed. Nibh cras pulvinar mattis nunc sed blandit libero. Dui id ornare arcu odio ut sem nulla pharetra diam. Proin nibh nisl condimentum id venenatis a condimentum vitae sapien. Erat velit scelerisque in dictum non consectetur a. Imperdiet dui accumsan sit amet nulla facilisi. Egestas egestas fringilla phasellus faucibus scelerisque eleifend donec pretium vulputate. Tempus iaculis urna id volutpat lacus laoreet non. Ullamcorper malesuada proin libero nunc consequat interdum varius sit amet. Arcu felis bibendum ut tristique et egestas. Accumsan in nisl nisi scelerisque eu. Volutpat diam ut venenatis tellus in';
		/** @noinspection SpellCheckingInspection */
		$longStringPart2 = 'metus vulputate eu. Nullam eget felis eget nunc lobortis mattis aliquam faucibus.';
		$discordMessage->setBigEmbedText("$longStringPart1 $longStringPart2");
		$this->assertEquals([
			[
				'title' => 'Часть 1',
				'description' => $longStringPart1,
				'type' => 'rich',
			],
			[
				'title' => 'Часть 2',
				'description' => $longStringPart2,
				'type' => 'rich',
			],
		], $discordMessage->embeds, 'Создание эмбедов из длинной строки без переносов');

		// строка длинее 4096 символов без пробелов
		$longString = Str::repeat('a', 4196);
		$discordMessage->setBigEmbedText($longString);
		$this->assertEquals([
			[
				'title' => 'Часть 1',
				'description' => Str::repeat('a', 4096),
				'type' => 'rich',
			],
			[
				'title' => 'Часть 2',
				'description' => Str::repeat('a', 100),
				'type' => 'rich',
			],
		], $discordMessage->embeds, 'Создание эмбедов из длинной строки без пробелов');
    }
}
