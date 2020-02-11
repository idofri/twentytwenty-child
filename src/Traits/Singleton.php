<?php

namespace TwentyTwenty\Traits;

trait Singleton {
	final public static function instance() {
		static $instance;

		return $instance[ static::class ] ?? ( $instance[ static::class ] = new static );
	}
}
