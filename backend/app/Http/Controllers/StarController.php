<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\StarRecord;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StarController extends Controller
{
    /**
     * Add stars to a child
     */
    public function add(Request $request, Child $child): JsonResponse
    {
        // Get max stars from settings
        $maxStars = Setting::get('max_stars_per_add', 100);

        $validator = Validator::make($request->all(), [
            'amount' => "required|integer|min:1|max:{$maxStars}",
            'reason' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $amount = $request->input('amount');
        $reason = $request->input('reason');

        try {
            DB::transaction(function () use ($child, $amount, $reason) {
                // Create star record
                StarRecord::create([
                    'child_id' => $child->id,
                    'amount' => $amount,
                    'type' => 'add',
                    'reason' => $reason,
                ]);

                // Update child's star count
                $child->increment('star_count', $amount);
            });

            $child->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Stars added successfully',
                'data' => [
                    'star_count' => $child->star_count,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add stars',
            ], 500);
        }
    }

    /**
     * Subtract stars from a child
     */
    public function subtract(Request $request, Child $child): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $amount = $request->input('amount');
        $reason = $request->input('reason');

        // Check if child has enough stars
        if ($child->star_count < $amount) {
            return response()->json([
                'success' => false,
                'message' => 'Not enough stars',
            ], 400);
        }

        try {
            DB::transaction(function () use ($child, $amount, $reason) {
                // Create star record (negative amount)
                StarRecord::create([
                    'child_id' => $child->id,
                    'amount' => -$amount,
                    'type' => 'subtract',
                    'reason' => $reason,
                ]);

                // Update child's star count
                $child->decrement('star_count', $amount);
            });

            $child->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Stars subtracted successfully',
                'data' => [
                    'star_count' => $child->star_count,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to subtract stars',
            ], 500);
        }
    }

    /**
     * Get recent star records for all children
     */
    public function recent(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 10);

        $records = StarRecord::with(['child:id,name,gender,avatar', 'reward:id,name,image'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $records->map(fn($record) => [
                'id' => $record->id,
                'amount' => $record->amount,
                'type' => $record->type,
                'reason' => $record->reason,
                'child' => [
                    'id' => $record->child->id,
                    'name' => $record->child->name,
                    'gender' => $record->child->gender,
                    'avatar' => $record->child->avatar ? \Storage::url($record->child->avatar) : null,
                ],
                'reward' => $record->reward ? [
                    'id' => $record->reward->id,
                    'name' => $record->reward->name,
                    'image' => $record->reward->image ? \Storage::url($record->reward->image) : null,
                ] : null,
                'created_at' => $record->created_at->format('Y-m-d H:i'),
            ]),
        ]);
    }
}
