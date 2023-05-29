<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PatientController extends Controller
{
    public function index(): JsonResponse
    {
        $patients = Cache::get('patients');

        if (!$patients) {
            $patients = Patient::all();

            Cache::put('patients', $patients, 5);
        }

        $response = $patients->map(function ($patient) {
            return [
                'name' => $patient->first_name . ' ' . $patient->last_name,
                'birthdate' => $patient->birthdate->format('d.m.Y'),
                'age' => $patient->age . ' ' . $patient->age_type,
            ];
        });

        return response()->json($response);
    }

    public function create(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'birthdate' => 'required|date',
        ]);

        $patient = Patient::createPatient($validatedData['first_name'], $validatedData['last_name'], $validatedData['birthdate']);

        $patients = Cache::get('patients', []);
        $patients[] = $patient;
        Cache::put('patients', $patients, 5);

        return response()->json(['message' => 'Patient created successfully']);
    }
}
