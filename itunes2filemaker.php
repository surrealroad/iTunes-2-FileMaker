#!/usr/bin/php
<?php

/*
===============================
This will convert an iTunes Library XML file to a Filemaker FMPXMLRESULT file

USAGE: itunes2filemaker.php <iTunes Library.xml> [<output file.xml>]

===============================
*/

// set the correct time zone here
date_default_timezone_set("UTC");

// set memory limit to 1 GB
ini_set('memory_limit', '1G');
$debug = false;
$ver = 1;

// for profiling
$starttime = time();

echo "iTunes XML to FileMaker conversion version ".$ver."\n";

// we require only 1  argument to the php file, the second parameter is optional
// $argv is an array that's created automatically when the script is run
if(!$argv[1]) exit("USAGE: itunes2filemaker.php <iTunes Library.xml> [<output file.xml>]\n"); // note the \n for a newline, otherwise the output gets messed up
$filein = $argv[1];

// see if optional second parameter was provided
if(isset($argv[2])) {
	$fileout = $argv[2];
} else {
	// trim the extension from the input file and append .xml to give us the output filename
	// to do this easily, we use the built-in pathinfo function, which returns an array
	$file_parts = pathinfo($filein);
	// join the parts back together, minus the extension
	$fileout = $file_parts['dirname']."/".$file_parts['filename']."-fmp.xml"; // note we have to add a slash to the dirname
}

