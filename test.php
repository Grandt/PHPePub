	<?php

	use com\grandt\EPub;
	include_once("EPub.php");

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

	$content_end = "</body>\n</html>\n";
	$blogurl = "http://test.com/";
	$cssData = "body {\n  margin-left: .5em;\n  margin-right: .5em;\n  text-align: justify;\n}\n\np {\n  font-family: serif;\n  font-size: 10pt;\n  text-align: justify;\n  text-indent: 1em;\n  margin-top: 0px;\n  margin-bottom: 1ex;\n}\n\nh1, h2 {\n  font-family: sans-serif;\n  font-style: italic;\n  text-align: center;\n  background-color: #6b879c;\n  color: white;\n  width: 100%;\n}\n\nh1 {\n    margin-bottom: 2px;\n}\n\nh2 {\n    margin-top: -2px;\n    margin-bottom: 2px;\n}\n";

	$book = new EPub();
	$book->setTitle("test");

	$authorname = "Ima Author";

	$book->setAuthor($authorname, $authorname);
	$book->setIdentifier($blogurl . "&amp;stamp=" . time(), EPub::IDENTIFIER_URI); 
	$book->setLanguage("en");

	$book->addCSSFile("styles.css", "css1", $cssData);
	$cover = $content_start . "<h1>" . "test" . "</h1>\n";
	if ($authorname) {
		$cover .= "<h2>By: $authorname</h2>\n";
	}

	$cover .= "<h2>From: <a href=\"$blogurl\">$blogurl</a></h2>";
	$cover .= $content_end;

	$book->addChapter("Notices", "Cover.html", $cover);
	$book->buildTOC();
	$book->addChapter(
	   "Chapter 1", 
		"Chapter1.html", 
		$content_start . "<h1>Chapter 1</h1>\n<p>Plenty of test content</p>\n" . $content_end
	);
	$book->addChapter(
	   "Chapter 2", 
		"Chapter2.html", 
		$content_start . "<h1>Chapter 2</h1>\n<p>Plenty of test content</p>\n" . $content_end
	);
	$book->addChapter(
	   "Chapter 3", 
		"Chapter3.html", 
		$content_start . "<h1>Chapter 3</h1>\n<p>Plenty of test content</p>\n" . $content_end
	);
	$book->addChapter(
	   "Epilogue", 
		"Epilogue.html", 
		$content_start . "<h1>Epilogue</h1>\n<p>Plenty of test content</p>\n" . $content_end
	);
	$book->finalize();
	$zipData = $book->sendBook("ExampleBook1_test");