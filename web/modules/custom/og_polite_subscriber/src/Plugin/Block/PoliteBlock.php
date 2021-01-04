<?php

namespace Drupal\og_polite_subscriber\Plugin\Block;

use Drupal\node\NodeInterface;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\user\Entity\User;

/**
 * Provides a block to subscribe a user to an organic group.
 *
 * @Block(
 *   id = "og_polite_subscriber",
 *   admin_label = @Translation("Polite Group Subscriber"),
 *   category = @Translation("Organic Groups")
 * )
 */
class PoliteBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $logged_in = \Drupal::currentUser()->isAuthenticated();
    $group = \Drupal::routeMatch()->getParameter('node');

    if ($logged_in && $group instanceof NodeInterface) {
      $account = User::load(\Drupal::currentUser()->id());

      $build['content'] = [
        '#markup' => $this->t('Hello @name, <a class="og-polite-link" href="/group/node/@nid/subscribe">click here if you would like to subscribe to this group called @group</a>.', [
          '@name' => $account->getAccountName(),
          '@nid' => $group->id(),
          '@group' => $group->getTitle(),
        ]
        ),
      ];
    }

    return $build;
  }

  /**
   *
   */
  public function getCacheTags() {
    // Rebuild the block when visiting another group.
    if ($node = \Drupal::routeMatch()->getParameter('node')) {
      // If there is node add its cachetag.
      return Cache::mergeTags(parent::getCacheTags(), ['group:' . $node->id()]);
    }
    else {
      // Return default tags instead.
      return parent::getCacheTags();
    }
  }

  /**
   *
   */
  public function getCacheContexts() {
    return Cache::mergeContexts(parent::getCacheContexts(), ['route']);
  }

}
