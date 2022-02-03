<?php

namespace WP_Rocket\Engine\Optimization\RUCSS\Database\Queries;

use WP_Rocket\Dependencies\Database\Query;

/**
 * RUCSS UsedCSS Query.
 */
class UsedCSS extends Query {

	/**
	 * Name of the database table to query.
	 *
	 * @var   string
	 */
	protected $table_name = 'wpr_rucss_used_css';

	/**
	 * String used to alias the database table in MySQL statement.
	 *
	 * Keep this short, but descriptive. I.E. "tr" for term relationships.
	 *
	 * This is used to avoid collisions with JOINs.
	 *
	 * @var   string
	 */
	protected $table_alias = 'wpr_rucss';

	/**
	 * Name of class used to setup the database schema.
	 *
	 * @var   string
	 */
	protected $table_schema = '\\WP_Rocket\\Engine\\Optimization\\RUCSS\\Database\\Schemas\\UsedCSS';

	/** Item ******************************************************************/

	/**
	 * Name for a single item.
	 *
	 * Use underscores between words. I.E. "term_relationship"
	 *
	 * This is used to automatically generate action hooks.
	 *
	 * @var   string
	 */
	protected $item_name = 'used_css';

	/**
	 * Plural version for a group of items.
	 *
	 * Use underscores between words. I.E. "term_relationships"
	 *
	 * This is used to automatically generate action hooks.
	 *
	 * @var   string
	 */
	protected $item_name_plural = 'used_css';

	/**
	 * Name of class used to turn IDs into first-class objects.
	 *
	 * This is used when looping through return values to guarantee their shape.
	 *
	 * @var   mixed
	 */
	protected $item_shape = '\\WP_Rocket\\Engine\\Optimization\\RUCSS\\Database\\Row\\UsedCSS';

	public function get_pending_jobs( int $count = 10 ) {
		return $this->query(
			[
				'number'         => $count,
				'status'         => 'pending',
				'fields'         => [
					'id',
				],
				'job_id__not_in' => [
					'not_in' => '',
				],
				'orderby'        => 'modified',
				'order'          => 'asc',
			]
		);
	}

	public function get_rucss_data( $search = false ) {
		$query_arr = [
			'fields'  => [
				'id',
				'url',
				'is_mobile',
				'status',
				'modified',
			],
			'orderby' => 'modified',
			'order'   => 'asc',
		];
		if ( $search ) {
			$query_arr['search_columns'] = [ 'url' ];
			$query_arr['search']         = $search;
		}

		return $this->query( $query_arr );
	}

	public function increment_retries( $id, $retries ) {
		return $this->update_item(
			 $id,
			[
				'retries' => $retries + 1,
			 ]
			);
	}
}
