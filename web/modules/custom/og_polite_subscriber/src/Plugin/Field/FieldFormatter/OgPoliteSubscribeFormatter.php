<?php

namespace Drupal\og_polite_subscriber\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Url;
use Drupal\og\Og;
use Drupal\og\OgMembershipInterface;
use Drupal\og\Plugin\Field\FieldFormatter\GroupSubscribeFormatter;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;

/**
 * Plugin implementation of the 'PoliteSubscriberFormatter' formatter.
 *
 * @FieldFormatter(
 *   id = "og_polite_subscribe",
 *   label = @Translation("Polite OG Subscriber"),
 *   field_types = {
 *     "og_group"
 *   }
 * )
 */
class OgPoliteSubscribeFormatter extends GroupSubscribeFormatter implements ContainerFactoryPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = parent::viewElements($items, $langcode);

    // Cache by user.
    $elements['#cache']['contexts'] = ['user'];

    // Heavily copied from Drupal\og\Plugin\Field\FieldFormatter\GroupSubscribeFormatter.
    $group = $items->getEntity();
    $entity_type_id = $group->getEntityTypeId();
    $user = $this->entityTypeManager->load(($this->currentUser->id()));

    if (!Og::isMember($group, $user, [OgMembershipInterface::STATE_ACTIVE, OgMembershipInterface::STATE_PENDING])) {
      // If the user is authenticated, set up the subscribe link.
      if ($user->isAuthenticated()) {
        $parameters = [
          'entity_type_id' => $group->getEntityTypeId(),
          'group' => $group->id(),
        ];

        $url = Url::fromRoute('og.subscribe', $parameters);

        $polite_message = $this->t('Hello @name, click here if you would like to subscribe to this group called @group.', [
          '@name' => $user->getAccountName(),
          '@group' => $group->getTitle(),
        ]);

        /** @var \Drupal\Core\Access\AccessResult $access */
        if (($access = $this->ogAccess->userAccess($group, 'subscribe without approval', $user)) && $access->isAllowed()) {
          $link['title'] = $polite_message;
          $link['class'] = ['subscribe'];
          $link['url'] = $url;
        }
        if (($access = $this->ogAccess->userAccess($group, 'subscribe', $user)) && $access->isAllowed()) {
          $link['title'] = $polite_message;
          $link['class'] = ['subscribe', 'request'];
          $link['url'] = $url;
        }
      }
    }

    if (!empty($link['title'])) {
      $link += [
        'options' => [
          'attributes' => [
            'title' => $link['title'],
            'class' => ['group'] + $link['class'],
          ],
        ],
      ];

      $elements[0] = [
        '#type' => 'link',
        '#title' => $link['title'],
        '#url' => $link['url'],
      ];
    }

    return $elements;
  }

}
