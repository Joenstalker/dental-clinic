<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Audit;
use Illuminate\Support\Facades\Hash;
use App\Models\Member;
use App\Models\Services;
use App\Models\AppointmentSession;

class AssistantController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkAssistant');
    }

    public function index()
    {
        $currentDate = date('F j, Y');
        $pendingAccount = User::where('status', 'inactive')->count();
        $pendingAppointment = Member::where('status', 'pending')->count();

        return view ('assistant.dashboard', compact('currentDate', 'pendingAccount', 'pendingAppointment'));
    }

    public function appointmentRequest()
    {
        $currentDate = date('F j, Y');
    
        // Fetch only pending appointments that need approval
        $appointments = Member::with(['user', 'appointmentSession.user'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
    
        return view('assistant.appointment-request', compact('appointments', 'currentDate'));
    }
    
    public function approveAppointment($id)
    {
        $appointment = Member::findOrFail($id);
        
        // Check authorization using Policy
        $this->authorize('approve', $appointment);
        
        $appointment->update(['status' => 'approved']);
        return redirect()->back()->with('success', 'Appointment approved successfully.');
    }

    public function disapproveAppointment($id)
    {
        $appointment = Member::findOrFail($id);
        
        // Check authorization using Policy
        $this->authorize('disapprove', $appointment);
        
        $appointment->update(['status' => 'disapproved']);
        return redirect()->back()->with('success', 'Appointment disapproved successfully.');
    }

    // Fetch patient details for 'view'
    public function viewAppointmentDetails($id)
    {
        $appointment = Member::with(['user', 'appointmentSession.user'])->findOrFail($id);
        
        // Check authorization using Policy
        $this->authorize('view', $appointment);
        
        return response()->json($appointment);
    }

    public function pendingAccount()
    {
        // Get only patients with inactive status (pending accounts)
        $patients = User::where('userRole', 'patient')
            ->where('status', 'inactive')
            ->get();
        $currentDate = date('F j, Y');

        return view ('assistant.pending-account', compact('patients', 'currentDate'));
    }

    public function updatePatientStatus(Request $request, $id)
    {
        // Find the patient by ID
        $patient = User::findOrFail($id);
        
        // Check authorization
        $this->authorize('updateStatus', $patient);
    
        // Update the status from inactive to active (approve the account)
        if ($patient->status === 'inactive') {
            $patient->status = 'active';
            $patient->save();
            
            // Redirect back with success message
            return redirect()->back()->with('success', 'Patient account approved successfully. The account will no longer appear in pending accounts.');
        }
        
        // If already active, toggle to inactive (for flexibility)
        $patient->status = 'inactive';
        $patient->save();
        
        // Redirect back with success message
        return redirect()->back()->with('success', 'Patient status updated successfully');
    }

    public function settings()
    {
        $currentDate = date('F j, Y');
        $user = auth()->user();
        return view ('assistant.settings', compact('currentDate', 'user'));
    }

    public function logout(Request $request)
    {
        // Get the current user
        $user = Auth::user();

        // Check if user is authenticated
        if ($user) {
            // Update the latest audit log for this user
            $auditLog = Audit::where('user_id', $user->id)->latest()->first();
            
            if ($auditLog) {
                $auditLog->updated_at = now(); // Set the updated_at to the current time
                $auditLog->save(); // Save the changes
            }

            // Logout the user
            Auth::logout();

            // Redirect to the desired route after logout
            return redirect()->route('signin')->with('success', 'Successfully logged out.');
        }

        // If user is not logged in, redirect with an error message
        return redirect()->route('signin')->with('error', 'You are not logged in.');
    }

    public function assistantChangePassword(Request $request)
    {
        // Validate incoming request data
        $request->validate([
            'current_password' => 'required',
            'new_password' => [
                'required',
                'regex:/^(?=.*[a-zA-Z])(?=.*\d).{6,}$/',
            ],
            'confirm_password' => 'required|same:new_password',
        ]);
    
        $user = auth()->user();
    
        // Check if the current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'errors' => ['current_password' => ['Incorrect current password']]
            ], 422);
        }
    
        // Update the user's password
        $user->update(['password' => Hash::make($request->new_password)]);
    
        // Return a success response
        return response()->json(['message' => 'Password changed successfully!']);
    }

    public function editAssistantProfile(Request $request)
    {
        // Check the parameters
        $request->validate([
            'full_name' => 'required',
            'email' => 'required',
            'number' => 'required',
            'address' => 'required',
            'dob' => 'required',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Find the user by ID
        $user = User::findOrFail($request->input('id'));

        // Check authorization
        $this->authorize('update', $user);

        // Prepare update data
        $updateData = [
            'full_name' => $request->full_name,
            'email' => $request->email,
            'number' => $request->number,
            'address' => $request->address,
            'dob' => $request->dob,
        ];

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($user->profile_picture) {
                $oldPath = public_path('storage/profile_pictures/' . $user->profile_picture);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            // Store new profile picture
            $file = $request->file('profile_picture');
            $filename = time() . '_' . $user->id . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('storage/profile_pictures'), $filename);
            $updateData['profile_picture'] = $filename;
        }

        // Update in the database
        $user->update($updateData);

        // Return to the frontend
        return redirect()->back()->with('success', 'Successfully Updated');
    }

    public function userDelete()
    {
        $user = auth()->user();
        
        // Check authorization - users can only delete their own account
        $this->authorize('delete', $user);
        
        // Delete the user account
        $user->delete();

        return response()->json(['message' => 'Your account has been deleted successfully.']);
    }

    public function patient()
    {
        $currentDate = date('F j, Y');
        $patients = User::where('userRole', 'patient')->get();

        return view ('assistant.patient', compact('currentDate', 'patients'));
    }

    public function viewPatient($id)
    {
        // Fetch patient details
        $patient = User::findOrFail($id);
        
        // Check authorization using Policy
        $this->authorize('view', $patient);
    
        // Fetch all approved appointments for the patient
        $approvedAppointments = Member::where('user_id', $id)
                                    ->where('status', 'approved')
                                    ->with('appointmentSession')  // Load related appointment sessions
                                    ->orderBy('created_at', 'desc')
                                    ->get();
    
        if ($approvedAppointments->isNotEmpty()) {
            // Initialize HTML structure
            $html = '
                <div style="text-align: center;">
                    <h2>CUDAL-BLANCO DENTAL CLINIC</h2>
                    <h4>Appointment Completion Report</h4>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <p><strong>Patient Number:</strong> ' . $patient->id . '</p>
                    <p><strong>Patient Name:</strong> ' . $patient->full_name . '</p>
                </div>
            ';
    
            // Start the table structure
            $html .= '
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Appointment Session Title</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
            ';
    
            $totalPrice = 0;
    
            // Loop through each appointment session and display details
            foreach ($approvedAppointments as $appointment) {
                $appointmentSession = $appointment->appointmentSession;
    
                $html .= '
                    <tr>
                        <td>' . $appointmentSession->session_title . '</td>
                        <td>₱' . number_format($appointmentSession->price, 2) . '</td>
                    </tr>
                ';
    
                // Calculate total price
                $totalPrice += $appointmentSession->price;
            }
    
            // Close the table and add the total price at the bottom
            $html .= '
                    </tbody>
                    <tfoot>
                        <tr>
                            <td><strong>Total</strong></td>
                            <td><strong>₱' . number_format($totalPrice, 2) . '</strong></td>
                        </tr>
                    </tfoot>
                </table>
            ';
    
        } else {
            // If no approved appointments found
            $html = '<p>No approved appointments found for this patient.</p>';
        }
    
        return response()->json(['html' => $html]);
    }


    public function session()
    {
        // Get current date
        $currentDate = date('F j, Y');
    
        // query to fetch users with the role of 'dentist'
        $dentists = User::where('userRole', 'dentist')->get();

        // query to fetch services
        $services = Services::all();
    
        // Get all appointment sessions
        $appointmentSessions = AppointmentSession::all();

        // including the members and their related user data
        $sessions = AppointmentSession::with('members.user')
        ->get();
    
        // Pass data to the view
        return view('assistant.add-session', compact('currentDate', 'appointmentSessions', 'dentists', 'services', 'sessions'));
    }

    public function addSession(Request $request)
    {
        // Check authorization using Gate
        $this->authorize('create', AppointmentSession::class);
        
        // Validate the request data with custom error messages
        $request->validate([
            'user_id' => 'required', 
            'session_title' => 'required',  
            'schedule_date' => 'required',  
            'number_of_member' => 'required',
            'price' => 'required',
        ]);

        // Create the AppointmentSession and store the result
        $appointmentSession = AppointmentSession::create([
            'user_id' => $request->input('user_id'),  
            'session_title' => $request->input('session_title'),
            'schedule_date' => $request->input('schedule_date'),
            'number_of_member' => $request->input('number_of_member'),
            'price' => $request->input('price'),
        ]);

        // Check if appointment session creation was successful
        if (!$appointmentSession) {
            return redirect()->route('assistant.session')->with('error', 'Failed to create appointment session.');
        }

        // Redirect with success message
        return redirect()->route('assistant.session')->with('success', 'Session Registered!');
    }

    public function cancelSession(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'session_id' => 'required|exists:appointment_sessions,id',
        ]);

        // Find the appointment session by its ID
        $session = AppointmentSession::findOrFail($request->session_id);
        
        // Check authorization using Policy
        $this->authorize('cancel', $session);

        // Delete all members associated with the session
        $session->members()->delete();

        // Delete the session itself
        $session->delete();

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Session and associated event canceled successfully.');
    }

    public function service()
    {
        $currentDate = date('F j, Y');
        
        // Fetch all services
        $services = Services::orderBy('service', 'asc')->get();

        return view ('assistant.services', compact('currentDate', 'services'));
    }

    public function addService(Request $request)
    {
        // Check authorization using Gate
        $this->authorize('manage-services');
        
        // Check the parameters
        $request->validate([
            'service' => 'required|string|max:255|unique:services,service',
            'price' => 'required|numeric|min:0',
        ]);

        $service = Services::create([
            'service' => $request->input('service'),
            'price' => $request->input('price'),
        ]);

        if ($service) {
            return redirect()->route('assistant.service')->with('success', 'Service created successfully!');
        }
        
        return redirect()->route('assistant.service')->with('error', 'Failed to create service.');
    }

    public function updateService(Request $request, $id)
    {
        // Check authorization using Gate
        $this->authorize('update-services');
        
        $request->validate([
            'service' => 'required|string|max:255|unique:services,service,' . $id,
            'price' => 'required|numeric|min:0',
        ]);

        $service = Services::findOrFail($id);
        $service->update([
            'service' => $request->input('service'),
            'price' => $request->input('price'),
        ]);

        return redirect()->route('assistant.service')->with('success', 'Service updated successfully!');
    }

    public function deleteService($id)
    {
        // Check authorization using Gate
        $this->authorize('delete-services');
        
        $service = Services::findOrFail($id);
        $service->delete();

        return redirect()->route('assistant.service')->with('success', 'Service deleted successfully!');
    }

}
