<?php
include 'vendor/autoload.php';

use \PHPePub\Core\EPub;

error_reporting(E_ALL | E_STRICT);
ini_set('error_reporting', E_ALL | E_STRICT);
ini_set('display_errors', 1);

// Example.
// Create a test book for download.
// ePub uses XHTML 1.1, preferably strict.
// This is the minimalistic version.

// This is for the example, this is the XHTML 1.1 header
$content_start =
"<?xml version=\"1.0\" encoding=\"utf-8\"?>\n"
. "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1//EN\"\n"
. "    \"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd\">\n"
. "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n"
. "<head>"
. "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n"
. "<link rel=\"stylesheet\" type=\"text/css\" href=\"styles.css\" />\n"
. "<title>Test Book</title>\n"
. "</head>\n"
. "<body>\n";

$bookEnd = "</body>\n</html>\n";

// setting timezone for time functions used for logging to work properly
date_default_timezone_set('Europe/Berlin');

$fileDir = './PHPePub';

$book = new EPub(); // no arguments gives us the default ePub 2, lang=en and dir="ltr"

// Title and Identifier are mandatory!
$book->setTitle("Simple Test book");
$book->setIdentifier("http://JohnJaneDoePublications.com/books/TestBookSimple.html", EPub::IDENTIFIER_URI); // Could also be the ISBN number, preferrd for published books, or a UUID.
$book->setLanguage("en"); // Not needed, but included for the example, Language is mandatory, but EPub defaults to "en". Use RFC3066 Language codes, such as "en", "da", "fr" etc.
$book->setDescription("This is a brief description\nA test ePub book as an example of building a book in PHP");
$book->setAuthor("John Doe Johnson", "Johnson, John Doe");
$book->setPublisher("John and Jane Doe Publications", "http://JohnJaneDoePublications.com/"); // I hope this is a non existent address :)
$book->setDate(time()); // Strictly not needed as the book date defaults to time().
$book->setRights("Copyright and licence information specific for the book."); // As this is generated, this _could_ contain the name or licence information of the user who purchased the book, if needed. If this is used that way, the identifier must also be made unique for the book.
$book->setSourceURL("http://JohnJaneDoePublications.com/books/TestBookSimple.html");

// A book need styling, in this case we use static text, but it could have been a file.
$cssData = "body {\n  margin-left: .5em;\n  margin-right: .5em;\n  text-align: justify;\n}\n\np {\n  font-family: serif;\n  font-size: 10pt;\n  text-align: justify;\n  text-indent: 1em;\n  margin-top: 0px;\n  margin-bottom: 1ex;\n}\n\nh1, h2 {\n  font-family: sans-serif;\n  font-style: italic;\n  text-align: center;\n  background-color: #6b879c;\n  color: white;\n  width: 100%;\n}\n\nh1 {\n    margin-bottom: 2px;\n}\n\nh2 {\n    margin-top: -2px;\n    margin-bottom: 2px;\n}\n";
$book->addCSSFile("styles.css", "css1", $cssData);

// Add cover page
$cover = $content_start . "<h1>Test Book</h1>\n<h2>By: John Doe Johnson</h2>\n" . $bookEnd;
$book->addChapter("Notices", "Cover.html", $cover);

