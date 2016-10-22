<?php

use voku\helper\Bootup;
use voku\helper\HtmlDomParser;

/**
 * Class SimpleHtmlDomNodeTest
 */
class SimpleHtmlDomNodeTest extends PHPUnit_Framework_TestCase
{
  /**
   * @param $filename
   *
   * @return null|string
   */
  protected function loadFixture($filename)
  {
    $path = __DIR__ . '/fixtures/' . $filename;
    if (file_exists($path)) {
      return file_get_contents($path);
    }

    return null;
  }

  /**
   * @dataProvider findTests
   *
   * @param $html
   * @param $selector
   * @param $count
   */
  public function testFind($html, $selector, $count)
  {
    $document = new HtmlDomParser($html);
    $nodeList = $document->find('section');

    $elements = $nodeList->find($selector);

    self::assertInstanceOf('voku\helper\SimpleHtmlDomNode', $elements);
    self::assertSame($count, count($elements));

    foreach ($elements as $node) {
      self::assertInstanceOf('voku\helper\SimpleHtmlDom', $node);
    }
  }

  /**
   * @return array
   */
  public function findTests()
  {
    $html = $this->loadFixture('test_page.html');

    $tests = array(
        array($html, '.fake h2', 0),
        array($html, 'article', 16),
        array($html, '.radio', 3),
        array($html, 'input.radio', 3),
        array($html, 'ul li', 9),
        array($html, 'fieldset#forms__checkbox li, fieldset#forms__radio li', 6),
        array($html, 'input[id]', 23),
        array($html, 'input[id=in]', 1),
        array($html, '#in', 1),
        array($html, '*[id]', 51),
    );

    if (Bootup::is_php('5.4')) {
      $tests = array_merge_recursive($tests, array(
          array($html, 'text', 539),
      ));
    } else {
      $tests = array_merge_recursive($tests, array(
          array($html, 'text', 390),
      ));
    }

    return $tests;
  }

  public function testInnerHtml()
  {
    $html = '<div><p>foo</p><p>bar</p></div>';
    $document = new HtmlDomParser($html);
    $element = $document->find('p');

    self::assertSame('<p>foo</p><p>bar</p>', $element->innerHtml());
    self::assertSame('<p>foo</p><p>bar</p>', $element->innertext);
  }

  public function testText()
  {
    $html = '<div><p>foo</p><p>bar</p></div>';
    $document = new HtmlDomParser($html);
    $element = $document->find('p');

    self::assertSame('foobar', $element->text());
    self::assertSame('foobar', $element->plaintext);
  }
}
