<?php

class Unit extends Eloquent {
	protected $fillable = [];

	public function criteria()
	{
		return $this->hasMany('eTrack\Models\Criteria');
	}

	public function learningOutcomes()
	{
		return $this->hasMany('eTrack\Models\LearningOutcome');
	}
}