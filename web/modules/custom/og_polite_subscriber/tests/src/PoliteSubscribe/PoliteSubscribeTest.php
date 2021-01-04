<?php

namespace Drupal\Tests\og_polite_subscriber\Formatter;

use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Test OGF subscribe message override using traits from Drupal Test Traits.
 */
class PoliteSubscribeTest extends ExistingSiteBase {

  /**
   * Tests visibility and correctness of og_polite_subscriber block.
   */
  public function testPoliteSubscribe() {
    // Creates a user. Will be automatically cleaned up at the end of the test.
    $author = $this->createUser();

    // Create a "Mountains" group. Will be automatically cleaned up at end of
    // test.
    $node = $this->createNode([
      'title' => 'Mountains',
      'type' => 'group',
      'uid' => $author->id(),
    ]);
    $this->assertEquals($author->id(), $node->getOwnerId());

    // We can login as a visitor and see the block.
    $visitor = $this->createUser();
    $this->drupalLogin($visitor);
    $this->drupalGet($node->toUrl());
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('Hello ' . $visitor->getUsername() . ', click here if you would like to subscribe to this group called Mountains');
    // Probably overkill since it comes from OG: test subscription link.
    $this->assertSession()->elementAttributeContains('css', '.field--name-og-group .field__item a', 'href', '/group/node/' . $node->id() . '/subscribe');
  }

}
