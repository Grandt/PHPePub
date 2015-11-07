<?php
include 'vendor/autoload.php';

use PHPePub\Core\EPub;
use PHPePub\Helpers\CalibreHelper;

error_reporting(E_ALL | E_STRICT);
ini_set('error_reporting', E_ALL | E_STRICT);
ini_set('display_errors', 1);

// Example.
// Create a test book for download.
// ePub uses XHTML 1.1, preferably strict.

$book = new EPub();

$book->setTitle("Test book");
$book->setIdentifier("http://JohnJaneDoePublications.com/books/TestBook.html", EPub::IDENTIFIER_URI); // Could also be the ISBN number, preferred for published books, or a UUID.
$book->setLanguage("en"); // Not needed, but included for the example, Language is mandatory, but EPub defaults to "en". Use RFC3066 Language codes, such as "en", "da", "fr" etc.
$book->setDescription("This is a brief description\nA test ePub book as an example of building a book in PHP");
$book->setAuthor("John Doe Johnson", "Johnson, John Doe");
$book->setPublisher("John and Jane Doe Publications", "http://JohnJaneDoePublications.com/"); // I hope this is a non existent address :)
$book->setDate(time()); // Strictly not needed as the book date defaults to time().
$book->setRights("Copyright and licence information specific for the book."); // As this is generated, this _could_ contain the name or licence information of the user who purchased the book, if needed. If this is used that way, the identifier must also be made unique for the book.
$book->setSourceURL("http://JohnJaneDoePublications.com/books/TestBook.html");

// Insert custom meta data to the book, in this case, Calibre series index information.
CalibreHelper::setCalibreMetadata($book, "PHPePub Test books", "4");

$book->isGifImagesEnabled = TRUE;

$cssData = "body {\n  margin-left: .5em;\n  margin-right: .5em;\n  text-align: justify;\n}\n\np {\n  font-family: serif;\n  font-size: 10pt;\n  text-align: justify;\n  text-indent: 1em;\n  margin-top: 0px;\n  margin-bottom: 1ex;\n}\n\nh1, h2 {\n  font-family: sans-serif;\n  font-style: italic;\n  text-align: center;\n  background-color: #6b879c;\n  color: white;\n  width: 100%;\n}\n\nh1 {\n    margin-bottom: 2px;\n}\n\nh2 {\n    margin-top: -2px;\n    margin-bottom: 2px;\n}\n";
$book->addCSSFile("Styles/styles.css", "css1", $cssData);

$content_start =
"<?xml version=\"1.0\" encoding=\"utf-8\"?>\n"
. "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1//EN\"\n"
. "    \"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd\">\n"
. "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n"
. "<head>"
. "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n"
. "<link rel=\"stylesheet\" type=\"text/css\" href=\"../Styles/styles.css\" />\n"
. "<title>Test Book</title>\n"
. "</head>\n"
. "<body>\n";

$bookEnd = "</body>\n</html>\n";
//$fileDir = './PHPePub/tests/';
$fileDir = '.';
// setting timezone for time functions used for logging to work properly
date_default_timezone_set('Europe/Berlin');

$chapter = $content_start . "<p><img src='demo/512x700_g1.gif' alt='Image' /></p>\n"
        . "<p><img src='demo/rxhVVUP.gif' alt='Animated Gif' /></p>\n"
        . "<p><img src='demo/512x700_2.jpg' alt='none' /></p>\n"
        . "<p><img src='demo/512x700_3.jpg' alt='Demo 2' /></p>\n"
        . "<p><img src='demo/test.svg' alt='Demo SVG 1' /></p>\n"
        . $bookEnd;

$book->setCoverImage('demo/512x700_1.jpg');
//$book->maxImageWidth = 150;
$book->addChapter("Prologue", "Texts/Prologue.html", $content_start . "<h2>Prologue</h2>\n" . $bookEnd);
$book->addChapter("Chapter 1", "Texts/Chapter1.html", $chapter, false, EPub::EXTERNAL_REF_ADD, $fileDir);
$book->addChapter("Chapter 2", "Texts/Chapter2.html", $chapter, false, EPub::EXTERNAL_REF_REPLACE_IMAGES, $fileDir);


$book->finalize();
// Send the book to the client. ".epub" will be appended if missing.
$zipData = $book->sendBook("ExampleBookImg");

// After this point your script should call exit. If anything is written to the output,
// it'll be appended to the end of the book, causing the epub file to become corrupt.