$chapter1 = $content_start . "<h1>Chapter 1</h1>\n"
    . "<h2>Lorem ipsum</h2>\n"
    . "<p>Lorem ipsum dolor sit <!-- test comment -->amet, consectetur adipiscing elit. Donec magna lorem, mattis sit amet porta vitae, consectetur ut eros. Nullam id mattis lacus. In eget neque magna, congue imperdiet nulla. Aenean erat lacus, imperdiet a adipiscing non, dignissim eget felis. Nulla facilisi. Vivamus sit amet lorem eget mauris dictum pharetra. In mauris nulla, placerat a accumsan ac, mollis sit amet ligula. Donec eget facilisis dui. Cras elit quam, imperdiet at malesuada vitae, luctus id orci. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Pellentesque eu libero in leo ultrices tristique. Etiam quis ornare massa. Donec in velit leo. Sed eu ante tortor.</p>\n"
    . "<p><img src=\"http://www.grandt.com/ePub/AnotherHappilyMarriedCouple.jpg\" alt=\"Test Image retrieved off the internet: Another happily married couple\" />Nullam at tempus enim. Nunc et augue non lectus consequat rhoncus ac a odio. Morbi et tellus eget nisi volutpat tincidunt. Curabitur tristique neque tincidunt purus blandit bibendum. Maecenas eleifend sem quis magna semper id pulvinar nisi porttitor. In in lectus accumsan eros tristique pharetra sit amet ac nulla. Nam vitae felis et orci congue porta nec non ipsum. Donec pretium blandit accumsan. In aliquam lacinia nisi, ut venenatis mauris condimentum ut. Morbi rutrum orci et nisl accumsan euismod. Etiam viverra luctus sem pellentesque suscipit. Aliquam ultricies egestas risus at eleifend. Ut lacinia, tortor non varius malesuada, massa diam aliquet augue, vitae tempor metus tellus eget diam. Nulla vel augue eu elit adipiscing egestas. Duis et nulla est, ac congue arcu. Phasellus semper, ipsum et blandit rutrum, erat ante semper quam, at iaculis quam tellus sed neque.</p>\n"
    . "<p>Pellentesque vulputate sollicitudin justo, at <!-- < !-- we -- > -->faucibus nisl convallis in. Nulla facilisi. Curabitur nec mauris eu justo ultricies ultricies gravida eu ipsum. Pellentesque at nunc velit, vitae congue nisl. Nam varius imperdiet leo eu accumsan. Nullam elementum fermentum diam euismod porttitor. Etiam sed pellentesque ante. Donec in est elementum mi tempor consectetur. Fusce orci lorem, mollis at tincidunt eget, fringilla sed nunc. Ut consectetur condimentum condimentum. Phasellus sed felis non massa gravida euismod ut in tellus. Curabitur suscipit pharetra sapien vitae dignissim. Morbi id arcu nec ante viverra lobortis vitae nec quam. Mauris id gravida odio. Nunc non sem nisi. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Pellentesque hendrerit volutpat nisl id elementum. Vivamus lobortis iaculis nisi, sit amet tristique risus porttitor vel. Suspendisse potenti.</p>\n"
    . "<p>Quisque aliquet sapien leo, vitae eleifend dolor. Fusce quis tincidunt nunc. Nam nec purus nulla, ac eleifend lorem. Curabitur eu quam et nibh egestas mattis. Maecenas eget felis augue. Integer scelerisque commodo urna, a pulvinar tortor euismod et. Praesent in nunc sapien. Ut iaculis auctor neque, sit amet rutrum est faucibus vitae. Sed a sagittis quam. Quisque interdum luctus fringilla. Vestibulum vitae nunc in felis luctus ultricies at id magna. Nam volutpat sapien ac lorem interdum pellentesque. Suspendisse faucibus, leo vitae laoreet interdum, mi mi pulvinar neque, sit amet tristique sapien nulla nec dolor. Etiam non ligula augue.</p>\n"
    . "<p>Vivamus purus elit, ornare eget accumsan ut, luctus et orci. Sed vestibulum turpis ut quam vehicula id hendrerit velit suscipit. Pellentesque pulvinar, libero vitae sagittis scelerisque, felis ante faucibus risus, ut viverra velit mi at tortor. Aliquam lacinia condimentum felis, eu elementum ligula laoreet vitae. Sed placerat tempus turpis a fringilla. Etiam porta accumsan feugiat. Phasellus et cursus magna. Suspendisse vitae odio sit amet urna vulputate consectetur. Vestibulum massa magna, sagittis at dictum vitae, sagittis scelerisque erat. Donec viverra tincidunt lacus. Maecenas fermentum erat et mauris tincidunt sed eleifend quam tempus. In at augue mi, in tincidunt arcu. Duis dapibus aliquet mi, ac ullamcorper est semper quis. Sed nec nulla nec odio malesuada viverra id sed nulla. Donec lobortis euismod aliquam. Praesent sit amet dolor quis lacus auctor lobortis. In hac habitasse platea dictumst. Sed at nisi sed nisi ullamcorper pellentesque. Vivamus eget enim sem, non laoreet leo. Sed vel odio lacus.</p>\n"
    . $bookEnd . "<!-- asdfasasdasfsf -- >";
$book->addChapter("Chapter 1: Lorem ipsum", "Chapter001.html", $chapter1, true, EPub::EXTERNAL_REF_ADD);

