<?php
include 'vendor/autoload.php';

use PHPePub\Core\EPub;
use PHPePub\Core\EPubChapterSplitter;
use PHPePub\Core\Structure\OPF\DublinCore;
use PHPePub\Core\Logger;
use PHPZip\Zip\File\Zip;

error_reporting(E_ALL | E_STRICT);
ini_set('error_reporting', E_ALL | E_STRICT);
ini_set('display_errors', 1);

// Example.
// Create a test book for download.
// ePub uses XHTML 1.1, preferably strict.
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

$log = new Logger("Example", TRUE);

$fileDir = './PHPePub';

// Default is EPub::BOOK_VERSION_EPUB2
$book = new EPub();
$log->logLine("new EPub()");
$log->logLine("EPub class version.: " . EPub::VERSION);
$log->logLine("Zip version........: " . Zip::VERSION);
$log->logLine("getCurrentServerURL: " . $book->getCurrentServerURL());
$log->logLine("getCurrentPageURL..: " . $book->getCurrentPageURL());

// Title and Identifier are mandatory!
$book->setTitle("Test book");
$book->setIdentifier("http://JohnJaneDoePublications.com/books/TestBook.html", EPub::IDENTIFIER_URI); // Could also be the ISBN number, preferred for published books, or a UUID.
$book->setLanguage("en"); // Not needed, but included for the example, Language is mandatory, but EPub defaults to "en". Use RFC3066 Language codes, such as "en", "da", "fr" etc.
$book->setDescription("This is a brief description\nA test ePub book as an example of building a book in PHP");
$book->setAuthor("John Doe Johnson", "Johnson, John Doe");
$book->setPublisher("John and Jane Doe Publications", "http://JohnJaneDoePublications.com/"); // I hope this is a non existent address :)
$book->setDate(time()); // Strictly not needed as the book date defaults to time().
$book->setRights("Copyright and licence information specific for the book."); // As this is generated, this _could_ contain the name or licence information of the user who purchased the book, if needed. If this is used that way, the identifier must also be made unique for the book.
$book->setSourceURL("http://JohnJaneDoePublications.com/books/TestBook.html");

$book->addDublinCoreMetadata(DublinCore::CONTRIBUTOR, "PHP");

$book->setSubject("Test book");
$book->setSubject("keywords");
$book->setSubject("Chapter levels");

// Insert custom meta data to the book, in this cvase, Calibre series index information.
$book->addCustomMetadata("calibre:series", "PHPePub Test books");
$book->addCustomMetadata("calibre:series_index", "1");

$log->logLine("Set up parameters");

$cssData = "body {\n  margin-left: .5em;\n  margin-right: .5em;\n  text-align: justify;\n}\n\np {\n  font-family: serif;\n  font-size: 10pt;\n  text-align: justify;\n  text-indent: 1em;\n  margin-top: 0px;\n  margin-bottom: 1ex;\n}\n\nh1, h2 {\n  font-family: sans-serif;\n  font-style: italic;\n  text-align: center;\n  background-color: #6b879c;\n  color: white;\n  width: 100%;\n}\n\nh1 {\n    margin-bottom: 2px;\n}\n\nh2 {\n    margin-top: -2px;\n    margin-bottom: 2px;\n}\n";

$log->logLine("Add css");
$book->addCSSFile("styles.css", "css1", $cssData);

// This test requires you have an image, change "demo/cover-image.jpg" to match your location.
$log->logLine("Add Cover Image");
$book->setCoverImage("Cover.jpg", file_get_contents("demo/cover-image.jpg"), "image/jpeg");

// A better way is to let EPub handle the image itself, as it may need resizing. Most e-books are only about 600x800
//  pixels, adding mega-pixel images is a waste of place and spends bandwidth. setCoverImage can resize the image.
//  When using this method, the given image path must be the absolute path from the servers Document root.

/* $book->setCoverImage("/absolute/path/to/demo/cover-image.jpg"); */

// setCoverImage can only be called once per book, but can be called at any point in the book creation.
$log->logLine("Set Cover Image");

$cover = $content_start . "<h1>Test Book</h1>\n<h2>By: John Doe Johnson</h2>\n" . $bookEnd;
$book->addChapter("Notices", "Cover.html", $cover);
$book->buildTOC(NULL, "toc", "Table of Contents", TRUE, TRUE);
//    function buildTOC($cssFileName = NULL, $tocCSSClass = "toc", $title = "Table of Contents", $addReferences = TRUE, $addToIndex = FALSE, $tocFileName = "TOC.xhtml") {


$chapter1 = $content_start . "<h1>Chapter 1</h1>\n"
    . "<h2>Lorem ipsum</h2>\n"
    . "<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec magna lorem, mattis sit amet porta vitae, consectetur ut eros. Nullam id mattis lacus. In eget neque magna, congue imperdiet nulla. Aenean erat lacus, imperdiet a adipiscing non, dignissim eget felis. Nulla facilisi. Vivamus sit amet lorem eget mauris dictum pharetra. In mauris nulla, placerat a accumsan ac, mollis sit amet ligula. Donec eget facilisis dui. Cras elit quam, imperdiet at malesuada vitae, luctus id orci. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Pellentesque eu libero in leo ultrices tristique. Etiam quis ornare massa. Donec in velit leo. Sed eu ante tortor.</p>\n"
    . "<p><img src=\"http://www.grandt.com/ePub/AnotherHappilyMarriedCouple.jpg\" alt=\"Test Image retrieved off the internet: Another happily married couple\" />Nullam at tempus enim. Nunc et augue non lectus consequat rhoncus ac a odio. Morbi et tellus eget nisi volutpat tincidunt. Curabitur tristique neque tincidunt purus blandit bibendum. Maecenas eleifend sem quis magna semper id pulvinar nisi porttitor. In in lectus accumsan eros tristique pharetra sit amet ac nulla. Nam vitae felis et orci congue porta nec non ipsum. Donec pretium blandit accumsan. In aliquam lacinia nisi, ut venenatis mauris condimentum ut. Morbi rutrum orci et nisl accumsan euismod. Etiam viverra luctus sem pellentesque suscipit. Aliquam ultricies egestas risus at eleifend. Ut lacinia, tortor non varius malesuada, massa diam aliquet augue, vitae tempor metus tellus eget diam. Nulla vel augue eu elit adipiscing egestas. Duis et nulla est, ac congue arcu. Phasellus semper, ipsum et blandit rutrum, erat ante semper quam, at iaculis quam tellus sed neque.</p>\n"
    . "<p>Pellentesque vulputate sollicitudin justo, at faucibus nisl convallis in. Nulla facilisi. Curabitur nec mauris eu justo ultricies ultricies gravida eu ipsum. Pellentesque at nunc velit, vitae congue nisl. Nam varius imperdiet leo eu accumsan. Nullam elementum fermentum diam euismod porttitor. Etiam sed pellentesque ante. Donec in est elementum mi tempor consectetur. Fusce orci lorem, mollis at tincidunt eget, fringilla sed nunc. Ut consectetur condimentum condimentum. Phasellus sed felis non massa gravida euismod ut in tellus. Curabitur suscipit pharetra sapien vitae dignissim. Morbi id arcu nec ante viverra lobortis vitae nec quam. Mauris id gravida odio. Nunc non sem nisi. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Pellentesque hendrerit volutpat nisl id elementum. Vivamus lobortis iaculis nisi, sit amet tristique risus porttitor vel. Suspendisse potenti.</p>\n"
    . "<p>Quisque aliquet sapien leo, vitae eleifend dolor. Fusce quis tincidunt nunc. Nam nec purus nulla, ac eleifend lorem. Curabitur eu quam et nibh egestas mattis. Maecenas eget felis augue. Integer scelerisque commodo urna, a pulvinar tortor euismod et. Praesent in nunc sapien. Ut iaculis auctor neque, sit amet rutrum est faucibus vitae. Sed a sagittis quam. Quisque interdum luctus fringilla. Vestibulum vitae nunc in felis luctus ultricies at id magna. Nam volutpat sapien ac lorem interdum pellentesque. Suspendisse faucibus, leo vitae laoreet interdum, mi mi pulvinar neque, sit amet tristique sapien nulla nec dolor. Etiam non ligula augue.</p>\n"
    . "<p>Vivamus purus elit, ornare eget accumsan ut, luctus et orci. Sed vestibulum turpis ut quam vehicula id hendrerit velit suscipit. Pellentesque pulvinar, libero vitae sagittis scelerisque, felis ante faucibus risus, ut viverra velit mi at tortor. Aliquam lacinia condimentum felis, eu elementum ligula laoreet vitae. Sed placerat tempus turpis a fringilla. Etiam porta accumsan feugiat. Phasellus et cursus magna. Suspendisse vitae odio sit amet urna vulputate consectetur. Vestibulum massa magna, sagittis at dictum vitae, sagittis scelerisque erat. Donec viverra tincidunt lacus. Maecenas fermentum erat et mauris tincidunt sed eleifend quam tempus. In at augue mi, in tincidunt arcu. Duis dapibus aliquet mi, ac ullamcorper est semper quis. Sed nec nulla nec odio malesuada viverra id sed nulla. Donec lobortis euismod aliquam. Praesent sit amet dolor quis lacus auctor lobortis. In hac habitasse platea dictumst. Sed at nisi sed nisi ullamcorper pellentesque. Vivamus eget enim sem, non laoreet leo. Sed vel odio lacus.</p>\n"
    . $bookEnd;

