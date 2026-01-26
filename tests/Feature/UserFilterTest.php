<?php

namespace Tests\Feature;

use App\Models\UserFilter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UserFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_save(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $response = $this->putJson('api/v1/user/filters', [
            'is_default' => false,
            'name' => 'My Filter',
            'filters' => [
                [
                    'value' => 'sports',
                    'type' => [
                        'id' => 'category',
                        'label' => 'Category',
                        'inputType' => 'select',
                    ],
                ],
                [
                    'value' => [
                        'from' => '2023-01-01',
                        'to' => '2023-12-31',
                    ],
                    'type' => [
                        'id' => 'dateRange',
                        'label' => 'Source',
                    ],
                ],
            ],
        ]);

        $response->assertStatus(Response::HTTP_OK);
    }

    public static function invalidFiltersData(): iterable
    {
        yield 'missing value' => [[
            [
                'type' => [
                    'id' => 1,
                    'label' => 'Category',
                    'inputType' => 'select',
                ],
            ],
        ]];

        yield 'missing type' => [[
            [
                'value' => 'sports',
            ],
        ]];

        yield 'invalid type structure' => [[
            [
                'value' => 'sports',
                'type' => [
                    'id' => 'invalid',
                ],
            ],
        ]];
    }

    #[DataProvider('invalidFiltersData')]
    public function test_save_fails_with_invalid_data($filters): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $response = $this->putJson('api/v1/user/filters', [
            'is_default' => false,
            'name' => 'My Filter',
            'filters' => $filters,
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_get_user_filters(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $response = $this->getJson('api/v1/user/filters');

        $response->dump()->assertJsonStructure([
            '*' => [
                'id',
                'name',
                'settings',
                'is_default',
                'created_at',
            ]
        ]);

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_update_filter(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $filter = UserFilter::factory()->createOne([
            'is_default' => false,
            'is_subscribed' => false,
        ]);

        $response = $this->patchJson('api/v1/user/filters/' . $filter->id, [
            'is_default' => true,
            'is_subscribed' => false,
        ])->assertStatus(Response::HTTP_OK);

        $filter->refresh();
        $this->assertTrue($filter->is_default);
        $this->assertFalse($filter->is_subscribed);
    }
}