$chapter2 = $content_start . "<h1>Chapter 2</h1>\n"
    . "<h2>Vivamus bibendum massa</h2>\n"
    . "<p>Vivamus bibendum massa<!-- test comment <!-- nested comments are not allowed. --> --> ac magna congue gravida. Curabitur nulla ante, accumsan sit amet luctus a, fermentum ut diam. Maecenas porttitor faucibus mattis. Ut auctor aliquet ligula nec posuere. Nullam arcu turpis, dapibus sit amet tempor nec, cursus at augue. Aliquam sed sem velit, id sagittis mauris. Donec sed ipsum nisi, id scelerisque felis. Cras lacus est, fermentum in ultricies eu, congue in elit. Nulla tincidunt posuere eros, eget suscipit tellus porta vel. Aliquam ut sollicitudin libero. Suspendisse potenti. Sed cursus dignissim nulla in elementum. Aliquam id quam justo, sit amet laoreet ligula. Etiam pellentesque tellus a nisi commodo eu sodales ante commodo. Vestibulum ultricies sapien arcu. Proin nunc mauris, ultrices id imperdiet ac, malesuada ac nunc. Nunc a mi quis nunc ultricies rhoncus. Mauris pellentesque eros eu augue congue ac tincidunt est gravida.</p>\n"
    . "<p>Integer lobortis <!--\n  Multi line\n test comment\n-->facilisis magna, non tristique sem facilisis ut. Sed id nisi diam. Nulla viverra lectus ut purus tempus sagittis. Quisque dictum enim tempus ipsum mollis blandit. Cras in mi non nulla imperdiet fringilla at blandit urna. Donec vel dui quis sem congue ullamcorper nec a massa. Vivamus in dui nunc. Donec sit amet augue odio, at imperdiet lacus. Mauris sit amet magna justo. Maecenas ultrices orci ultrices sapien ornare eget consequat nisl tristique. Integer non mi ac eros vehicula pharetra. Curabitur risus augue, sollicitudin vitae pharetra interdum, sollicitudin sit amet magna. Nunc sit amet est lacus, vel sodales elit. Duis dolor lorem, convallis eu dignissim quis, vulputate at nibh.</p>\n"
    . "<p>Praesent gravida, sapien aliquet interdum elementum, magna mauris hendrerit eros, blandit posuere lectus neque sed massa. Cras ultricies rhoncus mi, vitae posuere ligula scelerisque sit amet. Cras porttitor congue odio, sit amet tristique magna euismod id. Cras enim dolor, scelerisque eget egestas vel, consectetur vel purus. Aenean et convallis felis. Mauris in arcu sollicitudin ipsum lobortis fringilla. Suspendisse felis mauris, convallis ac blandit interdum, imperdiet eget massa. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris scelerisque velit quis augue commodo tristique. Maecenas dictum dui congue enim tristique vel mattis neque luctus. Fusce neque dui, laoreet suscipit varius sed, mattis sit amet diam. Nullam elementum, ante non cursus imperdiet, eros dui placerat elit, sit amet elementum erat risus eget nunc.</p>\n"
    . "<p id=\"internalLink\">Nam tellus nibh, vehicula a laoreet non, fermentum vel leo. Proin id augue tellus. Donec placerat pharetra interdum. Aliquam vestibulum viverra bibendum. Nullam auctor congue tortor. Sed sagittis, massa ac cursus malesuada, ipsum velit aliquam lectus, quis tincidunt tellus risus id justo. Suspendisse sodales adipiscing eros, ut pulvinar eros suscipit in. Fusce vel libero id urna blandit pharetra. Cras aliquam suscipit ultrices. Vivamus luctus tristique vestibulum. Nam placerat dolor ipsum. Nulla vitae tristique sapien. Nulla laoreet ante ut elit dictum ultricies. Fusce mi tortor, commodo sit amet tincidunt semper, pellentesque nec ante. Vestibulum nec dui at massa adipiscing pulvinar. Integer ultrices tristique odio, iaculis bibendum metus fringilla id. Ut ac elit ac enim convallis dignissim pharetra id purus. Nunc pulvinar blandit nulla, in ornare erat condimentum id. Sed sit amet placerat est. Curabitur pretium tellus in velit aliquet eu dictum arcu consectetur.</p>\n"
    . "<p>In hac habitasse platea dictumst. Integer lectus augue, varius nec rutrum non, fringilla eu neque. Curabitur a gravida velit. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Quisque vestibulum orci ac ligula interdum dapibus. Maecenas sollicitudin aliquet libero in sodales. In tempor orci vitae nisi imperdiet at varius sem dignissim. Aenean tortor libero, pellentesque eget hendrerit id, ullamcorper in justo. Sed euismod egestas est vitae convallis. Nunc tempus lacinia purus condimentum mattis. Sed id elementum est. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. In nec tempus eros. </p>\n"
    . $bookEnd;
$book->addChapter("Chapter 2: Vivamus bibendum massa", "Chapter002.html", $chapter2, true, EPub::EXTERNAL_REF_ADD);

$book->finalize(); // Finalize the book, and build the archive.

// Send the book to the client. ".epub" will be appended if missing.
$zipData = $book->sendBook("ExampleBookSimple");

// After this point your script should call exit. If anything is written to the output,
// it'll be appended to the end of the book, causing the epub file to become corrupt.