$chapter2 =
      "<h2>Vivamus bibendum massa</h2>\n"
    . "<p><img src=\"demo/DemoInlineImage.jpg\" alt=\"Demo Inline Image!\" /></p>\n"
    . "<p>Vivamus bibendum massa ac magna congue gravida. Curabitur nulla ante, accumsan sit amet luctus a, fermentum ut diam. Maecenas porttitor faucibus mattis. Ut auctor aliquet ligula nec posuere. Nullam arcu turpis, dapibus sit amet tempor nec, cursus at augue. Aliquam sed sem velit, id sagittis mauris. Donec sed ipsum nisi, id scelerisque felis. Cras lacus est, fermentum in ultricies eu, congue in elit. Nulla tincidunt posuere eros, eget suscipit tellus porta vel. Aliquam ut sollicitudin libero. Suspendisse potenti. Sed cursus dignissim nulla in elementum. Aliquam id quam justo, sit amet laoreet ligula. Etiam pellentesque tellus a nisi commodo eu sodales ante commodo. Vestibulum ultricies sapien arcu. Proin nunc mauris, ultrices id imperdiet ac, malesuada ac nunc. Nunc a mi quis nunc ultricies rhoncus. Mauris pellentesque eros eu augue congue ac tincidunt est gravida.</p>\n"
    . "<p>Integer lobortis facilisis magna, non tristique sem facilisis ut. Sed id nisi diam. Nulla viverra lectus ut purus tempus sagittis. Quisque dictum enim tempus ipsum mollis blandit. Cras in mi non nulla imperdiet fringilla at blandit urna. Donec vel dui quis sem congue ullamcorper nec a massa. Vivamus in dui nunc. Donec sit amet augue odio, at imperdiet lacus. Mauris sit amet magna justo. Maecenas ultrices orci ultrices sapien ornare eget consequat nisl tristique. Integer non mi ac eros vehicula pharetra. Curabitur risus augue, sollicitudin vitae pharetra interdum, sollicitudin sit amet magna. Nunc sit amet est lacus, vel sodales elit. Duis dolor lorem, convallis eu dignissim quis, vulputate at nibh.</p>\n"
    . "<p>Praesent gravida, sapien aliquet interdum elementum, magna mauris hendrerit eros, blandit posuere lectus neque sed massa. Cras ultricies rhoncus mi, vitae posuere ligula scelerisque sit amet. Cras porttitor congue odio, sit amet tristique magna euismod id. Cras enim dolor, scelerisque eget egestas vel, consectetur vel purus. Aenean et convallis felis. Mauris in arcu sollicitudin ipsum lobortis fringilla. Suspendisse felis mauris, convallis ac blandit interdum, imperdiet eget massa. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris scelerisque velit quis augue commodo tristique. Maecenas dictum dui congue enim tristique vel mattis neque luctus. Fusce neque dui, laoreet suscipit varius sed, mattis sit amet diam. Nullam elementum, ante non cursus imperdiet, eros dui placerat elit, sit amet elementum erat risus eget nunc.</p>\n"
    . "<p>Nam tellus nibh, vehicula a laoreet non, fermentum vel leo. Proin id augue tellus. Donec placerat pharetra interdum. Aliquam vestibulum viverra bibendum. Nullam auctor congue tortor. Sed sagittis, massa ac cursus malesuada, ipsum velit aliquam lectus, quis tincidunt tellus risus id justo. Suspendisse sodales adipiscing eros, ut pulvinar eros suscipit in. Fusce vel libero id urna blandit pharetra. Cras aliquam suscipit ultrices. Vivamus luctus tristique vestibulum. Nam placerat dolor ipsum. Nulla vitae tristique sapien. Nulla laoreet ante ut elit dictum ultricies. Fusce mi tortor, commodo sit amet tincidunt semper, pellentesque nec ante. Vestibulum nec dui at massa adipiscing pulvinar. Integer ultrices tristique odio, iaculis bibendum metus fringilla id. Ut ac elit ac enim convallis dignissim pharetra id purus. Nunc pulvinar blandit nulla, in ornare erat condimentum id. Sed sit amet placerat est. Curabitur pretium tellus in velit aliquet eu dictum arcu consectetur.</p>\n"
    . "<p>In hac habitasse platea dictumst. Integer lectus augue, varius nec rutrum non, fringilla eu neque. Curabitur a gravida velit. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Quisque vestibulum orci ac ligula interdum dapibus. Maecenas sollicitudin aliquet libero in sodales. In tempor orci vitae nisi imperdiet at varius sem dignissim. Aenean tortor libero, pellentesque eget hendrerit id, ullamcorper in justo. Sed euismod egestas est vitae convallis. Nunc tempus lacinia purus condimentum mattis. Sed id elementum est. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. In nec tempus eros. </p>\n"
    . $bookEnd;

$chapter3 = array();
$chapter3[] = $content_start . "<h1>Chapter 3</h1>\n"
    . "<h2>Vivamus bibendum massa 3A</h2>\n"
    . "<p>Vivamus bibendum massa ac magna congue gravida. Curabitur nulla ante, accumsan sit amet luctus a, fermentum ut diam. Maecenas porttitor faucibus mattis. Ut auctor aliquet ligula nec posuere. Nullam arcu turpis, dapibus sit amet tempor nec, cursus at augue. Aliquam sed sem velit, id sagittis mauris. Donec sed ipsum nisi, id scelerisque felis. Cras lacus est, fermentum in ultricies eu, congue in elit. Nulla tincidunt posuere eros, eget suscipit tellus porta vel. Aliquam ut sollicitudin libero. Suspendisse potenti. Sed cursus dignissim nulla in elementum. Aliquam id quam justo, sit amet laoreet ligula. Etiam pellentesque tellus a nisi commodo eu sodales ante commodo. Vestibulum ultricies sapien arcu. Proin nunc mauris, ultrices id imperdiet ac, malesuada ac nunc. Nunc a mi quis nunc ultricies rhoncus. Mauris pellentesque eros eu augue congue ac tincidunt est gravida.</p>\n"
    . "<p>Integer lobortis facilisis magna, non tristique sem facilisis ut. Sed id nisi diam. Nulla viverra lectus ut purus tempus sagittis. Quisque dictum enim tempus ipsum mollis blandit. Cras in mi non nulla imperdiet fringilla at blandit urna. Donec vel dui quis sem congue ullamcorper nec a massa. Vivamus in dui nunc. Donec sit amet augue odio, at imperdiet lacus. Mauris sit amet magna justo. Maecenas ultrices orci ultrices sapien ornare eget consequat nisl tristique. Integer non mi ac eros vehicula pharetra. Curabitur risus augue, sollicitudin vitae pharetra interdum, sollicitudin sit amet magna. Nunc sit amet est lacus, vel sodales elit. Duis dolor lorem, convallis eu dignissim quis, vulputate at nibh.</p>\n"
    . "<p>Praesent gravida, sapien aliquet interdum elementum, magna mauris hendrerit eros, blandit posuere lectus neque sed massa. Cras ultricies rhoncus mi, vitae posuere ligula scelerisque sit amet. Cras porttitor congue odio, sit amet tristique magna euismod id. Cras enim dolor, scelerisque eget egestas vel, consectetur vel purus. Aenean et convallis felis. Mauris in arcu sollicitudin ipsum lobortis fringilla. Suspendisse felis mauris, convallis ac blandit interdum, imperdiet eget massa. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris scelerisque velit quis augue commodo tristique. Maecenas dictum dui congue enim tristique vel mattis neque luctus. Fusce neque dui, laoreet suscipit varius sed, mattis sit amet diam. Nullam elementum, ante non cursus imperdiet, eros dui placerat elit, sit amet elementum erat risus eget nunc.</p>\n"
    . "<p>Nam tellus nibh, vehicula a laoreet non, fermentum vel leo. Proin id augue tellus. Donec placerat pharetra interdum. Aliquam vestibulum viverra bibendum. Nullam auctor congue tortor. Sed sagittis, massa ac cursus malesuada, ipsum velit aliquam lectus, quis tincidunt tellus risus id justo. Suspendisse sodales adipiscing eros, ut pulvinar eros suscipit in. Fusce vel libero id urna blandit pharetra. Cras aliquam suscipit ultrices. Vivamus luctus tristique vestibulum. Nam placerat dolor ipsum. Nulla vitae tristique sapien. Nulla laoreet ante ut elit dictum ultricies. Fusce mi tortor, commodo sit amet tincidunt semper, pellentesque nec ante. Vestibulum nec dui at massa adipiscing pulvinar. Integer ultrices tristique odio, iaculis bibendum metus fringilla id. Ut ac elit ac enim convallis dignissim pharetra id purus. Nunc pulvinar blandit nulla, in ornare erat condimentum id. Sed sit amet placerat est. Curabitur pretium tellus in velit aliquet eu dictum arcu consectetur.</p>\n"
    . "<p>In hac habitasse platea dictumst. Integer lectus augue, varius nec rutrum non, fringilla eu neque. Curabitur a gravida velit. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Quisque vestibulum orci ac ligula interdum dapibus. Maecenas sollicitudin aliquet libero in sodales. In tempor orci vitae nisi imperdiet at varius sem dignissim. Aenean tortor libero, pellentesque eget hendrerit id, ullamcorper in justo. Sed euismod egestas est vitae convallis. Nunc tempus lacinia purus condimentum mattis. Sed id elementum est. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. In nec tempus eros. </p>\n"
    . $bookEnd;
