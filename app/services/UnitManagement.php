<?php namespace eTrack\services;

use DB;
use eTrack\Repositories\CriteriaRepository;
use eTrack\Repositories\UnitRepository;

class UnitManagement {

    protected $unitRepository;
    protected $criteriaRepository;
    protected $db;

    public function __construct(UnitRepository $unitRepository,
                                CriteriaRepository $criteriaRepository, DB $db)
    {
        $this->unitRepository = $unitRepository;
        $this->criteriaRepository = $criteriaRepository;
        $this->db = $db;
    }

    public function deleteUnit($id)
    {
        return $this->db->transaction(function() use($id)
        {
            $this->criteriaRepository->deleteAllFromUnit($id);
            $this->unitRepository->delete($id);
        });
    }

} 