<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Quotation;
use App\Models\Drug;
use Illuminate\Support\Facades\Log;
use App\Mail\QuotationCreatedMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Exception;

class QuotationController extends Controller
{
    // Store quotation
    public function store(Request $request)
    {
        Log::info('Starting quotation store process', ['request_data' => $request->all()]);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'prescription_id' => 'required',
            'total_amount' => 'required|numeric',
            'status' => 'required|string',
            'drugs' => 'required|array|min:1',
            'drugs.*.drug_name' => 'required|string',
            'drugs.*.quantity' => 'required|string',
            'drugs.*.amount' => 'required|numeric',
        ]);

        Log::info('Validation successful', ['validated_data' => $validated]);

        try {
            DB::beginTransaction();
            $quotation = Quotation::create([
                'user_id' => $validated['user_id'],
                'prescription_id' => $validated['prescription_id'],
                'total_amount' => $validated['total_amount'],
                'status' => $validated['status'],
            ]);

            Log::info('Quotation created', ['quotation_id' => $quotation->id]);

            foreach ($validated['drugs'] as $drug) {
                $drugCreated = Drug::create([
                    'quotation_id' => $quotation->id,
                    'drug_name' => $drug['drug_name'],
                    'quantity' => $drug['quantity'],
                    'amount' => $drug['amount'],
                ]);

                Log::info('Drug added to quotation', [
                    'quotation_id' => $quotation->id,
                    'drug_id' => $drugCreated->id,
                    'drug_name' => $drug['drug_name'],
                ]);
            }

            $quotation->load(['user', 'drugs']);

            // Send email to user
            $user = $quotation->user;
            if ($user && $user->email) {
                Mail::to($user->email)->send(new QuotationCreatedMail($quotation));
                Log::info('Quotation email sent to user', ['email' => $user->email]);
            }
            DB::commit();
            return response()->json(['message' => 'Quotation saved and email sent successfully.']);
            
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to store quotation', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to save quotation.'], 500);
        }
    }

    // Get all quotations
    public function getQuotations()
    {
        try {
            $quotations = Quotation::select('id', 'created_at', 'total_amount', 'status')->get();

            return response()->json([
                'message' => 'Quotations fetched successfully',
                'data' => $quotations,
            ], 200);
        } catch (Exception $e) {
            Log::error('Error fetching quotations', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Something went wrong',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // View one quotation
    public function viewQuotation($quotationId)
    {
        try {
            $quotation = Quotation::with('drugs')->findOrFail($quotationId);

            $data = [
                'id' => $quotation->id,
                'user_id' => $quotation->user_id,
                'prescription_id' => $quotation->prescription_id,
                'total_amount' => $quotation->total_amount,
                'status' => $quotation->status,
                'created_at' => $quotation->created_at,
                'drugs' => $quotation->drugs->map(function ($drug) {
                    return [
                        'id' => $drug->id,
                        'drug_name' => $drug->drug_name,
                        'quantity' => $drug->quantity,
                        'amount' => $drug->amount,
                    ];
                }),
            ];

            return response()->json(['data' => $data]);
        } catch (Exception $e) {
            Log::error('Error fetching quotation', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Something went wrong',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // Accept a quotation
    public function accept($id)
    {
        $quotation = Quotation::find($id);

        if (!$quotation) {
            return response()->json(['message' => 'Quotation not found'], 404);
        }

        $quotation->status = 'Accepted';
        $quotation->save();

        return response()->json(['message' => 'Quotation accepted successfully'], 200);
    }

    // Reject a quotation
    public function reject($id)
    {
        $quotation = Quotation::find($id);

        if (!$quotation) {
            return response()->json(['message' => 'Quotation not found'], 404);
        }

        $quotation->status = 'Rejected';
        $quotation->save();

        return response()->json(['message' => 'Quotation rejected successfully'], 200);
    }
}