$chapter3[] = $content_start . "<h2>Vivamus bibendum massa 3B</h2>\n"
    . "<p>Vivamus bibendum massa ac magna congue gravida. Curabitur nulla ante, accumsan sit amet luctus a, fermentum ut diam. Maecenas porttitor faucibus mattis. Ut auctor aliquet ligula nec posuere. Nullam arcu turpis, dapibus sit amet tempor nec, cursus at augue. Aliquam sed sem velit, id sagittis mauris. Donec sed ipsum nisi, id scelerisque felis. Cras lacus est, fermentum in ultricies eu, congue in elit. Nulla tincidunt posuere eros, eget suscipit tellus porta vel. Aliquam ut sollicitudin libero. Suspendisse potenti. Sed cursus dignissim nulla in elementum. Aliquam id quam justo, sit amet laoreet ligula. Etiam pellentesque tellus a nisi commodo eu sodales ante commodo. Vestibulum ultricies sapien arcu. Proin nunc mauris, ultrices id imperdiet ac, malesuada ac nunc. Nunc a mi quis nunc ultricies rhoncus. Mauris pellentesque eros eu augue congue ac tincidunt est gravida.</p>\n"
    . "<p>Integer lobortis facilisis magna, non tristique sem facilisis ut. Sed id nisi diam. Nulla viverra lectus ut purus tempus sagittis. Quisque dictum enim tempus ipsum mollis blandit. Cras in mi non nulla imperdiet fringilla at blandit urna. Donec vel dui quis sem congue ullamcorper nec a massa. Vivamus in dui nunc. Donec sit amet augue odio, at imperdiet lacus. Mauris sit amet magna justo. Maecenas ultrices orci ultrices sapien ornare eget consequat nisl tristique. Integer non mi ac eros vehicula pharetra. Curabitur risus augue, sollicitudin vitae pharetra interdum, sollicitudin sit amet magna. Nunc sit amet est lacus, vel sodales elit. Duis dolor lorem, convallis eu dignissim quis, vulputate at nibh.</p>\n"
    . "<p>Praesent gravida, sapien aliquet interdum elementum, magna mauris hendrerit eros, blandit posuere lectus neque sed massa. Cras ultricies rhoncus mi, vitae posuere ligula scelerisque sit amet. Cras porttitor congue odio, sit amet tristique magna euismod id. Cras enim dolor, scelerisque eget egestas vel, consectetur vel purus. Aenean et convallis felis. Mauris in arcu sollicitudin ipsum lobortis fringilla. Suspendisse felis mauris, convallis ac blandit interdum, imperdiet eget massa. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris scelerisque velit quis augue commodo tristique. Maecenas dictum dui congue enim tristique vel mattis neque luctus. Fusce neque dui, laoreet suscipit varius sed, mattis sit amet diam. Nullam elementum, ante non cursus imperdiet, eros dui placerat elit, sit amet elementum erat risus eget nunc.</p>\n"
    . "<p>Nam tellus nibh, vehicula a laoreet non, fermentum vel leo. Proin id augue tellus. Donec placerat pharetra interdum. Aliquam vestibulum viverra bibendum. Nullam auctor congue tortor. Sed sagittis, massa ac cursus malesuada, ipsum velit aliquam lectus, quis tincidunt tellus risus id justo. Suspendisse sodales adipiscing eros, ut pulvinar eros suscipit in. Fusce vel libero id urna blandit pharetra. Cras aliquam suscipit ultrices. Vivamus luctus tristique vestibulum. Nam placerat dolor ipsum. Nulla vitae tristique sapien. Nulla laoreet ante ut elit dictum ultricies. Fusce mi tortor, commodo sit amet tincidunt semper, pellentesque nec ante. Vestibulum nec dui at massa adipiscing pulvinar. Integer ultrices tristique odio, iaculis bibendum metus fringilla id. Ut ac elit ac enim convallis dignissim pharetra id purus. Nunc pulvinar blandit nulla, in ornare erat condimentum id. Sed sit amet placerat est. Curabitur pretium tellus in velit aliquet eu dictum arcu consectetur.</p>\n"
    . "<p>In hac habitasse platea dictumst. Integer lectus augue, varius nec rutrum non, fringilla eu neque. Curabitur a gravida velit. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Quisque vestibulum orci ac ligula interdum dapibus. Maecenas sollicitudin aliquet libero in sodales. In tempor orci vitae nisi imperdiet at varius sem dignissim. Aenean tortor libero, pellentesque eget hendrerit id, ullamcorper in justo. Sed euismod egestas est vitae convallis. Nunc tempus lacinia purus condimentum mattis. Sed id elementum est. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. In nec tempus eros. </p>\n"
    . $bookEnd;

