<?php

	$providers = array( 'Google Translate', 'Microsoft Translator', 'transltr.org', 'Yandex.Translate' );
	$banners = array( 'Google Translate' => 'Google Translate', 'Microsoft Translator' => 'Microsoft Translator', 'transltr.org' => 'transltr.org', 'Yandex.Translate' => '<a href="http://translate.yandex.com/" target="_blank">Powered by Yandex.Translate</a>' );
	$includes = array( 'Yandex.Translate' => 'yandex.locales.php', 'Microsoft Translator' => 'microsoft.locales.php', 'Google Translate' => 'google.locales.php', 'transltr.org' => 'transltr.locales.php' );

	include( '../../GlotPress - GlotPress-WP/locales/locales.php' );
	
	$gp_locales = new GP_Locales;
	$locales = $gp_locales->locales;
	$storage = array();
	$length = 0;
	$plength = 0;
	
	foreach( $providers as $provider ) {
		if( strlen( $provider ) > $plength ) { $plength = strlen( $provider ); }
		
		$gp_machine_translate_locales = array();
		
		include( '../' . $includes[$provider] );
		
		foreach( $gp_machine_translate_locales as $mt_locale => $p_locale ) {
			if( array_key_exists( $mt_locale, $locales ) ) {
				$storage[$provider][] = $locales[$mt_locale]->english_name;
				
				if( count( $storage[$provider] ) > $length ) { $length = count( $storage[$provider] ); }
			}
		}
	}

	$output = '';
	$markdown = '';
	foreach( $providers as $provider ) {
		sort( $storage[$provider] );
		$output .= $provider . str_repeat( ' ', $plength - strlen( $provider )  ) . '  ';
		$markdown .= $provider . str_repeat( ' ', $plength - strlen( $provider )  ) . ' | ';
	}
	
	$output = trim( $output ) . PHP_EOL;
	$markdown = trim( $markdown ) . PHP_EOL;
	
	foreach( $providers as $provider ) {
		$output .= str_repeat( '-', $plength ) . '  ';
		$markdown .= str_repeat( '-', $plength ) . ' | ';
	}
	
	$output = trim( $output ) . PHP_EOL;
	$markdown = trim( $markdown ) . PHP_EOL;

	for( $i = 0; $i < $length; $i++ ) {
		$line = '';
		$mline = '';
		
		foreach( $providers as $provider ) {
			if( array_key_exists( $i, $storage[$provider] ) ) {
				$locale = $storage[$provider][$i];
				$line .= $locale . str_repeat( ' ', $plength - strlen( $locale )  ) . '  ';
				$mline .= $locale . str_repeat( ' ', $plength - strlen( $locale )  ) . ' | ';
			} else {
				$line .= str_repeat( ' ', $plength ) . '  ';
				$mline .= str_repeat( ' ', $plength ) . ' | ';
			}
		}
			
		$output .= trim( $line ) . PHP_EOL;
		$markdown .= trim( $mline ) . PHP_EOL;
	}
	
	file_put_contents( 'provider-chart.ascii.txt', $output );
	file_put_contents( 'provider-chart.markdown.txt', $markdown );
	
