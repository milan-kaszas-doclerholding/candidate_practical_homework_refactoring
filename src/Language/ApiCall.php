<?php

namespace Language;

class ApiCall
{
	const GET_LANGUAGE_FILE_RESULT = "<?php
		return array (
			'our models' => 'Unsere Darsteller',
			'favorites' => 'Favoriten',
			'help' => 'Hilfe',
			'account balance' => 'Kontostand',
			'member login' => 'Login',
			'login' => 'Anmelden',
			'sign up' => 'Registrieren',
			'get free credits' => 'GRATIS Kredite',
			'boy' => 'Jungs',
			'tranny' => 'Tranny',
			'transvestite' => 'Transvestit',
			'couple' => 'Paare',
			'group' => 'Gruppe',
			'girl' => 'Mädchen',
			'hermaphrodite' => 'Hermaphroditen',
			'shemale' => 'Shemale',
			'all' => 'Alles',
			'18-22' => '18-22',
			'asian' => 'Asiaten',
			'big tits' => 'Große Titten',
			'blonde' => 'Blond',
			'ebony' => 'Dunkelhäutig',
			'interracial' => 'Interrassisch',
			'latin' => 'Latin',
			'latex' => 'Latex',
			'leather' => 'Leder',
			'mommy' => 'Mami',
			'muscular' => 'Muskulös',
			'skinny' => 'Zierlich',
			'small tits' => 'Kleine Titten',
			'white' => 'Weiß',
			'man' => 'Mann',
			'anal' => 'Anal',
			'woman' => 'Frau'
		);";

	const GET_APPLET_LANGUAGE_FILE_RESULT = '<?xml version="1.0" encoding="UTF-8"?>
		<data>
			<button_go_private value="Privát Műsor indítása"/>
			<button_send value="Küldés"/>
			<button_confirm value="Rendben"/>
			<button_ok value="OK"/>
			<button_close value="Bezárás"/>
			<button_close_private value="Privát vége"/>
			<button_back_to_chat value="Vissza a chat-re"/>
			<button_back_to_cam value="Vissza a kamerához"/>
			<button_back value="Vissza"/>
			<cancel value="Törlés"/>
			<info_surprise value="Lepd meg a modellt"/>
			<info_gallery value="Galéria megtekintése"/>
			<info_gallery_no_image value="{@performerid} nem töltött még fel képeket"/>
			<info_snapshot value="Készíts egy pillanatképet"/>
			<info_add_fav value="Kedvencekhez adás"/>
			<info_rem_fav value="Kedvencekből törlés"/>
			<info_added_fav value="Hozzáadva a kedvencekhez"/>
			<info_removed_fav value="Kedvencekből eltávolítva"/>
			<info_buycredit value="Kreditvásárlás"/>
			<info_camera_on value="Kapcsold be a webkamerád"/>
			<info_camera_off value="Kapcsold ki a webkamerád"/>
			<info_size value="Nézetváltás"/>
			<info_personalinfo value="Modell adatlap"/>
		</data>';

	public static function call($target, $mode, $getParameters, $postParameters)
	{
		if (!isset($getParameters['action']))
		{
			return;
		}

		switch ($getParameters['action'])
		{
			case 'getLanguageFile':
				return self::formatAsResult(self::GET_LANGUAGE_FILE_RESULT);
				break;

			case 'getAppletLanguages':
				return self::formatAsResult(['en']);
				break;

			case 'getAppletLanguageFile':
				return self::formatAsResult(self::GET_APPLET_LANGUAGE_FILE_RESULT);
				break;
		}
	}

	private static function formatAsResult($data)
	{
		return [
			'status' => 'OK',
			'data'   => $data,
		];
	}
}