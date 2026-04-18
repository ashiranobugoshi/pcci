<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class MemberController extends Controller
{
    public function index()
    {
        $members = User::query()
            ->orderByDesc('id')
            ->get()
            ->map(function (User $user) {
                return [
                    'id' => $user->id,
                    'status' => 'active',
                    'membership_type_id' => 1,
                    'created_at' => optional($user->created_at)?->toIso8601String(),
                    'applicant' => [
                        'basic_profile' => [
                            'registered_business_name' => $user->name,
                            'email' => $user->email,
                            'telephone_no' => 'N/A',
                            'business_location' => [
                                'business_address' => 'N/A',
                                'city_municipality' => 'N/A',
                                'province' => 'N/A',
                                'region' => 'N/A',
                                'zip_code' => 'N/A',
                            ],
                        ],
                        'official_representative' => [
                            'first_name' => $user->name,
                            'mid_name' => '',
                            'surname' => '',
                            'contact_no' => 'N/A',
                        ],
                    ],
                ];
            })
            ->values();

        return response()->json(['data' => $members]);
    }
}
