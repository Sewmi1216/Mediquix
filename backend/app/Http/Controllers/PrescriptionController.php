<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use App\Models\PrescriptionImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Exception;
use Illuminate\Support\Facades\DB;

class PrescriptionController extends Controller
{
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            Log::info('Prescription request received', $request->all());
            Log::info($request->header('Authorization'));

            $allowedSlots = [
                '08:00-10:00',
                '10:00-12:00',
                '12:00-14:00',
                '14:00-16:00',
                '16:00-18:00',
                '18:00-20:00',
            ];

            $validator = Validator::make($request->all(), [
                'note'             => 'nullable|string',
                'delivery_address' => 'required|string|max:255',
                'delivery_date'    => 'required|date',
                'delivery_time'    => 'required|string|in:' . implode(',', $allowedSlots),
                'images'           => 'nullable|array|max:5',
                'images.*'         => 'image|mimes:jpeg,png,jpg|max:2048',
            ]);

            if ($validator->fails()) {
                Log::warning('Validation failed', $validator->errors()->toArray());
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Create the prescription
            $prescription = Prescription::create([
                'user_id'         => auth()->id(),
                'note'            => $request->note,
                'delivery_address'=> $request->delivery_address,
                'delivery_date'   => $request->delivery_date,
                'delivery_time'   => $request->delivery_time,
            ]);

            Log::info('Prescription created', ['prescription_id' => $prescription->id]);

            // Handle multiple images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('prescriptions', 'public');

                    PrescriptionImage::create([
                        'prescription_id' => $prescription->id,
                        'image_path'      => $path,
                    ]);

                    Log::info('Image stored', ['path' => $path]);
                }
            }
            DB::commit();

            return response()->json([
                'message' => 'Prescription uploaded successfully.',
                'data'    => $prescription->load('images'),
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating prescription', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error'   => 'Something went wrong',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getPrescriptions()
    {
        try {
            $prescriptions = Prescription::with(['images', 'user'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'message' => 'Prescriptions fetched successfully',
                'data'    => $prescriptions,
            ], 200);
        } catch (Exception $e) {
            Log::error('Error fetching prescriptions', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error'   => 'Something went wrong',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getPrescriptionImages($prescriptionId)
    {
        try {
            $images = PrescriptionImage::where('prescription_id', $prescriptionId)
                ->pluck('image_path');

            return response()->json($images);
        } catch (Exception $e) {
            Log::error('Error fetching prescription images', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error'   => 'Something went wrong',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getUser($prescriptionId)
    {
        try {
            $prescription = Prescription::where('id', $prescriptionId)->first();

            if (!$prescription) {
                return response()->json(['error' => 'Prescription not found'], 404);
            }

            return response()->json(['user_id' => $prescription->user_id]);

        } catch (Exception $e) {
            Log::error('Error fetching user ID from prescription', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error'   => 'Something went wrong',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
