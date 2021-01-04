<?php

namespace Drupal\Tests\og_polite_subscriber\Block;

use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * A model test case using traits from Drupal Test Traits.
 */
class PoliteBlockTest extends ExistingSiteBase {

  /**
   * Tests visibility and correctness of og_polite_subscriber block
   */
  public function testBlock() {
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
    $this->assertSession()->pageTextContains('click here if you would like to subscribe to this group called Mountains');
    $this->assertSession()->elementAttributeContains('css', '.og-polite-link', 'href', '/group/node/' . $node->id() . '/subscribe');
  }

}