$chapter4 = $content_start . "<h1>Chapter 4</h1>\n"
    . "<h2>Vivamus bibendum massa</h2>\n"
    . "<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum consequat nulla ac libero dapibus ornare. Nam vel lacus in eros vehicula volutpat at ac est. Cras eleifend odio vitae nibh viverra et blandit nibh iaculis. Aenean non pellentesque nisi. Pellentesque ultricies mauris vel odio ullamcorper auctor. Etiam nec erat non mi blandit sollicitudin sed sed metus. Cras vel sagittis augue. Vestibulum eros neque, convallis vel semper in, fringilla sit amet justo. Proin lobortis est ut augue cursus egestas. Maecenas cursus blandit tellus vitae varius. Integer euismod malesuada volutpat. Praesent sem odio, consequat tristique dictum tincidunt, ultricies sit amet sapien. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.</p>\n"
    . "<p>Pellentesque sit amet libero arcu, eu congue magna. Nam commodo, leo sed placerat sollicitudin, eros dolor vehicula ipsum, volutpat bibendum justo quam a turpis. Curabitur viverra ornare odio, aliquet accumsan nisi lobortis ut. Sed id mi non purus ultrices vestibulum eu ac justo. Nunc ultrices vulputate nulla eget porta. Nam iaculis arcu nec libero pretium eu ultricies enim porttitor. Praesent commodo, purus vel elementum egestas, sem nisl fermentum lorem, ac porttitor quam eros eget ante. Fusce diam eros, lacinia sit amet porttitor ac, lacinia quis mauris. Sed molestie, arcu id sodales malesuada, tortor diam faucibus diam, eget placerat tortor sapien ut est. Nam ut neque at nunc accumsan lacinia sed in neque. Nunc nec commodo eros. Suspendisse ut fringilla ipsum. Suspendisse eget neque nunc. Duis tincidunt consequat massa, vel vulputate ligula pretium a.</p>\n"
    . "<p>Etiam blandit malesuada purus, sollicitudin eleifend magna consectetur ac. Aenean erat mi, varius non lacinia non, eleifend eget urna. Curabitur sagittis vestibulum magna vel dapibus. Phasellus tempus cursus tellus sed aliquet. Vivamus interdum tincidunt varius. Cras ut mi odio. Donec molestie vehicula justo, at congue arcu convallis nec. Proin sit amet libero ante. Nunc nec ante vel libero faucibus commodo ac at lacus. Pellentesque faucibus tellus sit amet odio viverra condimentum. Morbi ut est urna, quis laoreet diam. Aliquam vulputate risus rhoncus massa lobortis porta. Nulla eleifend suscipit interdum. Praesent dictum lobortis urna in facilisis. Proin adipiscing pulvinar accumsan. Maecenas ac lacus vitae erat porta malesuada. Proin malesuada, quam nec cursus suscipit, metus ligula accumsan tortor, non ullamcorper dui dui eget odio. In et massa vel ligula condimentum mollis. Vestibulum ac consectetur risus. Etiam at odio velit, quis blandit ante.</p>\n"
    . "<p>Sed suscipit enim tortor. Curabitur ut dui dui, at tempus purus. Proin nulla velit, varius et ultricies at, pellentesque ac lectus. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Etiam commodo odio non leo commodo posuere. Integer sed mauris lacus. Aliquam nec diam velit, non volutpat metus. Vestibulum accumsan dignissim hendrerit. Nam elementum blandit pellentesque. Aliquam erat volutpat. Mauris ultrices metus ac enim pellentesque dignissim. Maecenas pellentesque interdum ligula, at imperdiet ipsum aliquam eu. Pellentesque pellentesque libero eget lacus placerat vel malesuada quam facilisis. Ut at nulla lacus. Nam et metus non velit accumsan pretium. Vestibulum eget iaculis nunc. In imperdiet lobortis tortor non eleifend.</p>\n"
    . "<p>Mauris vel gravida mauris. Aliquam eleifend cursus auctor. Nulla mattis nisl sit amet sem varius eu consequat felis volutpat. Mauris leo nibh, lobortis eget interdum id, rhoncus ut nulla. Donec pretium aliquet dictum. Quisque facilisis, urna id accumsan auctor, quam turpis eleifend sem, ac pulvinar dui tellus eget sapien. Proin ut lorem sit amet velit pretium eleifend eget et velit. Proin facilisis arcu at nisl iaculis in vehicula tellus tristique. Sed in quam augue, elementum scelerisque ligula. Proin nec viverra urna. Integer sed dui orci, pulvinar cursus ante. Cras leo felis, vehicula aliquet convallis quis, aliquet vel dui. Vivamus non urna vitae augue scelerisque sagittis ac vitae sem. Aliquam sagittis, felis nec vestibulum ultricies, nisi tellus varius sapien, in pellentesque orci libero sed tellus. Duis id ante ipsum, id tincidunt leo. Phasellus cursus, nisl sit amet sodales pretium, turpis enim fringilla nisi, quis adipiscing felis velit in orci. Praesent sit amet lacus libero. Maecenas ac lorem quis metus tempus commodo eu nec justo. Nunc vitae dolor at orci ullamcorper pretium. In hac habitasse platea dictumst.</p>\n"
    . "<p>Quisque rhoncus, nulla id viverra elementum, orci lorem lobortis enim, et fermentum erat massa et velit. Duis ullamcorper tempus laoreet. Quisque a massa vel magna viverra faucibus quis et erat. Praesent eu nulla a augue dignissim mattis. Nam ullamcorper pretium lobortis. Sed in quam vel leo dictum mattis. Quisque sapien est, consectetur et posuere sit amet, scelerisque at nulla. Proin sodales ultricies porttitor. Vestibulum sed dui lectus, sit amet hendrerit elit. Nunc nunc tortor, convallis sit amet vestibulum tempor, consequat sit amet arcu. Fusce congue scelerisque ante nec condimentum. Nulla facilisi. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Praesent ligula ante, aliquet nec ornare in, commodo ut metus. Cras varius tempor justo at dictum. Cras tincidunt posuere consectetur. Pellentesque fringilla, augue ac aliquet blandit, ante nunc accumsan eros, lacinia vehicula eros magna non est. Donec neque sapien, eleifend id mollis facilisis, fermentum eget metus. Nunc a ante lorem, vitae lacinia augue. Maecenas cursus aliquam dui.</p>\n"
    . "<p>Donec mollis nisi nec enim mattis vitae accumsan enim elementum. In rhoncus blandit odio, in facilisis nisl placerat in. Aenean vestibulum felis id dolor imperdiet tristique. Etiam ac urna eu nunc vestibulum euismod. Nulla mi magna, viverra at scelerisque non, rhoncus tempus libero. Sed facilisis, ante ullamcorper cursus placerat, orci turpis sagittis lectus, at placerat urna lorem elementum tortor. Vivamus felis mi, dictum et accumsan ut, lobortis vehicula libero. Suspendisse tellus arcu, malesuada vitae feugiat ut, vulputate sit amet sem. Pellentesque molestie, neque non rhoncus blandit, sapien dui pretium erat, eu tincidunt augue quam convallis nulla. Integer at adipiscing metus. Vestibulum felis urna, interdum eu egestas quis, iaculis a lacus. Aliquam nec urna nisl, id dignissim enim. Sed commodo vulputate turpis, ac dapibus nulla tincidunt et. Nam id nisl libero, in feugiat velit. Pellentesque lobortis adipiscing nisl sit amet rhoncus. Curabitur vulputate, ipsum a viverra ultrices, quam augue vulputate odio, pharetra placerat lorem tortor ac risus. Nunc egestas nisi vel orci venenatis iaculis. Phasellus facilisis risus et velit ultrices pulvinar. Donec convallis leo vel nisl lacinia luctus. Praesent non justo vitae eros malesuada auctor.</p>\n"
    . "<p>Etiam dignissim, augue eu malesuada faucibus, quam risus rhoncus libero, consectetur molestie nisl lacus eu nisl. Maecenas a nisi mauris. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Aenean nulla urna, lobortis lacinia semper at, laoreet vitae arcu. Pellentesque pretium ultrices velit, nec interdum risus aliquet a. Donec nec nunc turpis, non sagittis magna. Donec eget dolor purus, eget sodales diam. Aliquam elementum dui eget augue commodo sit amet fermentum sem venenatis. Quisque tristique ligula sit amet nulla condimentum commodo. Donec placerat quam eget justo lacinia vitae malesuada mauris sodales. Vestibulum bibendum sem sit amet ante sagittis dignissim. Nullam placerat, enim ut suscipit ultricies, lacus mi hendrerit lorem, sit amet vestibulum libero elit ut lorem. Integer laoreet commodo aliquet. Suspendisse eget velit enim. Nam tellus tortor, hendrerit eget pulvinar id, iaculis nec leo. Donec mattis semper libero vitae laoreet. Phasellus vitae velit ut neque suscipit lobortis non at justo. Curabitur viverra nisl eu odio varius vitae pellentesque erat luctus. Phasellus porta blandit pulvinar.</p>\n"
    . "<p>Donec rhoncus nunc sed ligula posuere a viverra nunc iaculis. Sed non dignissim nulla. Phasellus a nisl nec metus bibendum vulputate eu et ligula. Quisque dignissim quam id erat elementum cursus. Sed quis nisi nec lacus ullamcorper commodo. Nulla in lacinia odio. Vivamus metus turpis, tristique sed rutrum malesuada, pretium sed felis. Aliquam erat volutpat. Vestibulum eu elit porta tellus placerat consequat. Morbi sem nunc, vulputate sed scelerisque ut, feugiat et leo. Nam pellentesque metus eget ipsum feugiat euismod. Mauris nec enim sit amet turpis rutrum gravida. Cras velit nisl, tincidunt at mattis euismod, aliquet et urna. Sed pellentesque, magna at tristique pellentesque, metus est malesuada dolor, id lobortis eros justo in quam. In in lectus et arcu volutpat lacinia at ac nisl.</p>\n"
    . "<h1>Chapter 4B<br />test inlined chapter</h1>\n"
    . "<h2 id=\"sub01\">Vivamus bibendum massa</h2>\n"
    . "<p>Proin condimentum nisl tristique sapien luctus id ultrices magna molestie. Nulla nibh ligula, suscipit eget pharetra at, commodo et ligula. Quisque odio mi, aliquam a pulvinar id, condimentum vel dolor. In ut nisi eget orci facilisis pretium. Integer vel convallis nisi. Integer scelerisque luctus facilisis. Sed erat ante, adipiscing vel pretium eget, auctor quis eros. Donec tincidunt tempus porttitor. Phasellus in augue at nunc facilisis lacinia. Sed iaculis tristique diam at bibendum. Fusce lorem nibh, mollis semper pulvinar at, sollicitudin id velit. Donec aliquet, elit vel tempus sagittis, tortor nisl posuere turpis, in hendrerit mi justo id metus.</p>\n"
    . "<p>Nulla facilisi. Integer non libero neque. Cras consequat risus sed quam placerat elementum. Ut placerat, massa at sagittis fermentum, libero risus tempus urna, ac commodo lorem massa sed metus. Sed at tellus nulla, id imperdiet arcu. Proin vehicula urna arcu, sit amet eleifend dui. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Maecenas fringilla risus eu nunc lacinia vel varius odio venenatis. Sed ultrices magna purus. Ut eu risus molestie mi posuere fringilla in sit amet libero. Nulla facilisi. Sed non nulla nec mi adipiscing molestie. Aenean consectetur nibh sit amet tortor ultrices in ultrices ante tempus. Phasellus non arcu purus. In libero tellus, pharetra sed lobortis eu, tristique non neque.</p>\n"
    . "<p>Vivamus at odio id nisl egestas blandit. Sed sit amet ante urna, eget euismod justo. Cras adipiscing, purus vitae volutpat mollis, lectus massa tempor ligula, ut imperdiet erat lacus sit amet risus. Fusce erat tellus, adipiscing vitae fringilla at, pharetra eget ante. Sed sollicitudin dolor velit. Morbi suscipit turpis ac dui tincidunt nec tincidunt eros sagittis. Nulla et mauris vitae sapien commodo fermentum. Vivamus eget ante orci, id bibendum ipsum. Morbi ut neque tristique lorem pharetra rhoncus. Aliquam nisi odio, sollicitudin vel ornare quis, vestibulum sit amet magna. Cras ac augue quis mauris posuere convallis. Sed et nisi felis, in hendrerit tortor.</p>\n"
    . "<p>Nulla viverra diam non quam fringilla sodales et quis purus. Quisque mi velit, bibendum eget sagittis eu, interdum ut est. Maecenas convallis elit non turpis lobortis iaculis. Vestibulum erat justo, tincidunt vel pellentesque sit amet, placerat quis erat. Aliquam et neque ac lorem scelerisque imperdiet porta non leo. Mauris lacinia sagittis erat, quis lacinia est commodo non. Suspendisse sed eros libero. Sed a velit lorem, consectetur facilisis nisi. Aliquam risus risus, lacinia sed rutrum ut, faucibus ut nisl. Fusce volutpat euismod purus non malesuada. Sed urna orci, ultricies a placerat vel, mattis id ante.</p>\n"
    . "<p>Donec vitae ultrices tortor. Fusce luctus sollicitudin orci, nec tempus turpis tincidunt a. Nullam eu quam et magna aliquam viverra sed a tortor. Sed sem erat, tristique nec pellentesque quis, porta eu mi. Proin id ornare massa. Sed tincidunt, risus vitae fringilla porta, felis velit imperdiet justo, vel mattis lorem metus non metus. Sed condimentum leo non urna faucibus viverra aliquet est lobortis. Vivamus imperdiet velit quis odio fermentum eu luctus metus facilisis. Donec arcu tellus, commodo et semper in, aliquam vel lectus. Mauris tempus sagittis tortor eu blandit. Duis adipiscing accumsan commodo. Proin a arcu elit.</p>\n"
    . "<p>Duis ut aliquam mauris. Vestibulum lobortis porta dolor at fermentum. Proin at elit lacus, quis accumsan turpis. Aenean molestie lobortis sollicitudin. Praesent velit est, molestie sed cursus id, consectetur non turpis. Quisque a purus dui, nec faucibus libero. Cras porta molestie elementum. Phasellus sit amet facilisis eros. Ut et mi malesuada mi cursus vulputate vitae et lectus. Sed ac massa quis nisi egestas convallis. Duis interdum aliquam dui, nec laoreet turpis auctor a. Mauris consectetur eros eu elit tempus tincidunt. Suspendisse tellus elit, viverra eu aliquam nec, auctor volutpat quam.</p>\n"
    . "<p>Morbi pretium lectus laoreet sapien tincidunt ac volutpat erat ullamcorper. Vivamus dolor neque, blandit non ultrices vitae, mollis venenatis nisi. Fusce at mollis ante. Sed id libero id purus eleifend rutrum. Fusce eget lacus eget libero euismod elementum. Phasellus ac eros non mi luctus pulvinar vestibulum vitae nibh. Proin elementum ultricies mauris, non hendrerit massa egestas quis. Maecenas consectetur consequat quam, vitae tempor leo aliquam sed. Proin iaculis fringilla ante id laoreet. In facilisis vestibulum mollis. Etiam ut arcu mi. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nulla at fermentum nisl. Proin odio justo, condimentum euismod mollis et, malesuada ac tortor. Aliquam ac elit euismod nibh pulvinar sodales id vitae nibh. Curabitur ut libero metus, eu bibendum turpis.</p>\n"
    . "<p>Nam faucibus nibh non nulla aliquet id aliquet tortor tincidunt. Fusce at nisi ac mauris pulvinar vehicula at sed velit. Pellentesque vitae eros nec justo semper egestas ut id nisl. Quisque et est lectus. Cras eget nibh et odio pretium venenatis non nec tellus. Aliquam placerat odio non diam facilisis at sollicitudin turpis tempus. Etiam vitae magna dui, nec dignissim odio. Donec dui tellus, adipiscing vel dictum in, vehicula ut diam. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse est elit, porta non lobortis rutrum, placerat non urna. Integer nisi tellus, imperdiet ac dapibus at, interdum ut enim. Mauris fringilla tempus risus at dapibus. Quisque enim nunc, posuere vel dapibus vel, posuere vel sapien. Suspendisse potenti. Nullam pulvinar nibh nisi, nec porttitor nisi. Donec iaculis euismod elit at porttitor. Mauris quis nunc ut risus semper auctor. Pellentesque pulvinar cursus augue mattis luctus. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.</p>\n"
    . "<p>Pellentesque pharetra tincidunt velit, ac laoreet ipsum dictum quis. Cras hendrerit neque eu tellus pellentesque condimentum. Suspendisse metus mi, dignissim eu faucibus vel, molestie quis tortor. Suspendisse vel orci non orci gravida ultrices eu in dui. Vivamus vitae dolor vitae mauris congue auctor. Nulla iaculis, est tempor sagittis condimentum, libero erat fermentum libero, id dapibus tortor sem sit amet sapien. Pellentesque id ipsum eu elit pharetra tristique non ac nibh. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus facilisis, lorem id elementum feugiat, orci arcu tincidunt diam, quis placerat sem lectus convallis nulla. Proin eget est quis libero molestie faucibus. Cras et est vitae lacus lacinia auctor. Mauris ligula justo, ullamcorper molestie fermentum vel, tincidunt at nunc. Sed ullamcorper fringilla lectus in pharetra. Sed libero erat, lobortis nec tempor ac, volutpat id orci. Phasellus orci elit, blandit a sollicitudin at, dignissim in mi. Ut facilisis gravida cursus. Duis risus lacus, pretium vitae egestas varius, interdum non ipsum.</p>\n"
    . $bookEnd;

