<?php
include 'vendor/autoload.php';

use PHPePub\Core\EPub;
use PHPePub\Core\Structure\OPF\DublinCore;
use PHPePub\Core\Logger;
use PHPZip\Zip\File\Zip;

error_reporting(E_ALL | E_STRICT);
ini_set('error_reporting', E_ALL | E_STRICT);
ini_set('display_errors', 1);

// Example.
// Create a test book for download.
// ePub 3 uses a variant of HTML5 called XHTML5
$content_start =
"<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"
. "<html xmlns=\"http://www.w3.org/1999/xhtml\" xmlns:epub=\"http://www.idpf.org/2007/ops\">\n"
. "<head>"
. "<meta http-equiv=\"Default-Style\" content=\"text/html; charset=utf-8\" />\n"
. "<link rel=\"stylesheet\" type=\"text/css\" href=\"styles.css\" />\n"
. "<title>Test Book</title>\n"
. "</head>\n"
. "<body>\n";

$bookEnd = "</body>\n</html>\n";

// setting timezone for time functions used for logging to work properly
date_default_timezone_set('Europe/Berlin');

$log = new Logger("Example", TRUE);

$fileDir = './PHPePub';

// ePub 3 is not fully implemented. but aspects of it is, in order to help implementers.
// ePub 3 uses HTML5, formatted strictly as if it was XHTML but still using just the HTML5 doctype (aka XHTML5)
$book = new EPub(EPub::BOOK_VERSION_EPUB3, "en", EPub::DIRECTION_LEFT_TO_RIGHT); // Default is ePub 2
$log->logLine("new EPub()");
$log->logLine("EPub class version.: " . EPub::VERSION);
$log->logLine("Zip version........: " . Zip::VERSION);
$log->logLine("getCurrentServerURL: " . $book->getCurrentServerURL());
$log->logLine("getCurrentPageURL..: " . $book->getCurrentPageURL());

// Title and Identifier are mandatory!
$book->setTitle("ePub 3 Test book");
$book->setIdentifier("http://JohnJaneDoePublications.com/books/TestBookEPub3.html", EPub::IDENTIFIER_URI); // Could also be the ISBN number, preferred for published books, or a UUID.
$book->setLanguage("en"); // Not needed, but included for the example, Language is mandatory, but EPub defaults to "en". Use RFC3066 Language codes, such as "en", "da", "fr" etc.
$book->setDescription("This is a brief description\nA test ePub book as an example of building a book in PHP");
$book->setAuthor("John Doe Johnson", "Johnson, John Doe");
$book->setPublisher("John and Jane Doe Publications", "http://JohnJaneDoePublications.com/"); // I hope this is a non existent address :)
$book->setDate(time()); // Strictly not needed as the book date defaults to time().
$book->setRights("Copyright and licence information specific for the book."); // As this is generated, this _could_ contain the name or licence information of the user who purchased the book, if needed. If this is used that way, the identifier must also be made unique for the book.
$book->setSourceURL("http://JohnJaneDoePublications.com/books/TestBookEPub3.html");

$book->addDublinCoreMetadata(DublinCore::CONTRIBUTOR, "PHP");

$book->setSubject("Test book");
$book->setSubject("keywords");
$book->setSubject("Chapter levels");

// Insert custom meta data to the book, in this cvase, Calibre series index information.
$book->addCustomMetadata("calibre:series", "PHPePub Test books");
$book->addCustomMetadata("calibre:series_index", "3");

$log->logLine("Set up parameters");

$cssData = "body {\n  margin-left: .5em;\n  margin-right: .5em;\n  text-align: justify;\n}\n\np {\n  font-family: serif;\n  font-size: 10pt;\n  text-align: justify;\n  text-indent: 1em;\n  margin-top: 0px;\n  margin-bottom: 1ex;\n}\n\nh1, h2 {\n  font-family: sans-serif;\n  font-style: italic;\n  text-align: center;\n  background-color: #6b879c;\n  color: white;\n  width: 100%;\n}\n\nh1 {\n    margin-bottom: 2px;\n}\n\nh2 {\n    margin-top: -2px;\n    margin-bottom: 2px;\n}\n";

$log->logLine("Add css");
$book->addCSSFile("styles.css", "css1", $cssData);

// This test requires you have an image, change "demo/cover-image.jpg" to match your location.
$log->logLine("Add Cover Image");
$book->setCoverImage("Cover.jpg", file_get_contents("demo/cover-image.jpg"), "image/jpeg");

$data = '<div class="img-container" id="em_1" style="left: 138px; top: 148px; height: 232px; width: 308px; position: absolute; z-index: 1;"><img src="http://www.grandt.com/test/sample2.gif" style="width:100%;height:100%;"/></div>';

$book->addChapter("Page 1", "page_1.html", $content_start . $data . $bookEnd, FALSE, EPub::EXTERNAL_REF_ADD);

$log->logLine("Add TOC");
$book->buildTOC();

$book->addChapter("Log", "Log.html", $content_start . $log->getLog() . "\n</pre>" . $bookEnd);

if ($book->isLogging) { // Only used in case we need to debug EPub.php.
    $epuplog = $book->getLog();
    $book->addChapter("ePubLog", "ePubLog.html", $content_start . $epuplog . "\n</pre>" . $bookEnd);
}

$book->finalize(); // Finalize the book, and build the archive.

// Send the book to the client. ".epub" will be appended if missing.
$zipData = $book->sendBook("ExampleBook3_2.epub");

// After this point your script should call exit. If anything is written to the output,
// it'll be appended to the end of the book, causing the epub file to become corrupt.
