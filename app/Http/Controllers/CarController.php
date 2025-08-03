<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\User; // Required for Admin actions
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CarController extends Controller
{
    /**
     * Display a listing of the cars.
     */
    public function index()
    {
        if (Gate::allows('admin')) {
            $cars = Car::with('user')->get(); // Admin sees all cars
        } else {
            $cars = Car::where('user_id', Auth::id())->with('user')->get(); // User sees only their cars
        }
        return view('cars.index', compact('cars'));
    }

    /**
     * Show the form for creating a new car.
     */
    public function create()
    {
        return view('cars.create');
    }

    /**
     * Store a newly created car in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'brand' => 'required|string|max:255',
                'model' => 'required|string|max:255',
                'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
                'color' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
            ]);

            $car = Auth::user()->cars()->create($validated);

            return response()->json(['message' => 'Car added successfully!', 'car' => $car->load('user')], 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Display the specified car.
     */
    public function show(Car $car)
    {
        if (Gate::denies('admin') && Gate::denies('manage-own-car', $car)) {
            return response()->json(['message' => 'Unauthorized to view this car.'], 403);
        }
        return response()->json($car->load('user'));
    }

    /**
     * Show the form for editing the specified car.
     */
public function edit(Car $car)
{
    if (Gate::denies('admin') && Gate::denies('manage-own-car', $car)) {
        return response()->json(['message' => 'Unauthorized to edit this car.'], 403);
    }
    return response()->json($car->load('user'));
}

    /**
     * Update the specified car in storage.
     */
    public function update(Request $request, Car $car)
    {
        if (Gate::denies('admin') && Gate::denies('manage-own-car', $car)) {
            return response()->json(['message' => 'Unauthorized to update this car.'], 403);
        }

        try {
            $validated = $request->validate([
                'brand' => 'required|string|max:255',
                'model' => 'required|string|max:255',
                'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
                'color' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
            ]);

            $car->update($validated);

            return response()->json(['message' => 'Car updated successfully!', 'car' => $car->load('user')]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Remove the specified car from storage.
     */
    public function destroy(Car $car)
    {
        if (Gate::denies('admin') && Gate::denies('manage-own-car', $car)) {
            return response()->json(['message' => 'Unauthorized to delete this car.'], 403);
        }

        $car->delete();

        return response()->json(['message' => 'Car deleted successfully!']);
    }

    // --- Admin specific functions ---

    /**
     * Display a listing of all users for admin to manage.
     */
    public function manageUsers()
    {
        if (Gate::denies('admin')) {
            abort(403, 'Unauthorized action.');
        }
        $users = User::all();
        return view('admin.users', compact('users'));
    }

    /**
     * Authorize a user (e.g., change role).
     */
    public function authorizeUser(Request $request, User $user)
    {
        if (Gate::denies('admin')) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        try {       
            $validated = $request->validate([   
                'role' => 'required|in:user,admin',
            ]);

            $user->update(['role' => $validated['role']]);

            return response()->json(['message' => "User {$user->name} role updated to {$user->role}."]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Reset a user's password (admin only).
     */
    public function resetUserPassword(Request $request, User $user)
    {
        if (Gate::denies('admin')) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        // For security, you might want to send a password reset link instead of setting a new password directly.
        // Or generate a random password and send it to the user's email.
        // For demonstration, we'll set a simple default password.
        try {
            $validated = $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);
            $user->password = bcrypt($validated['password']);
            $user->save();
            return response()->json(['message' => "Password for user {$user->name} has been reset."]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Deactivate/cancel a user's login (admin only).
     */
    public function toggleUserStatus(User $user)
    {
        if (Gate::denies('admin')) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        // A simple way to "deactivate" a user is to set their role to something like 'inactive'
        // or add an 'is_active' column to the users table. For now, we'll just delete them
        // or set a special role for demonstration.
        // For proper deactivation, it's better to add an `is_active` column to the `users` table.
        // For this example, let's just demonstrate by updating a dummy status.
        // In a real application, consider an `is_active` boolean column or soft deletes for users.

        // Example: If you had an 'is_active' column:
        // $user->is_active = !$user->is_active;
        // $user->save();
        // $status = $user->is_active ? 'activated' : 'deactivated';
        // return response()->json(['message' => "User {$user->name} has been {$status}."]);

        // For this example, we'll simulate by toggling role or showing a message:
        if ($user->role === 'inactive') {
            $user->role = 'user'; // Reactivate
            $message = "User {$user->name} has been reactivated.";
        } else {
            $user->role = 'inactive'; // Deactivate (or another appropriate status)
            $message = "User {$user->name} has been deactivated.";
        }
        $user->save();

        return response()->json(['message' => $message]);
    }

    // --- Loan Calculator Function (can be static or in a separate controller) ---
    public function calculateLoan(Request $request)
    {
        try {
            $validated = $request->validate([
                'principal' => 'required|numeric|min:0',
                'interest_rate' => 'required|numeric|min:0',
                'loan_term_months' => 'required|integer|min:1',
            ]);

            $principal = $validated['principal'];
            $annual_interest_rate = $validated['interest_rate'];
            $loan_term_months = $validated['loan_term_months'];

            // Monthly interest rate
            $monthly_interest_rate = ($annual_interest_rate / 100) / 12;

            // Calculate monthly payment using the formula: M = P [ i(1 + i)^n ] / [ (1 + i)^n – 1]
            if ($monthly_interest_rate > 0) {
                $monthly_payment = $principal * ($monthly_interest_rate * pow(1 + $monthly_interest_rate, $loan_term_months)) / (pow(1 + $monthly_interest_rate, $loan_term_months) - 1);
            } else {
                $monthly_payment = $principal / $loan_term_months; // No interest
            }


            $amortization_schedule = [];
            $remaining_balance = $principal;

            for ($month = 1; $month <= $loan_term_months; $month++) {
                $interest_payment = $remaining_balance * $monthly_interest_rate;
                $principal_payment = $monthly_payment - $interest_payment;
                $remaining_balance -= $principal_payment;

                // Ensure remaining balance doesn't go negative due to floating point inaccuracies
                if ($remaining_balance < 0) {
                    $principal_payment += $remaining_balance; // Adjust last principal payment
                    $remaining_balance = 0;
                }

                $amortization_schedule[] = [
                    'month' => $month,
                    'monthly_payment' => round($monthly_payment, 2),
                    'principal_payment' => round($principal_payment, 2),
                    'interest_payment' => round($interest_payment, 2),
                    'remaining_balance' => round($remaining_balance, 2),
                ];
            }

            return response()->json([
                'monthly_payment' => round($monthly_payment, 2),
                'amortization_schedule' => $amortization_schedule,
            ]);

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
}