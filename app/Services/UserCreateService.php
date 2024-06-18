<?php

namespace App\Services;

use App\Models\Counselor;
use App\Models\DisabilityCategory;
use App\Models\Residence;

class UserCreateService
{
    protected $counselorRepository;
    protected $residenceRepository;
    protected $disabilityCategoryRepository;

    public function __construct(
        Counselor $counselorRepository,
        DisabilityCategory $disabilityCategoryRepository,
        Residence $residenceRepository
    ) {
        $this->counselorRepository = $counselorRepository;
        $this->residenceRepository = $residenceRepository;
        $this->disabilityCategoryRepository = $disabilityCategoryRepository;
    }

    /**
     * users/createページの障害区分、住居、相談員の選択肢用のデータを返すメソッド
     *
     *@return array 障害区分･住居･相談員情報が入った連想配列。
     */

    public function getUserResistrationData()
    {
        return [
            'disabilityCategories' => $this->disabilityCategoryRepository->get(),
            'residences' => $this->residenceRepository->get(),
            'counselors' => $this->counselorRepository->get()
        ];
    }
}
