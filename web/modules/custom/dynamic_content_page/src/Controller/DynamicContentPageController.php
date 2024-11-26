<?php

namespace Drupal\dynamic_content_page\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\Core\Link;
use Drupal\Core\Url;

class DynamicContentPageController extends ControllerBase {

    public function latestArticles() {
        $query = \Drupal::entityQuery('node')
            ->condition('status', 1)
            ->condition('type', 'article')
            ->sort('created', 'DESC')
            ->range(0, 5)
            ->accessCheck(FALSE);

        $nids = $query->execute();
        $nodes = Node::loadMultiple($nids);

        $items = [];
        foreach ($nodes as $node) {
            $items[] = [
                '#markup' => Link::fromTextAndUrl(
                    $node->getTitle(),
                    Url::fromRoute('entity.node.canonical', ['node' => $node->id()])
                )->toString() . ' - ' . date('F j, Y', $node->getCreatedTime()),
            ];
        }

        $read_more = Link::fromTextAndUrl(
            $this->t('Read More'),
            Url::fromRoute('view.content.page_1')
        )->toString();

        return [
            '#theme' => 'item_list',
            '#items' => $items,
            '#prefix' => '<h2>' . $this->t('Latest Articles') . '</h2>',
            '#suffix' => '<p>' . $read_more . '</p>',
        ];
    }
}