$chapter5 = $content_start . "<h1>Chapter 5</h1>\n"
    . "<h2>Vivamus bibendum massa 5A</h2>\n"
    . "<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum consequat nulla ac libero dapibus ornare. Nam vel lacus in eros vehicula volutpat at ac est. Cras eleifend odio vitae nibh viverra et blandit nibh iaculis. Aenean non pellentesque nisi. Pellentesque ultricies mauris vel odio ullamcorper auctor. Etiam nec erat non mi blandit sollicitudin sed sed metus. Cras vel sagittis augue. Vestibulum eros neque, convallis vel semper in, fringilla sit amet justo. Proin lobortis est ut augue cursus egestas. Maecenas cursus blandit tellus vitae varius. Integer euismod malesuada volutpat. Praesent sem odio, consequat tristique dictum tincidunt, ultricies sit amet sapien. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.</p>\n"
    . "<p>Pellentesque sit amet libero arcu, eu congue magna. Nam commodo, leo sed placerat sollicitudin, eros dolor vehicula ipsum, volutpat bibendum justo quam a turpis. Curabitur viverra ornare odio, aliquet accumsan nisi lobortis ut. Sed id mi non purus ultrices vestibulum eu ac justo. Nunc ultrices vulputate nulla eget porta. Nam iaculis arcu nec libero pretium eu ultricies enim porttitor. Praesent commodo, purus vel elementum egestas, sem nisl fermentum lorem, ac porttitor quam eros eget ante. Fusce diam eros, lacinia sit amet porttitor ac, lacinia quis mauris. Sed molestie, arcu id sodales malesuada, tortor diam faucibus diam, eget placerat tortor sapien ut est. Nam ut neque at nunc accumsan lacinia sed in neque. Nunc nec commodo eros. Suspendisse ut fringilla ipsum. Suspendisse eget neque nunc. Duis tincidunt consequat massa, vel vulputate ligula pretium a.</p>\n"
    . "<p>Etiam blandit malesuada purus, sollicitudin eleifend magna consectetur ac. Aenean erat mi, varius non lacinia non, eleifend eget urna. Curabitur sagittis vestibulum magna vel dapibus. Phasellus tempus cursus tellus sed aliquet. Vivamus interdum tincidunt varius. Cras ut mi odio. Donec molestie vehicula justo, at congue arcu convallis nec. Proin sit amet libero ante. Nunc nec ante vel libero faucibus commodo ac at lacus. Pellentesque faucibus tellus sit amet odio viverra condimentum. Morbi ut est urna, quis laoreet diam. Aliquam vulputate risus rhoncus massa lobortis porta. Nulla eleifend suscipit interdum. Praesent dictum lobortis urna in facilisis. Proin adipiscing pulvinar accumsan. Maecenas ac lacus vitae erat porta malesuada. Proin malesuada, quam nec cursus suscipit, metus ligula accumsan tortor, non ullamcorper dui dui eget odio. In et massa vel ligula condimentum mollis. Vestibulum ac consectetur risus. Etiam at odio velit, quis blandit ante.</p>\n"
    . "<p>Sed suscipit enim tortor. Curabitur ut dui dui, at tempus purus. Proin nulla velit, varius et ultricies at, pellentesque ac lectus. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Etiam commodo odio non leo commodo posuere. Integer sed mauris lacus. Aliquam nec diam velit, non volutpat metus. Vestibulum accumsan dignissim hendrerit. Nam elementum blandit pellentesque. Aliquam erat volutpat. Mauris ultrices metus ac enim pellentesque dignissim. Maecenas pellentesque interdum ligula, at imperdiet ipsum aliquam eu. Pellentesque pellentesque libero eget lacus placerat vel malesuada quam facilisis. Ut at nulla lacus. Nam et metus non velit accumsan pretium. Vestibulum eget iaculis nunc. In imperdiet lobortis tortor non eleifend.</p>\n"
    . "<p>Mauris vel gravida mauris. Aliquam eleifend cursus auctor. Nulla mattis nisl sit amet sem varius eu consequat felis volutpat. Mauris leo nibh, lobortis eget interdum id, rhoncus ut nulla. Donec pretium aliquet dictum. Quisque facilisis, urna id accumsan auctor, quam turpis eleifend sem, ac pulvinar dui tellus eget sapien. Proin ut lorem sit amet velit pretium eleifend eget et velit. Proin facilisis arcu at nisl iaculis in vehicula tellus tristique. Sed in quam augue, elementum scelerisque ligula. Proin nec viverra urna. Integer sed dui orci, pulvinar cursus ante. Cras leo felis, vehicula aliquet convallis quis, aliquet vel dui. Vivamus non urna vitae augue scelerisque sagittis ac vitae sem. Aliquam sagittis, felis nec vestibulum ultricies, nisi tellus varius sapien, in pellentesque orci libero sed tellus. Duis id ante ipsum, id tincidunt leo. Phasellus cursus, nisl sit amet sodales pretium, turpis enim fringilla nisi, quis adipiscing felis velit in orci. Praesent sit amet lacus libero. Maecenas ac lorem quis metus tempus commodo eu nec justo. Nunc vitae dolor at orci ullamcorper pretium. In hac habitasse platea dictumst.</p>\n"
    . "<p>Quisque rhoncus, nulla id viverra elementum, orci lorem lobortis enim, et fermentum erat massa et velit. Duis ullamcorper tempus laoreet. Quisque a massa vel magna viverra faucibus quis et erat. Praesent eu nulla a augue dignissim mattis. Nam ullamcorper pretium lobortis. Sed in quam vel leo dictum mattis. Quisque sapien est, consectetur et posuere sit amet, scelerisque at nulla. Proin sodales ultricies porttitor. Vestibulum sed dui lectus, sit amet hendrerit elit. Nunc nunc tortor, convallis sit amet vestibulum tempor, consequat sit amet arcu. Fusce congue scelerisque ante nec condimentum. Nulla facilisi. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Praesent ligula ante, aliquet nec ornare in, commodo ut metus. Cras varius tempor justo at dictum. Cras tincidunt posuere consectetur. Pellentesque fringilla, augue ac aliquet blandit, ante nunc accumsan eros, lacinia vehicula eros magna non est. Donec neque sapien, eleifend id mollis facilisis, fermentum eget metus. Nunc a ante lorem, vitae lacinia augue. Maecenas cursus aliquam dui.</p>\n"
    . "<p>Donec mollis nisi nec enim mattis vitae accumsan enim elementum. In rhoncus blandit odio, in facilisis nisl placerat in. Aenean vestibulum felis id dolor imperdiet tristique. Etiam ac urna eu nunc vestibulum euismod. Nulla mi magna, viverra at scelerisque non, rhoncus tempus libero. Sed facilisis, ante ullamcorper cursus placerat, orci turpis sagittis lectus, at placerat urna lorem elementum tortor. Vivamus felis mi, dictum et accumsan ut, lobortis vehicula libero. Suspendisse tellus arcu, malesuada vitae feugiat ut, vulputate sit amet sem. Pellentesque molestie, neque non rhoncus blandit, sapien dui pretium erat, eu tincidunt augue quam convallis nulla. Integer at adipiscing metus. Vestibulum felis urna, interdum eu egestas quis, iaculis a lacus. Aliquam nec urna nisl, id dignissim enim. Sed commodo vulputate turpis, ac dapibus nulla tincidunt et. Nam id nisl libero, in feugiat velit. Pellentesque lobortis adipiscing nisl sit amet rhoncus. Curabitur vulputate, ipsum a viverra ultrices, quam augue vulputate odio, pharetra placerat lorem tortor ac risus. Nunc egestas nisi vel orci venenatis iaculis. Phasellus facilisis risus et velit ultrices pulvinar. Donec convallis leo vel nisl lacinia luctus. Praesent non justo vitae eros malesuada auctor.</p>\n"
    . "<p>Etiam dignissim, augue eu malesuada faucibus, quam risus rhoncus libero, consectetur molestie nisl lacus eu nisl. Maecenas a nisi mauris. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Aenean nulla urna, lobortis lacinia semper at, laoreet vitae arcu. Pellentesque pretium ultrices velit, nec interdum risus aliquet a. Donec nec nunc turpis, non sagittis magna. Donec eget dolor purus, eget sodales diam. Aliquam elementum dui eget augue commodo sit amet fermentum sem venenatis. Quisque tristique ligula sit amet nulla condimentum commodo. Donec placerat quam eget justo lacinia vitae malesuada mauris sodales. Vestibulum bibendum sem sit amet ante sagittis dignissim. Nullam placerat, enim ut suscipit ultricies, lacus mi hendrerit lorem, sit amet vestibulum libero elit ut lorem. Integer laoreet commodo aliquet. Suspendisse eget velit enim. Nam tellus tortor, hendrerit eget pulvinar id, iaculis nec leo. Donec mattis semper libero vitae laoreet. Phasellus vitae velit ut neque suscipit lobortis non at justo. Curabitur viverra nisl eu odio varius vitae pellentesque erat luctus. Phasellus porta blandit pulvinar.</p>\n"
    . "<p>Donec rhoncus nunc sed ligula posuere a viverra nunc iaculis. Sed non dignissim nulla. Phasellus a nisl nec metus bibendum vulputate eu et ligula. Quisque dignissim quam id erat elementum cursus. Sed quis nisi nec lacus ullamcorper commodo. Nulla in lacinia odio. Vivamus metus turpis, tristique sed rutrum malesuada, pretium sed felis. Aliquam erat volutpat. Vestibulum eu elit porta tellus placerat consequat. Morbi sem nunc, vulputate sed scelerisque ut, feugiat et leo. Nam pellentesque metus eget ipsum feugiat euismod. Mauris nec enim sit amet turpis rutrum gravida. Cras velit nisl, tincidunt at mattis euismod, aliquet et urna. Sed pellentesque, magna at tristique pellentesque, metus est malesuada dolor, id lobortis eros justo in quam. In in lectus et arcu volutpat lacinia at ac nisl.</p>\n"
    . "<h1>Chapter 5B<br />test inlined chapter</h1>\n"
    . "<h2>Vivamus bibendum massa 5B</h2>\n"
    . "<p>Proin condimentum nisl tristique sapien luctus id ultrices magna molestie. Nulla nibh ligula, suscipit eget pharetra at, commodo et ligula. Quisque odio mi, aliquam a pulvinar id, condimentum vel dolor. In ut nisi eget orci facilisis pretium. Integer vel convallis nisi. Integer scelerisque luctus facilisis. Sed erat ante, adipiscing vel pretium eget, auctor quis eros. Donec tincidunt tempus porttitor. Phasellus in augue at nunc facilisis lacinia. Sed iaculis tristique diam at bibendum. Fusce lorem nibh, mollis semper pulvinar at, sollicitudin id velit. Donec aliquet, elit vel tempus sagittis, tortor nisl posuere turpis, in hendrerit mi justo id metus.</p>\n"
    . "<p>Nulla facilisi. Integer non libero neque. Cras consequat risus sed quam placerat elementum. Ut placerat, massa at sagittis fermentum, libero risus tempus urna, ac commodo lorem massa sed metus. Sed at tellus nulla, id imperdiet arcu. Proin vehicula urna arcu, sit amet eleifend dui. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Maecenas fringilla risus eu nunc lacinia vel varius odio venenatis. Sed ultrices magna purus. Ut eu risus molestie mi posuere fringilla in sit amet libero. Nulla facilisi. Sed non nulla nec mi adipiscing molestie. Aenean consectetur nibh sit amet tortor ultrices in ultrices ante tempus. Phasellus non arcu purus. In libero tellus, pharetra sed lobortis eu, tristique non neque.</p>\n"
    . "<p>Vivamus at odio id nisl egestas blandit. Sed sit amet ante urna, eget euismod justo. Cras adipiscing, purus vitae volutpat mollis, lectus massa tempor ligula, ut imperdiet erat lacus sit amet risus. Fusce erat tellus, adipiscing vitae fringilla at, pharetra eget ante. Sed sollicitudin dolor velit. Morbi suscipit turpis ac dui tincidunt nec tincidunt eros sagittis. Nulla et mauris vitae sapien commodo fermentum. Vivamus eget ante orci, id bibendum ipsum. Morbi ut neque tristique lorem pharetra rhoncus. Aliquam nisi odio, sollicitudin vel ornare quis, vestibulum sit amet magna. Cras ac augue quis mauris posuere convallis. Sed et nisi felis, in hendrerit tortor.</p>\n"
    . "<p>Nulla viverra diam non quam fringilla sodales et quis purus. Quisque mi velit, bibendum eget sagittis eu, interdum ut est. Maecenas convallis elit non turpis lobortis iaculis. Vestibulum erat justo, tincidunt vel pellentesque sit amet, placerat quis erat. Aliquam et neque ac lorem scelerisque imperdiet porta non leo. Mauris lacinia sagittis erat, quis lacinia est commodo non. Suspendisse sed eros libero. Sed a velit lorem, consectetur facilisis nisi. Aliquam risus risus, lacinia sed rutrum ut, faucibus ut nisl. Fusce volutpat euismod purus non malesuada. Sed urna orci, ultricies a placerat vel, mattis id ante.</p>\n"
    . "<p>Donec vitae ultrices tortor. Fusce luctus sollicitudin orci, nec tempus turpis tincidunt a. Nullam eu quam et magna aliquam viverra sed a tortor. Sed sem erat, tristique nec pellentesque quis, porta eu mi. Proin id ornare massa. Sed tincidunt, risus vitae fringilla porta, felis velit imperdiet justo, vel mattis lorem metus non metus. Sed condimentum leo non urna faucibus viverra aliquet est lobortis. Vivamus imperdiet velit quis odio fermentum eu luctus metus facilisis. Donec arcu tellus, commodo et semper in, aliquam vel lectus. Mauris tempus sagittis tortor eu blandit. Duis adipiscing accumsan commodo. Proin a arcu elit.</p>\n"
    . "<p>Duis ut aliquam mauris. Vestibulum lobortis porta dolor at fermentum. Proin at elit lacus, quis accumsan turpis. Aenean molestie lobortis sollicitudin. Praesent velit est, molestie sed cursus id, consectetur non turpis. Quisque a purus dui, nec faucibus libero. Cras porta molestie elementum. Phasellus sit amet facilisis eros. Ut et mi malesuada mi cursus vulputate vitae et lectus. Sed ac massa quis nisi egestas convallis. Duis interdum aliquam dui, nec laoreet turpis auctor a. Mauris consectetur eros eu elit tempus tincidunt. Suspendisse tellus elit, viverra eu aliquam nec, auctor volutpat quam.</p>\n"
    . "<p>Morbi pretium lectus laoreet sapien tincidunt ac volutpat erat ullamcorper. Vivamus dolor neque, blandit non ultrices vitae, mollis venenatis nisi. Fusce at mollis ante. Sed id libero id purus eleifend rutrum. Fusce eget lacus eget libero euismod elementum. Phasellus ac eros non mi luctus pulvinar vestibulum vitae nibh. Proin elementum ultricies mauris, non hendrerit massa egestas quis. Maecenas consectetur consequat quam, vitae tempor leo aliquam sed. Proin iaculis fringilla ante id laoreet. In facilisis vestibulum mollis. Etiam ut arcu mi. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nulla at fermentum nisl. Proin odio justo, condimentum euismod mollis et, malesuada ac tortor. Aliquam ac elit euismod nibh pulvinar sodales id vitae nibh. Curabitur ut libero metus, eu bibendum turpis.</p>\n"
    . "<p>Nam faucibus nibh non nulla aliquet id aliquet tortor tincidunt. Fusce at nisi ac mauris pulvinar vehicula at sed velit. Pellentesque vitae eros nec justo semper egestas ut id nisl. Quisque et est lectus. Cras eget nibh et odio pretium venenatis non nec tellus. Aliquam placerat odio non diam facilisis at sollicitudin turpis tempus. Etiam vitae magna dui, nec dignissim odio. Donec dui tellus, adipiscing vel dictum in, vehicula ut diam. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse est elit, porta non lobortis rutrum, placerat non urna. Integer nisi tellus, imperdiet ac dapibus at, interdum ut enim. Mauris fringilla tempus risus at dapibus. Quisque enim nunc, posuere vel dapibus vel, posuere vel sapien. Suspendisse potenti. Nullam pulvinar nibh nisi, nec porttitor nisi. Donec iaculis euismod elit at porttitor. Mauris quis nunc ut risus semper auctor. Pellentesque pulvinar cursus augue mattis luctus. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.</p>\n"
    . "<p>Pellentesque pharetra tincidunt velit, ac laoreet ipsum dictum quis. Cras hendrerit neque eu tellus pellentesque condimentum. Suspendisse metus mi, dignissim eu faucibus vel, molestie quis tortor. Suspendisse vel orci non orci gravida ultrices eu in dui. Vivamus vitae dolor vitae mauris congue auctor. Nulla iaculis, est tempor sagittis condimentum, libero erat fermentum libero, id dapibus tortor sem sit amet sapien. Pellentesque id ipsum eu elit pharetra tristique non ac nibh. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus facilisis, lorem id elementum feugiat, orci arcu tincidunt diam, quis placerat sem lectus convallis nulla. Proin eget est quis libero molestie faucibus. Cras et est vitae lacus lacinia auctor. Mauris ligula justo, ullamcorper molestie fermentum vel, tincidunt at nunc. Sed ullamcorper fringilla lectus in pharetra. Sed libero erat, lobortis nec tempor ac, volutpat id orci. Phasellus orci elit, blandit a sollicitudin at, dignissim in mi. Ut facilisis gravida cursus. Duis risus lacus, pretium vitae egestas varius, interdum non ipsum.</p>\n"
    . $bookEnd;

