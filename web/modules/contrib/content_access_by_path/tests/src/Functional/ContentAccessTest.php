<?php

namespace Drupal\Tests\content_access_by_path\Functional;

use Drupal\node\Entity\NodeType;
use Drupal\taxonomy\Entity\Vocabulary;
use Drupal\taxonomy\VocabularyInterface;
use Drupal\Tests\BrowserTestBase;
use Drupal\Tests\taxonomy\Traits\TaxonomyTestTrait;
use Drupal\Tests\user\Traits\UserCreationTrait;
use Symfony\Component\HttpFoundation\Response;

class ContentAccessTest extends BrowserTestBase {

  use TaxonomyTestTrait;
  use UserCreationTrait;

  public $defaultTheme = 'stark';

  public $strictConfigSchema = FALSE;

  protected static $modules = [
    // Core.
    'node',
    'path',
    'taxonomy',

    // Contrib.
    'content_access_by_path',
  ];

  private VocabularyInterface $vocabulary;

  public function setUp(): void {
    parent::setUp();

    NodeType::create(['type' => 'article'])->save();

    $this->vocabulary = Vocabulary::load('content_access_by_path');
  }

  public function testUsersWithTheNewsSiteSectionTermCanEditNodesWithinTheNewsSection(): void {
    $siteSection = $this->createTerm($this->vocabulary, [
      'content_access_by_path' => ['/news'],
      'name' => 'News',
    ]);

    // $siteSectionB = $this->createTerm($vocabulary, ['name' => 'News (Business)']);
    // $this->createTerm($vocabulary, ['name' => 'News (Sports)']);
    // $this->createTerm($vocabulary, ['name' => 'News (Sports: Rugby)']);

    $user = $this->createUser(
      ['access administration pages', 'administer nodes', 'edit any article content'],
      NULL,
      FALSE,
      ['content_access_by_path' => $siteSection],
    );

    $this->createNode([
      'title' => 'A news article',
      'path' => '/news/something',
      'type' => 'article',
    ]);

    $this->drupalLogin($user);

    $this->drupalGet('node/1/edit');
    $this->assertSession()->statusCodeEquals(Response::HTTP_OK);
  }

  public function testUsersWithNoSiteSectionTermsCanEditNodesWithinTheNewsSection(): void {
    $siteSection = $this->createTerm($this->vocabulary, [
      'content_access_by_path' => ['/news'],
      'name' => 'News',
    ]);

    $user = $this->createUser(
      ['access administration pages', 'administer nodes', 'edit any article content'],
      NULL,
      FALSE,
      ['content_access_by_path' => []],
    );

    $this->createNode([
      'title' => 'A news article',
      'path' => '/news/something',
      'type' => 'article',
    ]);

    $this->drupalLogin($user);

    $this->drupalGet('node/1/edit');
    $this->assertSession()->statusCodeEquals(Response::HTTP_OK);
  }

  public function testUsersWithoutTheNewsSiteSectionTermCanNotEditNodesWithinTheNewsSection(): void {
    $siteSectionA = $this->createTerm($this->vocabulary, [
      'content_access_by_path' => ['/news'],
      'name' => 'News',
    ]);

    $siteSectionB = $this->createTerm($this->vocabulary, [
      'content_access_by_path' => ['/blog'],
      'name' => 'Banana',
    ]);

    $user = $this->createUser(
      ['access administration pages', 'administer nodes', 'edit any article content'],
      NULL,
      FALSE,
      ['content_access_by_path' => [$siteSectionB]],
    );

    $this->createNode([
      'title' => 'A news article',
      'path' => '/news/something',
      'type' => 'article',
    ]);

    $this->drupalLogin($user);

    $this->drupalGet('node/1/edit');
    $this->assertSession()->statusCodeEquals(Response::HTTP_FORBIDDEN);
  }

  public function testUsersCanEditNodesWithinASubSection(): void {
    $siteSectionA = $this->createTerm($this->vocabulary, [
      'content_access_by_path' => ['/news'],
      'name' => 'News',
    ]);

    $siteSectionB = $this->createTerm($this->vocabulary, [
      'content_access_by_path' => ['/blog'],
      'name' => 'Sports',
    ]);

    $user = $this->createUser(
      ['access administration pages', 'administer nodes', 'edit any article content'],
      NULL,
      FALSE,
      ['content_access_by_path' => [$siteSectionA, $siteSectionB]],
    );

    $this->createNode([
      'title' => 'A news article',
      'path' => '/news/something',
      'type' => 'article',
    ]);

    $this->createNode([
      'title' => 'A blog post',
      'path' => '/blog/something-else',
      'type' => 'article',
    ]);

    $this->drupalLogin($user);

    $this->drupalGet('node/1/edit');
    $this->assertSession()->statusCodeEquals(Response::HTTP_OK);

    $this->drupalGet('node/2/edit');
    $this->assertSession()->statusCodeEquals(Response::HTTP_OK);
  }

  public function testUsersCanNotEditNodesIfTheyAreAssignedTheParentSection(): void {
    $siteSectionA = $this->createTerm($this->vocabulary, [
      'content_access_by_path' => ['/news'],
      'name' => 'News',
    ]);

    $siteSectionB = $this->createTerm($this->vocabulary, [
      'content_access_by_path' => ['/news/sports'],
      'name' => 'Sports',
    ]);

    $user = $this->createUser(
      ['access administration pages', 'administer nodes', 'edit any article content'],
      NULL,
      FALSE,
      ['content_access_by_path' => [$siteSectionB]],
    );

    $this->createNode([
      'title' => 'A news article',
      'path' => '/news/something',
      'type' => 'article',
    ]);

    $this->drupalLogin($user);

    $this->drupalGet('node/1/edit');
    $this->assertSession()->statusCodeEquals(Response::HTTP_FORBIDDEN);
  }

  public function testUsersCanEditNodesIfTheyAreInMultipleSections(): void {
    $siteSectionA = $this->createTerm($this->vocabulary, [
      'content_access_by_path' => ['/news'],
      'name' => 'News',
    ]);

    $siteSectionB = $this->createTerm($this->vocabulary, [
      'content_access_by_path' => ['/news/sports'],
      'name' => 'Sports',
    ]);

    $user = $this->createUser(
      ['access administration pages', 'administer nodes', 'edit any article content'],
      NULL,
      FALSE,
      ['content_access_by_path' => [$siteSectionB]],
    );

    $this->createNode([
      'title' => 'A news article',
      'path' => '/news/something',
      'type' => 'article',
    ]);

    $this->drupalLogin($user);

    $this->drupalGet('node/1/edit');
    $this->assertSession()->statusCodeEquals(Response::HTTP_FORBIDDEN);
  }

}
