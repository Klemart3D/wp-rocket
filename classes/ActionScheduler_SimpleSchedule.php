<?php

/**
 * Class ActionScheduler_SimpleSchedule
 */
class ActionScheduler_SimpleSchedule implements ActionScheduler_Schedule {
	private $date = NULL;
	private $timestamp = 0;
	public function __construct( DateTime $date ) {
		$this->date = clone($date);
	}

	/**
	 * @param DateTime $after
	 *
	 * @return DateTime|null
	 */
	public function next( DateTime $after = NULL ) {
		$after = empty($after) ? ActionScheduler::get_datetime_object('@0') : $after;
		return ( $after > $this->date ) ? NULL : clone( $this->date );
	}

	/**
	 * For PHP 5.2 compat, since DateTime objects can't be serialized
	 * @return array
	 */
	public function __sleep() {
		$this->timestamp = $this->date->format('U');
		return array(
			'timestamp',
		);
	}

	public function __wakeup() {
		$this->date = ActionScheduler::get_datetime_object($this->timestamp);
	}
}
 