$log->logLine("Build Chapters");

$log->logLine("Add Chapter 1");
$book->addChapter("Chapter 1: Lorem ipsum", "Chapter001.html", $chapter1, true, EPub::EXTERNAL_REF_ADD);

$log->logLine("Add Chapter 2");
$book->addChapter("Chapter 2: Vivamus bibendum massa", "Chapter002.html", $content_start . "<h1>Chapter 2</h1>\n" . $chapter2);

// Chapter 2 contains an image reference "demo/DemoInlineImage.jpg" which we didn't get it to import
// automatically. So we will do that manually.
$log->logLine("Add referenced image from Chapter 2");
$book->addLargeFile("demo/DemoInlineImage.jpg", "DemoInlineImage", "demo/DemoInlineImage.jpg", "image/jpeg");

$log->logLine("Add Chapter 3");
$book->addChapter("Chapter 3: Vivamus bibendum massa again", "Chapter003.html", $chapter3);

// Auto split a chapter:
$log->logLine("Add Chapter 4");
$book->setSplitSize(15000); // For this test, we split at approx 15k. Default is 250000 had we left it alone.
$book->addChapter("Chapter 4: Vivamus bibendum massa split", "Chapter004.html", $chapter4, true);
$book->setSplitSize(250000);

$book->subLevel();
$book->addChapter("Chapter 4B: test inlined chapter", "Chapter004.html#sub01");
$book->backLevel();

