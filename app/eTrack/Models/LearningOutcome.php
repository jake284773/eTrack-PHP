<?php namespace eTrack\Models;

class LearningOutcome extends \Eloquent {
	protected $fillable = [];

	public function unit()
	{
		return $this->belongsTo('eTrack\Models\Unit');
	}

	public function criteria()
	{
		return $this->hasMany('eTrack\Models\Criteria');
	}
}