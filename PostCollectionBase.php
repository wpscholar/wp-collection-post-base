<?php
/**
 * Abstract collection class for use with posts.
 *
 * @package wpscholar\WordPresss
 */

namespace wpscholar\WordPress;

/**
 * Class PostCollectionBase
 *
 * @package wpscholar\WordPress
 */
abstract class PostCollectionBase extends CollectionBase {

	/**
	 * Post type name
	 *
	 * @var string
	 */
	const POST_TYPE = null;

	/**
	 * Default query args
	 *
	 * @var array
	 */
	protected $default_args = [
		'ignore_sticky_posts' => true,
	];

	/**
	 * Internal storage of latest query.
	 *
	 * @var \WP_Query
	 */
	protected $query;

	/**
	 * Fetch items
	 *
	 * @param array|string $args Query arguments
	 */
	public function fetch( $args = [] ) {

		$args = wp_parse_args( $args );

		$items = [];

		$query_args = array_merge(
			$this->default_args,
			$args,
			array_merge(
				[
					'fields'    => 'ids',
					'post_type' => static::POST_TYPE,
				],
				$this->required_args
			)
		);

		$this->query = new \WP_Query( $query_args );

		if ( $this->query->have_posts() ) {
			$items = $this->query->posts;
		}

		$this->populate( $items );
	}

	/**
	 * Get the found objects
	 */
	public function objects() {
		return $this->collection()->map( 'get_post' );
	}

	/**
	 * Transform ID into a post object.
	 *
	 * @param int $id Post ID
	 *
	 * @return \WP_Post
	 */
	protected function transform( $id ) {
		return get_post( $id );
	}

}
