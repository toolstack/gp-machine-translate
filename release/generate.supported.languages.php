<?php

	$providers = array( 'Google Translate', 'Microsoft Translator', 'transltr.org', 'Yandex.Translate' );
	$banners = array( 'Google Translate' => 'Google Translate', 'Microsoft Translator' => 'Microsoft Translator', 'transltr.org' => 'transltr.org', 'Yandex.Translate' => '<a href="http://translate.yandex.com/" target="_blank">Powered by Yandex.Translate</a>' );
	$includes = array( 'Yandex.Translate' => 'yandex.locales.php', 'Microsoft Translator' => 'microsoft.locales.php', 'Google Translate' => 'google.locales.php', 'transltr.org' => 'transltr.locales.php' );

	include( '../../GlotPress - GlotPress-WP/locales/locales.php' );
	
	$gp_locales = new GP_Locales;
	$locales = $gp_locales->locales;
	$storage = array();
	$length = 0;
	$width = 0;
	
	foreach( $providers as $provider ) {
		if( strlen( $provider ) > $width ) { $width = strlen( $provider ); }
		
		$gp_machine_translate_locales = array();
		
		include( '../' . $includes[$provider] );
		
		foreach( $gp_machine_translate_locales as $mt_locale => $p_locale ) {
			if( array_key_exists( $mt_locale, $locales ) ) {
				if( strlen( $locales[$mt_locale]->english_name ) > $width ) { $width = strlen( $locales[$mt_locale]->english_name ); }
				
				$storage[$provider][] = $locales[$mt_locale]->english_name;
				
				if( count( $storage[$provider] ) > $length ) { $length = count( $storage[$provider] ); }
			}
		}
	}

	$output = '';
	$markdown = '';
	$wp_md = "\t";
	
	foreach( $providers as $provider ) {
		sort( $storage[$provider] );
		
		$padding = str_repeat( ' ', $width - strlen( $provider )  );
		
		$output .= $provider . $padding . '  ';
		$markdown .= $provider . $padding . ' | ';
		$wp_md .= $provider . $padding . '  ';
	}
	
	$output = rtrim( $output ) . PHP_EOL;
	$markdown = rtrim( $markdown ) . PHP_EOL;
	$wp_md = rtrim( $wp_md ) . PHP_EOL;
	
	$wp_md .= "\t";
	
	foreach( $providers as $provider ) {
		$padding = str_repeat( '-', $width );
		
		$output .= $padding . '  ';
		$markdown .= $padding . '-|-';
		$wp_md .= $padding . '  ';
	}
	
	$output = rtrim( $output ) . PHP_EOL;
	$markdown = rtrim( $markdown ) . PHP_EOL;
	$wp_md = rtrim( $wp_md ) . PHP_EOL;

	for( $i = 0; $i < $length; $i++ ) {
		$line = '';
		$mline = '';
		
		foreach( $providers as $provider ) {
			if( array_key_exists( $i, $storage[$provider] ) ) {
				$locale = $storage[$provider][$i];
				$line .= $locale . str_repeat( ' ', $width - strlen( $locale )  ) . '  ';
				$mline .= $locale . str_repeat( ' ', $width - strlen( $locale )  ) . ' | ';
			} else {
				$line .= str_repeat( ' ', $width ) . '  ';
				$mline .= str_repeat( ' ', $width ) . ' | ';
			}
		}
			
		$output .= rtrim( $line ) . PHP_EOL;
		$markdown .= rtrim( $mline ) . PHP_EOL;
		$wp_md .= "\t" . rtrim( $line ) . PHP_EOL;
	}
	
	file_put_contents( 'provider-chart.ascii.txt', $output );
	file_put_contents( 'provider-chart.markdown.txt', $markdown );
	file_put_contents( 'provider-chart.wordpress.txt', $wp_md );
