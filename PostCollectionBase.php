<?php

namespace wpscholar\WordPress;

/**
 * Class PostCollectionBase
 *
 * @package wpscholar\WordPress
 */
abstract class PostCollectionBase implements \Countable, \IteratorAggregate {

	const POST_TYPE = null;

	/**
	 * Contains WP_Query instance or pseudo WP_Query instance (stdClass object) if returned from cache
	 *
	 * @var \WP_Query|\stdClass
	 */
	public $query;

	/**
	 * Default query args
	 *
	 * @var array
	 */
	protected $_default_args = [
		'ignore_sticky_posts' => true,
	];

	/**
	 * @var \Traversable|\Countable
	 */
	protected $_iterator;

	/**
	 * Collection constructor.
	 *
	 * @param array|string|null $args WP_Query arguments
	 */
	public function __construct( $args = null ) {
		if ( ! is_null( $args ) ) {
			$this->fetch( $args );
		}
	}

	/**
	 * Fetch posts
	 *
	 * @param array|string $args
	 */
	public function fetch( $args = [] ) {

		$required_args = [
			'post_type' => static::POST_TYPE,
		];

		$query_args = array_merge( $this->_default_args, wp_parse_args( $args ), $required_args );

		$query = new PostsQuery();
		$query->fetch( $query_args );

		$this->_iterator = $query;
		$this->query     = $this->_iterator->query;

	}

	/**
	 * Check if this collection has posts
	 *
	 * @return bool
	 */
	public function havePosts() {
		return $this->count() > 0;
	}

	/**
	 * Count posts
	 *
	 * @return int
	 */
	public function count() {

		// If iterator isn't set, just do a fetch automatically.
		if ( ! isset( $this->_iterator ) ) {
			$this->fetch();
		}

		return count( $this->_iterator );
	}

	/**
	 * Get iterator for collection
	 *
	 * @return \Generator
	 */
	public function getIterator() {

		// If iterator isn't set, just do a fetch automatically.
		if ( ! isset( $this->_iterator ) ) {
			$this->fetch();
		}

		foreach ( $this->_iterator as $post ) {
			yield $this->_decorate( $post );
		}
	}

	/**
	 * Decorate post
	 *
	 * @param \WP_Post $post
	 *
	 * @return object
	 */
	abstract protected function _decorate( \WP_Post $post );

}
