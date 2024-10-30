<?php
/**
 * Красивые числа
 *
 * @return string
 */
function wtik_shortenNumber( $number ) {
	if ( $number >= 1000000 ) {
		return $number / 1000000 . 'M';
	} elseif ( $number >= 1000 ) {
		return $number / 1000 . 'K';
	}

	return $number;
}

