<?php namespace eTrack\Models;

class Criteria extends \Eloquent {
	protected $fillable = [];

	protected $table = 'criteria';

	public function unit()
	{
		return $this->belongsTo('Unit');
	}

	public function learningOutcome()
	{
		return $this->belongsTo('LearningOutcome');
	}
}