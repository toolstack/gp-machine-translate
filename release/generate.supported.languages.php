<?php

	$providers = array( 'Google Translate', 'Microsoft Translator', 'transltr.org', 'Yandex.Translate' );
	$banners = array( 'Google Translate' => 'Google Translate', 'Microsoft Translator' => 'Microsoft Translator', 'transltr.org' => 'transltr.org', 'Yandex.Translate' => '<a href="http://translate.yandex.com/" target="_blank">Powered by Yandex.Translate</a>' );
	$includes = array( 'Yandex.Translate' => 'yandex.locales.php', 'Microsoft Translator' => 'microsoft.locales.php', 'Google Translate' => 'google.locales.php', 'transltr.org' => 'transltr.locales.php' );

	include( '../../GlotPress - GlotPress-WP/locales/locales.php' );
	
	$gp_locales = new GP_Locales;
	$locales = $gp_locales->locales;
	
	foreach( $providers as $provider ) {
		$gp_machine_translate_locales = array();
		
		include( '../' . $includes[$provider] );
		
		echo PHP_EOL;
		echo $provider . PHP_EOL;
		echo str_repeat( '-', strlen( $provider ) ) . PHP_EOL;
		
		foreach( $gp_machine_translate_locales as $mt_locale => $p_locale ) {
			if( array_key_exists( $mt_locale, $locales ) ) {
				echo $locales[$mt_locale]->english_name . PHP_EOL;
			}
		}
	}