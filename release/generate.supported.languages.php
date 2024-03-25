<?php

	$providers = array( 'DeepL', 'Google Translate', 'Microsoft Translator', 'Yandex.Translate' );
	$banners = array( 'DeepL' => 'DeepL', 'Google Translate' => 'Google Translate', 'Microsoft Translator' => 'Microsoft Translator', 'Yandex.Translate' => '<a href="http://translate.yandex.com/" target="_blank">Powered by Yandex.Translate</a>' );
	$includes = array( 'DeepL' => 'deepl.locales.php', 'Yandex.Translate' => 'yandex.locales.php', 'Microsoft Translator' => 'microsoft.locales.php', 'Google Translate' => 'google.locales.php' );

	// This should be the path to a GlotPress locales file.
	include( '../../../glotpress/GlotPress/locales/locales.php' );

	$gp_locales = new GP_Locales;
	$locales = $gp_locales->locales;
	$storage = array();
	$length = 0;
	$width = 0;

	foreach( $providers as $provider ) {
		if( mb_strlen( $provider ) > $width ) { $width = mb_strlen( $provider ); }

		$gp_machine_translate_locales = array();

		include( '../' . $includes[$provider] );

		foreach( $gp_machine_translate_locales as $mt_locale => $p_locale ) {
			if( array_key_exists( $mt_locale, $locales ) ) {
				if( mb_strlen( $locales[$mt_locale]->english_name ) > $width ) { $width = mb_strlen( $locales[$mt_locale]->english_name ); }

				$storage[$provider][] = $locales[$mt_locale]->english_name;

				if( count( $storage[$provider] ) > $length ) { $length = count( $storage[$provider] ); }
			}
		}
	}

	$output = '| ';
	$markdown = '| ';
	$wp_md = "\t";

	foreach( $providers as $provider ) {
		sort( $storage[$provider] );

		$padding = str_repeat( ' ', $width - mb_strlen( $provider )  );

		$output .= $provider . $padding . '  ';
		$markdown .= $provider . $padding . ' | ';
		$wp_md .= $provider . $padding . '  ';
	}

	$output = rtrim( $output ) . PHP_EOL;
	$markdown = rtrim( $markdown ) . PHP_EOL;
	$wp_md = rtrim( $wp_md ) . PHP_EOL;

	$output .= '|';
	$markdown .= '|';
	$wp_md .= "\t";

	foreach( $providers as $provider ) {
		$padding = str_repeat( '-', $width );

		$output .= $padding . '  ';
		$markdown .= $padding . '--|';
		$wp_md .= $padding . '  ';
	}

	$output = rtrim( $output ) . PHP_EOL;
	$markdown = rtrim( $markdown ) . PHP_EOL;
	$wp_md = rtrim( $wp_md ) . PHP_EOL;

	for( $i = 0; $i < $length; $i++ ) {
		$line = '';
		$mline = '| ';

		foreach( $providers as $provider ) {
			if( array_key_exists( $i, $storage[$provider] ) ) {
				$locale = $storage[$provider][$i];
				$line .= $locale . str_repeat( ' ', $width - mb_strlen( $locale )  ) . '  ';
				$mline .= $locale . str_repeat( ' ', $width - mb_strlen( $locale )  ) . ' | ';
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