// More advanced use of the splitter:
// Still using Chapter 4, but as you can see, "Chapter 4" also contains a header for Chapter 5.
$splitter = new EPubChapterSplitter();
$splitter->setSplitSize(15000); // For this test, we split at approx 15k. Default is 250000 had we left it alone.
$log->logLine("new EPubChapterSplitter()");

/* Using the # as regexp delimiter here, it makes writing the regexp easier.
 *  in this case we could have just searched for "Chapter ", or if we were using regexp '#^<h1>Chapter #i',
 *  using regular text (no regexp delimiters) will look for the text after the first tag. Meaning had we used
 *  "Chapter ", any paragraph or header starting with "Chapter " would have matched. The regexp equivalent of
 *  "Chapter " is '#^<.+?>Chapter #'
 * Essentially, the search string is looking for lines starting with...
 */
$log->logLine("Add Chapter 5");
$html2 = $splitter->splitChapter($chapter5, true, "Chapter ");/* '#^<.+?>Chapter \d*#i'); */
$log->logLine("Split chapter 5");

$idx = 0;
while (list($k, $v) = each($html2)) {
    $idx++;
    // Because we used a string search in the splitter, the returned hits are put in the key part of the array.
    // The entire HTML tag of the line matching the chapter search.

    // find the text inside the tags
    preg_match('#^<(\w+)\ *.*?>(.+)</\ *\1>$#i', $k, $cName);

    // because of the back reference, the tag name is in $cName[1], and the content is in $cName[2]
    // Change any line breaks in the chapter name to " - "
    $cName = preg_replace('#<br.+?>#i', " - ", $cName[2]);
    // Remove any other tags
    $cName = preg_replace('#<.+?>#i', " ", $cName);
    // clean the chapter name by removing any double spaces left behind to single space.
    $cName = preg_replace('#\s+#i', " ", $cName);

    $book->addChapter($cName, "Chapter005_" . $idx . ".html", $v, true);
}