// verify that the file can be found
if (file_exists($filein)) {
	// output RAM available
	echo "Allocated ".ini_get('memory_limit')." RAM\n";

	if (file_exists($filein)) {
		echo "Reading file $filein\n";
	    $xmlin = simplexml_load_file($filein);
		//$xmlin->registerXPathNamespace("n", "http://www.next.co.uk");

		$trackbase = '//dict/key[.="Tracks"]/following-sibling::dict[1]/dict';
		$tracks = $xmlin->xpath($trackbase);


	    // define the array which will determine what gets exported
	    // path is relative to track

	    $fields = array(
	    	array("label" => 'Track ID', "type" => 'NUMBER', "path" => './key[.="Track ID"]/following-sibling::integer[1]'),
	    	array("label" => 'Name', "type" => 'TEXT', "path" => './key[.="Name"]/following-sibling::string[1]'),
	    	array("label" => 'Artist', "type" => 'TEXT', "path" => './key[.="Artist"]/following-sibling::string[1]'),
	    	array("label" => 'Album Artist', "type" => 'TEXT', "path" => './key[.="Album Artist"]/following-sibling::string[1]'),
	    	array("label" => 'Composer', "type" => 'TEXT', "path" => './key[.="Composer"]/following-sibling::string[1]'),
	    	array("label" => 'Album', "type" => 'TEXT', "path" => './key[.="Album"]/following-sibling::string[1]'),
	    	array("label" => 'Genre', "type" => 'TEXT', "path" => './key[.="Genre"]/following-sibling::string[1]'),
	    	array("label" => 'Kind', "type" => 'TEXT', "path" => './key[.="Kind"]/following-sibling::string[1]'),
	    	array("label" => 'Size', "type" => 'NUMBER', "path" => './key[.="Size"]/following-sibling::integer[1]'),
	    	array("label" => 'Total Time', "type" => 'NUMBER', "path" => './key[.="Total Time"]/following-sibling::integer[1]'),
	    	array("label" => 'Disc Number', "type" => 'NUMBER', "path" => './key[.="Disc Number"]/following-sibling::integer[1]'),
	    	array("label" => 'Disc Count', "type" => 'NUMBER', "path" => './key[.="Disc Count"]/following-sibling::integer[1]'),
	    	array("label" => 'Track Number', "type" => 'NUMBER', "path" => './key[.="Track Number"]/following-sibling::integer[1]'),
	    	array("label" => 'Track Count', "type" => 'NUMBER', "path" => './key[.="Track Count"]/following-sibling::integer[1]'),
	    	array("label" => 'Year', "type" => 'NUMBER', "path" => './key[.="Year"]/following-sibling::integer[1]'),
	    	array("label" => 'Date Modified', "type" => 'TIMESTAMP', "path" => './key[.="Date Modified"]/following-sibling::date[1]'),
	    	array("label" => 'Date Added', "type" => 'TIMESTAMP', "path" => './key[.="Date Added"]/following-sibling::date[1]'),
	    	array("label" => 'Bit Rate', "type" => 'NUMBER', "path" => './key[.="Bit Rate"]/following-sibling::integer[1]'),
	    	array("label" => 'Sample Rate', "type" => 'NUMBER', "path" => './key[.="Sample Rate"]/following-sibling::integer[1]'),
	    	array("label" => 'Comments', "type" => 'TEXT', "path" => './key[.="Comments"]/following-sibling::string[1]'),
	    	array("label" => 'Play Count', "type" => 'NUMBER', "path" => './key[.="Play Count"]/following-sibling::integer[1]'),
	    	array("label" => 'Play Date', "type" => 'TIMESTAMP', "path" => './key[.="Play Date UTC"]/following-sibling::date[1]'),
	    	array("label" => 'Release Date', "type" => 'TIMESTAMP', "path" => './key[.="Release Date"]/following-sibling::date[1]'),
	    	array("label" => 'Rating', "type" => 'NUMBER', "path" => './key[.="Rating"]/following-sibling::integer[1]'),
	    	array("label" => 'Album Rating', "type" => 'NUMBER', "path" => './key[.="Album Rating"]/following-sibling::integer[1]'),
	    	array("label" => 'Album Rating Computed', "type" => 'BOOLEAN', "path" => './key[.="Album Rating Computed"]/following-sibling::*[1]'),
	    	array("label" => 'Artwork Count', "type" => 'NUMBER', "path" => './key[.="Artwork Count"]/following-sibling::integer[1]'),
	    	array("label" => 'Series', "type" => 'TEXT', "path" => './key[.="Series"]/following-sibling::string[1]'),
	    	array("label" => 'Season', "type" => 'NUMBER', "path" => './key[.="Season"]/following-sibling::integer[1]'),
	    	array("label" => 'Episode', "type" => 'TEXT', "path" => './key[.="Episode"]/following-sibling::string[1]'),
	    	array("label" => 'Episode Order', "type" => 'NUMBER', "path" => './key[.="Episode Order"]/following-sibling::integer[1]'),
	    	array("label" => 'Sort Album', "type" => 'TEXT', "path" => './key[.="Sort Album"]/following-sibling::string[1]'),
	    	array("label" => 'Sort Artist', "type" => 'TEXT', "path" => './key[.="Sort Artist"]/following-sibling::string[1]'),
	    	array("label" => 'Sort Name', "type" => 'TEXT', "path" => './key[.="Sort Name"]/following-sibling::string[1]'),
	    	array("label" => 'Persistent ID', "type" => 'TEXT', "path" => './key[.="Persistent ID"]/following-sibling::string[1]'),
	    	array("label" => 'Content Rating', "type" => 'TEXT', "path" => './key[.="Content Rating"]/following-sibling::string[1]'),
	    	array("label" => 'Track Type', "type" => 'TEXT', "path" => './key[.="Track Type"]/following-sibling::string[1]'),
	    	array("label" => 'File Type', "type" => 'NUMBER', "path" => './key[.="File Type"]/following-sibling::integer[1]'),
	    	array("label" => 'Protected', "type" => 'BOOLEAN', "path" => './key[.="Protected"]/following-sibling::*[1]'),
	    	array("label" => 'Purchased', "type" => 'BOOLEAN', "path" => './key[.="Purchased"]/following-sibling::*[1]'),
	    	array("label" => 'Has Video', "type" => 'BOOLEAN', "path" => './key[.="Has Video"]/following-sibling::*[1]'),
	    	array("label" => 'HD', "type" => 'BOOLEAN', "path" => './key[.="HD"]/following-sibling::*[1]'),
	    	array("label" => 'Video Width', "type" => 'NUMBER', "path" => './key[.="Video Width"]/following-sibling::integer[1]'),
	    	array("label" => 'Video Height', "type" => 'NUMBER', "path" => './key[.="Video Height"]/following-sibling::integer[1]'),
	    	array("label" => 'TV Show', "type" => 'BOOLEAN', "path" => './key[.="TV Show"]/following-sibling::*[1]'),
	    	array("label" => 'Location', "type" => 'TEXT', "path" => './key[.="Location"]/following-sibling::string[1]'),
	    	array("label" => 'File Folder Count', "type" => 'NUMBER', "path" => './key[.="File Folder Count"]/following-sibling::integer[1]'),
	    	array("label" => 'Library Folder Count', "type" => 'NUMBER', "path" => './key[.="Library Folder Count"]/following-sibling::integer[1]'),
	    );

	    $tracks = $xmlin->xpath($trackbase);
		if($debug) echo "Debug enabled, outputting first track only\n";
		else echo "Processing ".count($tracks)." tracks\n";


	    // before we actually process the file, we create our XML object
	    // there are several methods, but XMLWriter usually provides a good mix of functionality and ease of use (this is the same approach used in the datatherapy script)
	    // http://php.net/manual/en/intro.xmlwriter.php
	    // Filemaker XML spec: http://www.filemaker.com/help/html/import_export.16.30.html

	    $xml=new XMLWriter();
	    echo "Writing file ".$fileout."\n";

	    $xml->openURI($fileout); //note we could also use openMemory() instead if we didn't want to write to a file
	    $xml->startDocument('1.0','UTF-8');
	    $xml->startElementNS(null, "FMPXMLRESULT", "http://www.filemaker.com/fmpxmlresult"); //the first element is name-spaced
	    $xml->startElement("DATABASE");
	    $xml->writeAttribute("DATEFORMAT", "D/m/yyyy");
	    $xml->writeAttribute("TIMEFORMAT", "k:mm:ss");
	    $xml->writeAttribute("RECORDS", count($tracks));
	    $xml->endElement(); // close DATABASE

	    $xml->setIndent(4); // this is purely to make the file readable
	    // write metadata
	    $xml->startElement("METADATA");
		$columnCount = count($fields); // note that there's a pipe at the end of each line, so this actually returns 1 too many

		// create definitions for each field
		foreach($fields as $field) {
	    	if($field['type'] == "BOOLEAN") $type = "TEXT";
	    	else $type = $field['type'];
	    	$xml->startElement("FIELD");
	    	$xml->writeAttribute("EMPTYOK", "YES"); // always allow empty values for fields
	    	if(isset($field['maxRepetitions']) && $field['maxRepetitions']) {
		    	$xml->writeAttribute("MAXREPEAT", $field['maxRepetitions']);
	    	} else {
		    	$xml->writeAttribute("MAXREPEAT", "1");	// don't allow repeating fields
	    	}
	    	$xml->writeAttribute("NAME", $field['label']);
	    	$xml->writeAttribute("TYPE", $type);
	    	$xml->endElement(); // close FIELD
    	}
    	$xml->endElement(); //close METADATA

    	$xml->startElement("RESULTSET");

	    // we are going to ignore all the optional stuff FMP sticks in the XML file during an export, we only need what's required for import

	    // loop through the tracks
	    foreach($tracks as $track) {

	    	$xml->startElement("ROW");

	    	foreach($fields as $field) { // loop through the fields to capture

	    		$data = $track->xpath($field['path']);
		    	$xml->startElement("COL");
		    	if(isset($field['maxRepetitions']) && $field['maxRepetitions']) {
			    	$rep = $field['maxRepetitions'];
		    	} else {
			    	$rep = 1;
		    	}
		    	for($i=0; $i<$rep; $i++) {
		    		if(isset($data[$i])) $dataString = (string)$data[$i];
		    		else $dataString = null;
			    	if(isset($dataString) && $dataString) {
			    		// convert timestamps
			    		if($field['type'] == "TIMESTAMP") $dataString = date_create_from_format('Y-m-d\TH:i:sT', $dataString)->format("d/m/Y H:i:s");
			    		elseif($field['type'] == "BOOLEAN") {
			    			$data = $track->xpath($field['path']);
			    			$dataString = $data[$i]->getName();
			    		}
			    		$xml->writeElement("DATA", $dataString);
			    	} else {
			    		$xml->writeElement("DATA");
			    		if(isset($field['required']) && $field['required']) echo "WARNING: Required field ".$field['label']." has no data\n";
			    	}
			    	if($debug) {
			    		echo $field['label'];
			    		if($i>0) echo "[".($i+1)."]";
			    		echo ": ".$dataString."\n";
			    	}
		    	}
		    	if(count($data)>$rep) {
		    		echo "Warning: Field '".$field['label']." has multiple matches, output truncated (increase repetition limit)\n";
		    	}
		    	$xml->endElement(); // close COL
	    	}

	    	$xml->endElement(); // close ROW

	    	if($debug) break;

		}

		$xml->endElement(); // close RESULTSET
	    $xml->endElement(); // close FMPXMLRESULT
	    $xml->endDocument(); // close the output file
	} else {
		// return error
	    exit("Cannot open file ($filein)\n");
	}

	// profiling
	$endtime = time();
	$totaltime = ($endtime - $starttime);
	echo "Execution time: ".gmdate("H:i:s", $totaltime)."\n";

} else {
	// return error
    exit('Failed to open '.$filein);
}
