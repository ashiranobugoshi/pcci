<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class MemberController extends Controller
{
    public function index()
    {
        // Members should only be records explicitly approved/saved by admin.
        $baseQuery = User::query()->orderByDesc('id');
        if (Schema::hasColumn('users', 'is_member')) {
            $baseQuery->where('is_member', true);
        } else {
            $baseQuery->whereRaw('1 = 0');
        }

        $members = $baseQuery
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'induction_date' => ['required', 'date'],
        ]);

        $existing = User::where('email', strtolower($validated['email']))->first();
        if ($existing) {
            if (!$existing->is_member) {
                $existing->name = $validated['company_name'];
                $existing->is_member = true;
                try {
                    $existing->created_at = Carbon::parse($validated['induction_date'])->startOfDay();
                } catch (\Throwable $e) {
                    // Keep user as member even if induction date normalization fails.
                }
                $existing->save();

                return response()->json([
                    'message' => 'Member created successfully.',
                    'data' => [
                        'id' => $existing->id,
                        'name' => $existing->name,
                        'email' => $existing->email,
                        'induction_date' => $validated['induction_date'],
                    ],
                ], 201);
            }

            return response()->json([
                'message' => 'This company is already a member.',
                'data' => [
                    'id' => $existing->id,
                    'email' => $existing->email,
                ],
            ], 409);
        }

        $user = User::create([
            'name' => $validated['company_name'],
            'email' => strtolower($validated['email']),
            'password' => Hash::make(Str::random(32)),
            'is_member' => true,
        ]);

        try {
            $user->created_at = Carbon::parse($validated['induction_date'])->startOfDay();
            $user->save();
        } catch (\Throwable $e) {
            // Keep created member even if induction date normalization fails.
        }

        return response()->json([
            'message' => 'Member created successfully.',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'induction_date' => $validated['induction_date'],
            ],
        ], 201);
    }
}