// Notice that Chapter 1 have an image reference in paragraph 2?
// We can tell EPub to automatically load embedded images and other references:
// The parameters for addChapter are:
//  1: Chapter Name
//  2: File Name (in the book)
//  3: Chapter Data (HTML or array of HTML strings making up one chapter)
//  4: Auto Split Chapter (Default false)
//  5: External References, How to handle external references, default is EPub::EXTERNAL_REF_IGNORE
//  6: Base Dir, This is important, as this have to point to the root of the imported HTML, as seen from it's Document root.
//     if you are importing an HTML designed to live in "http://server/story/book.html", $baseDir must be "story"
//     It is used to resolve any links in the HTML.

// BEWARE!
// Using EPub::EXTERNAL_REF_ADD means EPub will try to download the images from the internet if they are external. This WILL slow down book generation a lot.
// $book->addChapter("Chapter 6: External Image test", "Chapter006.html", $chapter1, false, EPub::EXTERNAL_REF_ADD, $fileDir);
//$log->logLine("add chapter 6");

$log->logLine("Add Chapter 6");
$book->addChapter("Chapter 6: Local Image test", "Chapter006.html", $content_start . "<h1>Chapter 6</h1>\n" . $chapter2, false, EPub::EXTERNAL_REF_ADD, $fileDir);


// Chapter 7 tests level indentation
$chapter7Body = "<p>Vivamus bibendum massa ac magna congue gravida. Curabitur nulla ante, accumsan sit amet luctus a, fermentum ut diam. Maecenas porttitor faucibus mattis. Ut auctor aliquet ligula nec posuere. Nullam arcu turpis, dapibus sit amet tempor nec, cursus at augue. Aliquam sed sem velit, id sagittis mauris. Donec sed ipsum nisi, id scelerisque felis. Cras lacus est, fermentum in ultricies eu, congue in elit. Nulla tincidunt posuere eros, eget suscipit tellus porta vel. Aliquam ut sollicitudin libero. Suspendisse potenti. Sed cursus dignissim nulla in elementum. Aliquam id quam justo, sit amet laoreet ligula. Etiam pellentesque tellus a nisi commodo eu sodales ante commodo. Vestibulum ultricies sapien arcu. Proin nunc mauris, ultrices id imperdiet ac, malesuada ac nunc. Nunc a mi quis nunc ultricies rhoncus. Mauris pellentesque eros eu augue congue ac tincidunt est gravida.</p>\n" . $bookEnd;

$log->logLine("Add Chapter 7.0.0.0");
$book->addChapter("Chapter 7", "Chapter00700.html", $content_start . "<h2>Chapter 7.0.0</h2>\n" . $chapter7Body, false, EPub::EXTERNAL_REF_ADD, $fileDir);


$log->logLine("Add Chapter 7.1.0.0");
$book->subLevel();
$book->addChapter("Chapter 7.1", "Chapter00710.html", $content_start . "<h2>Chapter 7.1.0</h2>\n" . $chapter7Body, false, EPub::EXTERNAL_REF_ADD, $fileDir);


$log->logLine("Add Chapter 7.1.1.0");
$book->subLevel();
$book->addChapter("Chapter 7.1.1", "Chapter00711.html", $content_start . "<h2>Chapter 7.1.1</h2>\n" . $chapter7Body, false, EPub::EXTERNAL_REF_ADD, $fileDir);

$log->logLine("Add Chapter 7.1.1.1");
$book->subLevel();
$book->addChapter("Chapter 7.1.1.1", "Chapter007111.html", $content_start . "<h2>Chapter 7.1.1.1</h2>\n" . $chapter7Body, false, EPub::EXTERNAL_REF_ADD, $fileDir);
$book->addChapter("Chapter 7.1.1.2", "Chapter007112.html", $content_start . "<h2>Chapter 7.1.1.2</h2>\n" . $chapter7Body, false, EPub::EXTERNAL_REF_ADD, $fileDir);

$log->logLine("Add Chapter 7.1.2.0");
$book->backLevel();
$book->addChapter("Chapter 7.1.2", "Chapter007120.html", $content_start . "<h2>Chapter 7.1.2.0</h2>\n" . $chapter7Body, false, EPub::EXTERNAL_REF_ADD, $fileDir);

$log->logLine("Add Chapter 7.1.3.0");
$book->addChapter("Chapter 7.1.3", "Chapter007130.html", $content_start . "<h2>Chapter 7.1.3.0</h2>\n" . $chapter7Body, false, EPub::EXTERNAL_REF_ADD, $fileDir);

$log->logLine("Add Chapter 7.1.3.x");
$book->subLevel();
$book->addChapter("Chapter 7.1.3.1", "Chapter007131.html", $content_start . "<h2>Chapter 7.1.3.1</h2>\n" . $chapter7Body, false, EPub::EXTERNAL_REF_ADD, $fileDir);
$book->addChapter("Chapter 7.1.3.2", "Chapter007132.html", $content_start . "<h2>Chapter 7.1.3.2</h2>\n" . $chapter7Body, false, EPub::EXTERNAL_REF_ADD, $fileDir);
$book->addChapter("Chapter 7.1.3.3", "Chapter007133.html", $content_start . "<h2>Chapter 7.1.3.3</h2>\n" . $chapter7Body, false, EPub::EXTERNAL_REF_ADD, $fileDir);
$book->addChapter("Chapter 7.1.3.4", "Chapter007134.html", $content_start . "<h2>Chapter 7.1.3.4</h2>\n" . $chapter7Body, false, EPub::EXTERNAL_REF_ADD, $fileDir);


$log->logLine("Add Chapter 7.2.0.0");
// We went deep with Chapter 7.1.3.x, and sometimes the generating class knows exactly where it is anyway,
//  so instead of relying on multiple ->backLevel() calls, you can set the target level directly.
// This only works for going back in the hierarchy. ->setCurrentLevel(1) (or less) equals ->rootLevel();
$book->setCurrentLevel(2);
$book->addChapter("Chapter 7.2", "Chapter00720.html", $content_start . "<h2>Chapter 7.2.0</h2>\n" . $chapter7Body, false, EPub::EXTERNAL_REF_ADD, $fileDir);


$log->logLine("Add Chapter 7.3.0.0");
$book->addChapter("Chapter 7.3", "Chapter00730.html", $content_start . "<h2>Chapter 7.3.0</h2>\n" . $chapter7Body, false, EPub::EXTERNAL_REF_ADD, $fileDir);


$log->logLine("Add Chapter 7.3.1.0");
$book->subLevel();
$book->addChapter("Chapter 7.3.1", "Chapter00731.html", $content_start . "<h2>Chapter 7.3.1</h2>\n" . $chapter7Body, false, EPub::EXTERNAL_REF_ADD, $fileDir);

// If you have nested chapters, you can call ->rootLevel() to return your hierarchy to the root of the navMap.
$book->rootLevel();

// $log->logLine("Add TOC");
// $book->buildTOC();

$book->addChapter("Log", "Log.html", $content_start . $log->getLog() . "\n</pre>" . $bookEnd);

if ($book->isLogging) { // Only used in case we need to debug EPub.php.
    $epuplog = $book->getLog();
    $book->addChapter("ePubLog", "ePubLog.html", $content_start . $epuplog . "\n</pre>" . $bookEnd);
}

$book->finalize(); // Finalize the book, and build the archive.

// This is not really a part of the EPub class, but IF you have errors and want to know about them,
//  they would have been written to the output buffer, preventing the book from being sent.
//  This behaviour is desired as the book will then most likely be corrupt.
//  However you might want to dump the output to a log, this example section can do that:
/*
if (ob_get_contents() !== false && ob_get_contents() != '') {
    $f = fopen ('./log.txt', 'a') or die("Unable to open log.txt.");
    fwrite($f, "\r\n" . date("D, d M Y H:i:s T") . ": Error in " . __FILE__ . ": \r\n");
    fwrite($f, ob_get_contents() . "\r\n");
    fclose($f);
}
*/

// Save book as a file relative to your script (for local ePub generation)
// Notice that the extension .epub will be added by the script.
// The second parameter is a directory name which is '.' by default. Don't use trailing slash!
//$book->saveBook('epub-filename', '.');

// Send the book to the client. ".epub" will be appended if missing.
$zipData = $book->sendBook("ExampleBook1");

// After this point your script should call exit. If anything is written to the output,
// it'll be appended to the end of the book, causing the epub file to become corrupt.
