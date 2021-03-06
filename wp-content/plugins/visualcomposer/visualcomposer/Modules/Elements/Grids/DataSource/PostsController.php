<?php

namespace VisualComposer\Modules\Elements\Grids\DataSource;

if (!defined('ABSPATH')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit;
}

use VisualComposer\Framework\Container;
// use VisualComposer\Framework\Illuminate\Support\Module;
use VisualComposer\Helpers\PostType;
use VisualComposer\Helpers\Traits\EventsFilters;
use WP_Query;

/**
 * Class PostsController
 * @package VisualComposer\Modules\Elements\Grids\DataSource
 */
class PostsController extends Container /*implements Module*/
{
    use EventsFilters;

    /**
     * PostsController constructor.
     */
    public function __construct()
    {
        /** @see \VisualComposer\Modules\Elements\Grids\DataSource\PostsController::queryPosts */
        $this->addFilter('vcv:elements:grids:posts', 'queryPosts');

        /** @see \VisualComposer\Modules\Elements\Grids\DataSource\PostsController::addGlobalVariables */
        $this->addFilter('vcv:frontend:head:extraOutput', 'addGlobalVariables');
        $this->addFilter('vcv:backend:extraOutput', 'addGlobalVariables');
    }

    protected function addGlobalVariables($scripts, $payload)
    {
        /** @see visualcomposer/resources/views/elements/grids/sources/postvariables.php */
        $variables = [];
        $variables[] = sprintf('<script>%s</script>', vcview('elements/grids/sources/postvariables'));

        return array_merge($scripts, $variables);
    }

    /**
     * @param $posts
     * @param $payload
     * @param \VisualComposer\Helpers\PostType $postTypeHelper
     *
     * @return array
     */
    protected function queryPosts($posts, $payload, PostType $postTypeHelper)
    {
        global $post;

        if (isset($payload['atts']['source'], $payload['atts']['source']['tag'])
            && $payload['atts']['source']['tag'] === 'postsGridDataSourcePost'
        ) {
            // Value:
            $value = html_entity_decode($payload['atts']['source']['value']);

            if (strpos($payload['atts']['source']['value'], 'sticky_posts') !== false) {
                $paginationQuery = new WP_Query($value);
                $newPosts = [];
                while ($paginationQuery->have_posts()) {
                    $paginationQuery->the_post();
                    $newPosts[] = $post;
                }
                wp_reset_postdata();
                $posts = array_merge(
                    $posts,
                    $newPosts
                );
            } else {
                $posts = array_merge(
                    $posts,
                    $postTypeHelper->query($value)
                );
            }
        }

        return $posts;
    }
}
