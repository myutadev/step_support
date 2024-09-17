<?php

namespace Tests\Unit;

use App\Repositories\AdminRepository;
use App\Repositories\UserRepository;
use App\Services\UserService;
use Mockery;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertEquals;

class UserServiceTest extends TestCase
{
    protected $userRepositoryMock;
    protected $adminRepositoryMock;
    protected $userService;

    public function setUp(): void
    {
        parent::setUp();

        $this->userRepositoryMock = Mockery::mock(UserRepository::class);
        $this->adminRepositoryMock = Mockery::mock(AdminRepository::class);
        $this->userService = new UserService($this->userRepositoryMock, $this->adminRepositoryMock); // 一旦sort機能だけテストするのでnullで
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
    /**
     * A basic unit test example.
     */
    public function test_sortUserAccountInfoObj_returns_asc_sort_by_beneficiary_number(): void
    {
        //userInfoarray
        $userInfoArray = [
            [
                "beneficiary_number" => "0000110569",
                "beneficiary_number_expiration" => null,
                "name" => "山本薫平",
                "email" => "user1@test.co.jp",
                "is_on_welfare" => "無",
                "admission_date" => "2022-08-29",
                "discharge_date" => null,
                "birthdate" => "1995-05-12",
                "disability_category_id" => "精神",
                "residence_id" => "いーまーる識名",
                "counselor_id" => "佐藤花子",
                "user_id" => 1

            ],
            [
                "beneficiary_number" => "0000029918",
                "beneficiary_number_expiration" => null,
                "name" => "山本陽平",
                "email" => "user2@test.co.jp",
                "is_on_welfare" => "無",
                "admission_date" => "2022-08-01",
                "discharge_date" => null,
                "birthdate" => "1984-11-02",
                "disability_category_id" => "身体",
                "residence_id" => "グループホームゆいまーる 那覇",
                "counselor_id" => "田中健太郎",
                "user_id" => 2
            ]
        ];

        $sortField = "beneficiary_number";
        $sortOrder = "asc";

        $expected =   $userInfoArray = [
            [
                "beneficiary_number" => "0000029918",
                "beneficiary_number_expiration" => null,
                "name" => "山本陽平",
                "email" => "user2@test.co.jp",
                "is_on_welfare" => "無",
                "admission_date" => "2022-08-01",
                "discharge_date" => null,
                "birthdate" => "1984-11-02",
                "disability_category_id" => "身体",
                "residence_id" => "グループホームゆいまーる 那覇",
                "counselor_id" => "田中健太郎",
                "user_id" => 2
            ],
            [
                "beneficiary_number" => "0000110569",
                "beneficiary_number_expiration" => null,
                "name" => "山本薫平",
                "email" => "user1@test.co.jp",
                "is_on_welfare" => "無",
                "admission_date" => "2022-08-29",
                "discharge_date" => null,
                "birthdate" => "1995-05-12",
                "disability_category_id" => "精神",
                "residence_id" => "いーまーる識名",
                "counselor_id" => "佐藤花子",
                "user_id" => 1

            ]
        ];

        $result = $this->userService->sortUserAccountInfoObj($userInfoArray, $sortField, $sortOrder);

        assertEquals($expected, $result);
    }
}
