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

//http://www.php-astux.info/script-compteur-telechargements.php
// Mini config

$filesdir = 'Download/'; // Path where the files to download are stored

/**
 * Protect against register_globals vulnerabilities.
 * This line must be present before any global variable is referenced.
 */
if ( !defined( 'MEDIAWIKI' ) ) {
	die();
}

/**
 * @param $input
 * @param $argv array
 * @param $parser Parser
 * @return int|string
 */
function AfficheDetailsTelechargements( $input, $argv, $parser ) {
	$parser->disableCache();
	$file = $argv['name'];
	$details_type = $argv['type'];

	$req_FileDetails = "SELECT
					{CHAMP}
				FROM
					downloads_files
				WHERE
					filename='" . $file . "';";

	// Read the number or the last date of a downloaded file
	$req_FileDetails = ( $details_type == 'total' ) ?
		str_replace( '{CHAMP}', 'downloaded', $req_FileDetails ) :
		str_replace( '{CHAMP}', 'last_download', $req_FileDetails );

	// Execute the query and return the result
	//$FileDetails = DatabaseMysql.query($req_FileDetails);//mysql_query($req_FileDetails);// or die($req_FileDetails.'<br />'.mysql_error());
	$db = wfGetDB( DB_MASTER );
	$FileDetails = $db->doQuery( $req_FileDetails ); // or die($req_FileDetails.'<br />'.mysql_error());

	if ( mysql_num_rows( $FileDetails ) != 1 ) // File not found
	{
		return 0;
	} else // File found
	{
		$rs = mysql_fetch_array( $FileDetails );

		return ( $details_type == 'total' ) ? $rs['downloaded'] : date( "d/m/Y H:i:s", $rs['last_download'] );
	}
}

//Avoid unstubbing $wgParser on setHook() too early on modern (1.12+) MW versions, as per r35980
if ( defined( 'MW_SUPPORTS_PARSERFIRSTCALLINIT' ) ) {
	$wgHooks['ParserFirstCallInit'][] = 'wfDownloadCounter';
} else {
	$wgExtensionFunctions[] = 'wfDownloadCounter';
}

$wgExtensionCredits['parserhook'][] = array(
	'name' => 'DownloadCounter',
	'version' => '0.1',
	'author' => 'Eric Petit',
	'description' => 'Allows the display of total and last download, Read download counter value',
	'url' => 'http://www.mediawiki.org/wiki/Extension:DownloadCounter',
);

function wfDownloadCounter() {
	global $wgParser;
	# register the extension with the WikiText parser
	# the first parameter is the name of the new tag.
	# In this case it defines the tag <dirlist> ... </dirlist>
	# the second parameter is the callback function for
	# processing the text between the tags
	$wgParser->setHook( 'AfficheDetailsTelechargements', 'AfficheDetailsTelechargements' );
	$wgParser->setHook( 'DownloadFs', 'DownloadFs' );
	return true;
}