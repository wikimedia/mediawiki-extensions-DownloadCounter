<?php
#
# Author: Eric Petit (Surfzoid) - surfzoid@gmail.com
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License along
# with this program; if not, write to the Free Software Foundation, Inc.,
# 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
# http://www.gnu.org/copyleft/gpl.html

// Code originally based on http://www.php-astux.info/script-compteur-telechargements.php

require_once( __DIR__ . '/includes/AutoLoader.php' );
$filesdir = 'Download/'; // // Path where the files to download are stored
require_once( __DIR__ . '/includes/WebStart.php' );
/**
 * Protect against register_globals vulnerabilities.
 * This line must be present before any global variable is referenced.
 */

// Get the filename argument
$filename = ( isset( $_GET['f'] ) ) ? trim( sprintf( "%s", $_GET['f'] ) ) : '';

if ( $filename != '' ) { // It is not empty, okay.
	// WARNING : Check if the file exist
	if ( !file_exists( $filesdir . $filename ) || !is_file( $filesdir . $filename ) ) {
		exit();
	}

	// // Now execute the query
	$db = wfGetDB( DB_MASTER );
	$db->replace(
		'downloads_files',
		array(
			'filename' => $filename,
			'downloaded = (downloaded+1)',
			'last_download' => time(),
		),
		__METHOD__
	);

	// Query finish, send the file to the user
	header( "Location: " . $filesdir . $filename );
	exit();
}
