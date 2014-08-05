iTunes-2-FileMaker
==================

A PHP script to convert iTunes Library XML (metadata) into FileMaker XML (FMPXMLRESULT)

##Usage

run `php itunes2filemaker.php <iTunes Library.xml> [<output file.xml>]`
The resulting XML file can then be imported into FileMaker.

##Configuration
The script can be edited to change the memory limit (defaults to 1G) and the time zone (defaults to UTC).

##Caveats
This has only been tested with FileMaker 11+ and iTunes 11.3, other versions may not work.

##Supported Fields
The following metadata fields are currently supported (more can be added on request):
* Track ID
* Name
* Artist
* Album Artist
* Composer
* Album
* Genre
* Kind
* Size
* Total Time
* Disc Number 
* Disc Count 
* Track Number
* Track Count 
* Year
* Date Modified
* Date Added
* Bit Rate
* Sample Rate
* Comments
* Play Count
* Play Date
* Release Date
* Rating 
* Album Rating
* Album Rating Computed 
* Artwork Count
* Series 
* Season 
* Episode 
* Episode Order 
* Sort Album 
* Sort Artist 
* Sort Name 
* Persistent ID
* Content Rating 
* Track Type
* File Type
* Protected 
* Purchased 
* Has Video 
* HD 
* Video Width 
* Video Height 
* TV Show 
* Location
* File Folder Count
* Library Folder Count