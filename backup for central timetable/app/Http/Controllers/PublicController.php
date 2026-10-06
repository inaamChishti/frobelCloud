<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\{GeneralTimetable, AccessPermission, StaffAttendance, IagMeeting, Comment, StudentTest, Note, Payment, Role, Admission, Guardian, Kin, Student, medical_condition, TimeTable, Consent, StudentRequest, MockResult, Subject, Attendance, Book, Sale, Purchase, AssignBook, Activity};
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log; // Moved to the top
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mime\Part\Text\HtmlPart;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Swift_Attachment;
use Illuminate\Support\Facades\Session;
use Symfony\Component\Mime\Part\TextPart;
use Illuminate\Support\Facades\Notification;
use App\Notifications\StudentRequestNotification;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Dompdf\Dompdf;
use Dompdf\Options;
use Carbon\Carbon;
use DateTime;
use Auth;
use FPDF;


class PublicController extends Controller
{
    public function examReg()
    {
        // Get all users where is_main_branch = 1
        $branches = DB::table('users')
            ->where('is_main_branch', 1)
            ->select('branch_id', 'branch_name')
            ->get();
        // $branches = $branches->filter(function ($branch) {
        //     return stripos($branch->branch_name, 'Hayes') === false;
        // })->values();
        // dd($branches);

        return view('branchFrontend.examReg.publicPage', compact('branches'));
    }

    public function examRegHayes()
    {
        // Get all users where is_main_branch = 1
        $branches = DB::table('users')
            ->where('is_main_branch', 1)
            ->select('branch_id', 'branch_name')
            ->get();
        // $branches = $branches->filter(function ($branch) {
        //     return stripos($branch->branch_name, 'Hayes') === false;
        // })->values();
        // dd($branches);

        return view('branchFrontend.examReg.hayes', compact('branches'));
    }

     public function examRegApplicationForm()
     {
         $branches = DB::table('users')
            ->where('is_main_branch', 1)
            ->select('branch_id', 'branch_name')
            ->get();
        // $branches = $branches->filter(function ($branch) {
        //     return stripos($branch->branch_name, 'Hayes') === false;
        // })->values();
        // dd($branches);

        return view('branchFrontend.examReg.examRegApplicationForm', compact('branches'));
     }

    //   public function studentRequest(Request $request)
    // {
    //     // dd($request->all());
    //     try {
    //         // Validate request data
    //         $validated = $request->validate([
    //             'parent1_email' => 'required|email',
    //             'branch' => 'required',
    //             'parent1_first_name' => 'required',
    //             'parent1_last_name' => 'required',
    //             'consent_1_checkbox' => 'required',
    //             'how_did_you_hear' => 'required',
    //         ]);

    //         // Get branch_id from request
    //         $branchId = $request->input('branch');

    //         // Fetch branch details
    //         $branch = User::where('branch_id', $branchId)
    //             ->where('is_main_branch', 1)
    //             ->select('branch_id', 'branch_name')
    //             ->first();

    //         if (!$branch) {
    //             Log::error('Branch not found for branch_id: ' . $branchId);
    //             return response()->json(['error' => 'Invalid branch selected'], 400);
    //         }

    //         // Create student request with branch info
    //         $studentRequest = StudentRequest::create([
    //             'base64_data' => json_encode($request->except('_token')),
    //             'is_approved' => '0',
    //             'branch_id' => $branch->branch_id,
    //             'branch_name' => $branch->branch_name,
    //         ]);

    //         // Determine branch admin email based on branch name
    //         // $branchEmails = [
    //         //     'Barking' => 'inaam.chishti2@gmail.com',
    //         //     'Stratford' => 'inaam.chishti2@gmail.com',
    //         //     'Grays' => 'inaam.chishti2@gmail.com',
    //         // ];

    //         // Determine branch admin email based on branch name
    //         $branchEmails = [
    //             'Barking Centre' => 'barking@frobel.co.uk',
    //             'Stratford Centre' => 'stratford@frobel.co.uk',
    //             'Grays' => 'grays@frobel.co.uk',
    //         ];
    //         // dd($branch->branch_name);
    //         $branchEmail = $branchEmails[$branch->branch_name] ?? null;
    //         if (!$branchEmail) {
    //             Log::error('No email defined for branch: ' . $branch->branch_name);
    //             return response()->json(['error' => 'No email configured for this branch'], 500);
    //         }

    //         // Generate PDF
    //         $options = new Options();
    //         $options->set('isHtml5ParserEnabled', true);
    //         $options->set('isRemoteEnabled', true);
    //         $dompdf = new Dompdf($options);

    //         // Prepare form data for PDF
    //         $formData = json_decode($studentRequest->base64_data, true);
    //         $students = [];
    //         foreach ($formData['firstName'] as $index => $firstName) {
    //             $students[] = [
    //                 'firstName' => $firstName,
    //                 'middleName' => $formData['middleName'][$index] ?? '',
    //                 'lastName' => $formData['lastName'][$index] ?? '',
    //                 'dob' => $formData['dob'][$index] ?? '',
    //                 'gender' => $formData['gender'][$index] ?? '',
    //                 'yearInSchool' => $formData['yearInSchool'][$index] ?? '',
    //                 'tuitionHours' => $formData['tuitionHours'][$index] ?? '',
    //                 'medicalConditions' => $formData['medicalConditions'][$index] ?? '',
    //                 'allergies' => $formData['allergies'][$index] ?? '',
    //                 'additionalNeeds' => $formData['additionalNeeds'][$index] ?? '',
    //                 'gpPrefix' => $formData['gpPrefix'][$index] ?? '',
    //                 'gpFirstName' => $formData['gpFirstName'][$index] ?? '',
    //                 'gpLastName' => $formData['gpLastName'][$index] ?? '',
    //                 'gpAddress' => $formData['gpAddress'][$index] ?? '',
    //                 'gpAddressLineTwo' => $formData['gpAddressLineTwo'][$index] ?? '',
    //                 'city' => $formData['city'][$index] ?? '',
    //                 'CountyStateRegion' => $formData['CountyStateRegion'][$index] ?? '',
    //                 'zipCode' => $formData['zipCode'][$index] ?? '',
    //                 'country' => $formData['country'][$index] ?? '',
    //                 'GPPhone' => $formData['GPPhone'][$index] ?? '',
    //                 'medicalConsent' => $formData['medicalConsent'][$index] ?? '',
    //                 'photoConsent' => is_array($formData['photoConsent'][$index]) ? implode(', ', $formData['photoConsent'][$index]) : $formData['photoConsent'][$index] ?? '',
    //                 'leaveAlone' => $formData['leaveAlone'][$index] ?? '',
    //             ];
    //         }

    //         // PDF HTML content
    //         $html = '
    //         <!DOCTYPE html>
    //         <html>
    //         <head>
    //             <style>
    //                 body { font-family: Arial, sans-serif; font-size: 12px; }
    //                 h1, h2 { color: #67C0EA; }
    //                 table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    //                 th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    //                 th { background-color: #67C0EA; color: white; }
    //                 .section { margin-bottom: 20px; }
    //                 .signature-img { max-width: 200px; height: auto; }
    //             </style>
    //         </head>
    //         <body>
    //             <h1>Frobel Education Form Submission</h1>
    //             <div class="section">
    //                 <h2>Branch Information</h2>
    //                 <p><strong>Branch:</strong> ' . htmlspecialchars($branch->branch_name) . '</p>
    //             </div>';

    //         foreach ($students as $index => $student) {
    //             $html .= '
    //             <div class="section">
    //                 <h2>Student ' . ($index + 1) . ' Details</h2>
    //                 <table>
    //                     <tr><th>Field</th><th>Value</th></tr>
    //                     <tr><td>First Name</td><td>' . htmlspecialchars($student['firstName']) . '</td></tr>
    //                     <tr><td>Middle Name</td><td>' . htmlspecialchars($student['middleName']) . '</td></tr>
    //                     <tr><td>Last Name</td><td>' . htmlspecialchars($student['lastName']) . '</td></tr>
    //                     <tr><td>Date of Birth</td><td>' . htmlspecialchars($student['dob']) . '</td></tr>
    //                     <tr><td>Gender</td><td>' . htmlspecialchars($student['gender']) . '</td></tr>
    //                     <tr><td>Year in School</td><td>' . htmlspecialchars($student['yearInSchool']) . '</td></tr>
    //                     <tr><td>Tuition Hours</td><td>' . htmlspecialchars($student['tuitionHours']) . '</td></tr>
    //                     <tr><td>Medical Conditions</td><td>' . htmlspecialchars($student['medicalConditions']) . '</td></tr>
    //                     <tr><td>Allergies</td><td>' . htmlspecialchars($student['allergies']) . '</td></tr>
    //                     <tr><td>Additional Needs</td><td>' . htmlspecialchars($student['additionalNeeds']) . '</td></tr>
    //                     <tr><td>GP Prefix</td><td>' . htmlspecialchars($student['gpPrefix']) . '</td></tr>
    //                     <tr><td>GP First Name</td><td>' . htmlspecialchars($student['gpFirstName']) . '</td></tr>
    //                     <tr><td>GP Last Name</td><td>' . htmlspecialchars($student['gpLastName']) . '</td></tr>
    //                     <tr><td>GP Address</td><td>' . htmlspecialchars($student['gpAddress']) . '</td></tr>
    //                     <tr><td>GP Address Line 2</td><td>' . htmlspecialchars($student['gpAddressLineTwo']) . '</td></tr>
    //                     <tr><td>City</td><td>' . htmlspecialchars($student['city']) . '</td></tr>
    //                     <tr><td>County/State/Region</td><td>' . htmlspecialchars($student['CountyStateRegion']) . '</td></tr>
    //                     <tr><td>ZIP/Postal Code</td><td>' . htmlspecialchars($student['zipCode']) . '</td></tr>
    //                     <tr><td>Country</td><td>' . htmlspecialchars($student['country']) . '</td></tr>
    //                     <tr><td>GP Phone</td><td>' . htmlspecialchars($student['GPPhone']) . '</td></tr>
    //                     <tr><td>Medical Consent</td><td>' . htmlspecialchars($student['medicalConsent']) . '</td></tr>
    //                     <tr><td>Photo Consent</td><td>' . htmlspecialchars($student['photoConsent']) . '</td></tr>
    //                     <tr><td>Leave Alone</td><td>' . htmlspecialchars($student['leaveAlone']) . '</td></tr>
    //                 </table>
    //             </div>';
    //         }

    //         $html .= '
    //             <div class="section">
    //                 <h2>Parent/Guardian Details</h2>
    //                 <table>
    //                     <tr><th>Field</th><th>Value</th></tr>
    //                     <tr><td>First Name</td><td>' . htmlspecialchars($formData['parent1_first_name']) . '</td></tr>
    //                     <tr><td>Last Name</td><td>' . htmlspecialchars($formData['parent1_last_name']) . '</td></tr>
    //                     <tr><td>Address</td><td>' . htmlspecialchars($formData['parent1_Address']) . '</td></tr>
    //                     <tr><td>Address Line 2</td><td>' . htmlspecialchars($formData['parent1_Address_line2']) . '</td></tr>
    //                     <tr><td>City</td><td>' . htmlspecialchars($formData['parent1_city']) . '</td></tr>
    //                     <tr><td>County/State/Region</td><td>' . htmlspecialchars($formData['parent1_country_state_region']) . '</td></tr>
    //                     <tr><td>ZIP/Postal Code</td><td>' . htmlspecialchars($formData['parent1_zipCode']) . '</td></tr>
    //                     <tr><td>Country</td><td>' . htmlspecialchars($formData['parent1_country']) . '</td></tr>
    //                     <tr><td>Email</td><td>' . htmlspecialchars($formData['parent1_email']) . '</td></tr>
    //                     <tr><td>Mobile</td><td>' . htmlspecialchars($formData['parent1_mobile']) . '</td></tr>
    //                 </table>
    //             </div>
    //             <div class="section">
    //                 <h2>Emergency Contact Details</h2>
    //                 <table>
    //                     <tr><th>Field</th><th>Value</th></tr>
    //                     <tr><td>First Name</td><td>' . htmlspecialchars($formData['emergency_conatct1_first_name']) . '</td></tr>
    //                     <tr><td>Last Name</td><td>' . htmlspecialchars($formData['emergency_conatct1_last_name']) . '</td></tr>
    //                     <tr><td>Address</td><td>' . htmlspecialchars($formData['emergency_conatct1_Address']) . '</td></tr>
    //                     <tr><td>Address Line 2</td><td>' . htmlspecialchars($formData['emergency_conatct1_Address_line2']) . '</td></tr>
    //                     <tr><td>City</td><td>' . htmlspecialchars($formData['emergency_conatct1_city']) . '</td></tr>
    //                     <tr><td>County/State/Region</td><td>' . htmlspecialchars($formData['emergency_conatct1_country_state_region']) . '</td></tr>
    //                     <tr><td>ZIP/Postal Code</td><td>' . htmlspecialchars($formData['emergency_conatct1_zipCode']) . '</td></tr>
    //                     <tr><td>Country</td><td>' . htmlspecialchars($formData['emergency_conatct1_country']) . '</td></tr>
    //                     <tr><td>Email</td><td>' . htmlspecialchars($formData['emergency_conatct1_email']) . '</td></tr>
    //                     <tr><td>Mobile</td><td>' . htmlspecialchars($formData['emergency_conatct1_mobile']) . '</td></tr>
    //                 </table>
    //             </div>
    //             <div class="section">
    //                 <h2>Consent Details</h2>
    //                 <table>
    //                     <tr><th>Field</th><th>Value</th></tr>
    //                     <tr><td>First Name</td><td>' . htmlspecialchars($formData['consent_1_first_name']) . '</td></tr>
    //                     <tr><td>Last Name</td><td>' . htmlspecialchars($formData['consent_1_last_name']) . '</td></tr>
    //                     <tr><td>Date</td><td>' . htmlspecialchars($formData['consent_1date']) . '</td></tr>
    //                     <tr><td>Terms Agreed</td><td>' . htmlspecialchars($formData['consent_1_checkbox']) . '</td></tr>
    //                     <tr><td>Signature</td><td><img src="' . htmlspecialchars($formData['consent_1signature']) . '" class="signature-img" alt="Signature"></td></tr>
    //                 </table>
    //             </div>
    //             <div class="section">
    //                 <h2>Additional Information</h2>
    //                 <table>
    //                     <tr><th>Field</th><th>Value</th></tr>
    //                     <tr><td>How did you hear about us?</td><td>' . htmlspecialchars($formData['how_did_you_hear']) . '</td></tr>
    //                 </table>
    //             </div>
    //         </body>
    //         </html>';

    //         $dompdf->loadHtml($html);
    //         $dompdf->setPaper('A4', 'portrait');
    //         $dompdf->render();
    //         $pdfOutput = $dompdf->output();
    //         $pdfPath = storage_path('app/public/student_form_' . $studentRequest->id . '.pdf');
    //         file_put_contents($pdfPath, $pdfOutput);

    //         // Email template base
    //         $emailTemplate = '
    //         <!DOCTYPE html>
    //         <html>
    //         <head>
    //             <meta charset="UTF-8">
    //             <meta name="viewport" content="width=device-width, initial-scale=1.0">
    //             <title>%s</title>
    //         </head>
    //         <body style="font-family: Arial, sans-serif; background-color: #f5f7fa; padding: 20px; color: #333;">
    //             <!-- Logo -->
    //             <div style="text-align: center; margin-bottom: 20px;">
    //                 <img src="https://images.efrobel.com/Frobellogo.png" alt="Frobel Logo" style="max-width: 180px;">
    //             </div>
    //             <!-- Main Content -->
    //             <div style="background-color: #ffffff; padding: 25px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.05);">
    //                 <h2 style="color: #2e6da4;">Dear %s,</h2>
    //                 %s
    //                 <p>If you have any questions or require clarification, feel free to get in touch with us.</p>
    //                 <p>Kind regards,<br>
    //                 <strong>Frobel Education Team</strong><br>
    //                 Frobel Education</p>
    //             </div>
    //             <!-- Footer -->
    //             <div style="text-align: center; font-size: 13px; color: #777; margin-top: 30px;">
    //                 <p>
    //                     67–73 Longbridge Road, Barking, Essex, IG11 8TG<br>
    //                     Phone: <a href="tel:02089355931" style="color: #337ab7;">020 8935 5931</a> |
    //                     Email: <a href="mailto:exams@frobel.co.uk" style="color: #337ab7;">exams@frobel.co.uk</a>
    //                 </p>
    //                 <img src="https://images.efrobel.com/FrobelBranding.png" alt="Frobel Branding" style="max-width: 300px; margin-top: 10px;">
    //             </div>
    //         </body>
    //         </html>';

    //         // Send confirmation email to parent
    //         $parentEmail = $formData['parent1_email'];
    //         if (!empty($parentEmail) && filter_var($parentEmail, FILTER_VALIDATE_EMAIL)) {
    //             $parentEmailContent = sprintf(
    //                 $emailTemplate,
    //                 'Registration Confirmation',
    //                 'Learner',
    //                 '<p>Thank you for registering with Frobel Education. Your form has been successfully submitted to the ' . htmlspecialchars($branch->branch_name) . ' branch. We will be in touch shortly.</p>'
    //             );

    //             Mail::html($parentEmailContent, function ($message) use ($parentEmail) {
    //                 $message->to($parentEmail)
    //                         ->subject('Thank You for Your Registration - Frobel Education')
    //                         ->from('exams@frobel.co.uk', 'Frobel Education');
    //             });
    //         } else {
    //             Log::warning('Invalid or missing parent email: ' . ($parentEmail ?? 'NULL'));
    //         }

    //         // Send notification email to branch admin with PDF attachment
    //         $adminEmailContent = sprintf(
    //             $emailTemplate,
    //             'New Student Registration',
    //             'Administrator',
    //             '<p>A new student registration form has been submitted for the ' . htmlspecialchars($branch->branch_name) . ' branch. Please find the details in the attached PDF.</p>'
    //         );

    //         // Mail::html($adminEmailContent, function ($message) use ($branchEmail, $pdfPath, $studentRequest) {
    //         //     $message->to($branchEmail)
    //         //             ->subject('New Student Registration - ' . $studentRequest->id)
    //         //             ->from('exams@frobel.co.uk', 'Frobel Education')
    //         //             ->attach($pdfPath, [
    //         //                 'as' => 'student_form_' . $studentRequest->id . '.pdf',
    //         //                 'mime' => 'application/pdf',
    //         //             ]);
    //         // });

    //         // Notify users with access permissions
    //         $userIds = AccessPermission::where('page_name', 'LIKE', '%new_admission%')
    //             ->pluck('user_id')
    //             ->toArray();

    //         $users = User::whereIn('id', $userIds)->get();

    //         if ($users->isEmpty()) {
    //             Log::warning('No users found for notification with user_ids: ' . json_encode($userIds));
    //         } else {
    //             Notification::send($users, new StudentRequestNotification($studentRequest));
    //         }

    //         // Clean up PDF file
    //         if (file_exists($pdfPath)) {
    //             unlink($pdfPath);
    //         }

    //         return response()->json(['message' => 'Data stored and emails sent successfully']);
    //     } catch (\Exception $e) {
    //         Log::error('Error in studentRequest: ' . $e->getMessage());
    //         return response()->json(['error' => 'An error occurred', 'message' => $e->getMessage()], 500);
    //     }
    // }

    //     public function studentRequest(Request $request)
    // {
    //     dd($request->all());
    //     try {
    //         // Start a database transaction
    //         return DB::transaction(function () use ($request) {
    //             // Validate request data
    //             $validated = $request->validate([
    //                 'parent1_email' => 'required|email',
    //                 'branch' => 'required',
    //                 'parent1_first_name' => 'required',
    //                 'parent1_last_name' => 'required',
    //                 'consent_1_checkbox' => 'required',
    //                 'how_did_you_hear' => 'required',
    //             ]);

    //             // Get branch_id from request
    //             $branchId = $request->input('branch');

    //             // Fetch branch details
    //             $branch = User::where('branch_id', $branchId)
    //                 ->where('is_main_branch', 1)
    //                 ->select('branch_id', 'branch_name')
    //                 ->first();

    //             if (!$branch) {
    //                 Log::error('Branch not found for branch_id: ' . $branchId);
    //                 throw new \Exception('Invalid branch selected', 400);
    //             }

    //             // Create student request with branch info
    //             $studentRequest = StudentRequest::create([
    //                 'base64_data' => json_encode($request->except('_token')),
    //                 'is_approved' => '0',
    //                 'branch_id' => $branch->branch_id,
    //                 'branch_name' => $branch->branch_name,
    //             ]);

    //             // Determine branch admin email based on branch name
    //             $branchEmails = [
    //                 // 'Barking Centre' => 'barking@frobel.co.uk',
    //                 'Barking Centre' => 'admin@frobel.co.uk',
    //                 'Stratford Centre' => 'stratford@frobel.co.uk',
    //                 'Grays' => 'grays@frobel.co.uk',
    //             ];

    //             $branchEmail = $branchEmails[$branch->branch_name] ?? null;
    //             if (!$branchEmail) {
    //                 Log::error('No email defined for branch: ' . $branch->branch_name);
    //                 throw new \Exception('No email configured for this branch', 500);
    //             }

    //             // Generate PDF
    //             $options = new Options();
    //             $options->set('isHtml5ParserEnabled', true);
    //             $options->set('isRemoteEnabled', true);
    //             $dompdf = new Dompdf($options);

    //             // Prepare form data for PDF
    //             $formData = json_decode($studentRequest->base64_data, true);
    //             $students = [];
    //             foreach ($formData['firstName'] as $index => $firstName) {
    //                 $students[] = [
    //                     'firstName' => $firstName,
    //                     'middleName' => $formData['middleName'][$index] ?? '',
    //                     'lastName' => $formData['lastName'][$index] ?? '',
    //                     'dob' => $formData['dob'][$index] ?? '',
    //                     'gender' => $formData['gender'][$index] ?? '',
    //                     'yearInSchool' => $formData['yearInSchool'][$index] ?? '',
    //                     'tuitionHours' => $formData['tuitionHours'][$index] ?? '',
    //                     'medicalConditions' => $formData['medicalConditions'][$index] ?? '',
    //                     'allergies' => $formData['allergies'][$index] ?? '',
    //                     'additionalNeeds' => $formData['additionalNeeds'][$index] ?? '',
    //                     'gpPrefix' => $formData['gpPrefix'][$index] ?? '',
    //                     'gpFirstName' => $formData['gpFirstName'][$index] ?? '',
    //                     'gpLastName' => $formData['gpLastName'][$index] ?? '',
    //                     'gpAddress' => $formData['gpAddress'][$index] ?? '',
    //                     'gpAddressLineTwo' => $formData['gpAddressLineTwo'][$index] ?? '',
    //                     'city' => $formData['city'][$index] ?? '',
    //                     'CountyStateRegion' => $formData['CountyStateRegion'][$index] ?? '',
    //                     'zipCode' => $formData['zipCode'][$index] ?? '',
    //                     'country' => $formData['country'][$index] ?? '',
    //                     'GPPhone' => $formData['GPPhone'][$index] ?? '',
    //                     'medicalConsent' => $formData['medicalConsent'][$index] ?? '',
    //                     'photoConsent' => is_array($formData['photoConsent'][$index]) ? implode(', ', $formData['photoConsent'][$index]) : $formData['photoConsent'][$index] ?? '',
    //                     'leaveAlone' => $formData['leaveAlone'][$index] ?? '',
    //                 ];
    //             }

    //             // PDF HTML content
    //             $html = '
    //             <!DOCTYPE html>
    //             <html>
    //             <head>
    //                 <style>
    //                     body { font-family: Arial, sans-serif; font-size: 12px; }
    //                     h1, h2 { color: #67C0EA; }
    //                     table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    //                     th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    //                     th { background-color: #67C0EA; color: white; }
    //                     .section { margin-bottom: 20px; }
    //                     .signature-img { max-width: 200px; height: auto; }
    //                 </style>
    //             </head>
    //             <body>
    //                 <h1>Frobel Education Form Submission</h1>
    //                 <div class="section">
    //                     <h2>Branch Information</h2>
    //                     <p><strong>Branch:</strong> ' . htmlspecialchars($branch->branch_name) . '</p>
    //                 </div>';

    //             foreach ($students as $index => $student) {
    //                 $html .= '
    //                 <div class="section">
    //                     <h2>Student ' . ($index + 1) . ' Details</h2>
    //                     <table>
    //                         <tr><th>Field</th><th>Value</th></tr>
    //                         <tr><td>First Name</td><td>' . htmlspecialchars($student['firstName']) . '</td></tr>
    //                         <tr><td>Middle Name</td><td>' . htmlspecialchars($student['middleName']) . '</td></tr>
    //                         <tr><td>Last Name</td><td>' . htmlspecialchars($student['lastName']) . '</td></tr>
    //                         <tr><td>Date of Birth</td><td>' . htmlspecialchars($student['dob']) . '</td></tr>
    //                         <tr><td>Gender</td><td>' . htmlspecialchars($student['gender']) . '</td></tr>
    //                         <tr><td>Year in School</td><td>' . htmlspecialchars($student['yearInSchool']) . '</td></tr>
    //                         <tr><td>Tuition Hours</td><td>' . htmlspecialchars($student['tuitionHours']) . '</td></tr>
    //                         <tr><td>Medical Conditions</td><td>' . htmlspecialchars($student['medicalConditions']) . '</td></tr>
    //                         <tr><td>Allergies</td><td>' . htmlspecialchars($student['allergies']) . '</td></tr>
    //                         <tr><td>Additional Needs</td><td>' . htmlspecialchars($student['additionalNeeds']) . '</td></tr>
    //                         <tr><td>GP Prefix</td><td>' . htmlspecialchars($student['gpPrefix']) . '</td></tr>
    //                         <tr><td>GP First Name</td><td>' . htmlspecialchars($student['gpFirstName']) . '</td></tr>
    //                         <tr><td>GP Last Name</td><td>' . htmlspecialchars($student['gpLastName']) . '</td></tr>
    //                         <tr><td>GP Address</td><td>' . htmlspecialchars($student['gpAddress']) . '</td></tr>
    //                         <tr><td>GP Address Line 2</td><td>' . htmlspecialchars($student['gpAddressLineTwo']) . '</td></tr>
    //                         <tr><td>City</td><td>' . htmlspecialchars($student['city']) . '</td></tr>
    //                         <tr><td>County/State/Region</td><td>' . htmlspecialchars($student['CountyStateRegion']) . '</td></tr>
    //                         <tr><td>ZIP/Postal Code</td><td>' . htmlspecialchars($student['zipCode']) . '</td></tr>
    //                         <tr><td>Country</td><td>' . htmlspecialchars($student['country']) . '</td></tr>
    //                         <tr><td>GP Phone</td><td>' . htmlspecialchars($student['GPPhone']) . '</td></tr>
    //                         <tr><td>Medical Consent</td><td>' . htmlspecialchars($student['medicalConsent']) . '</td></tr>
    //                         <tr><td>Photo Consent</td><td>' . htmlspecialchars($student['photoConsent']) . '</td></tr>
    //                         <tr><td>Leave Alone</td><td>' . htmlspecialchars($student['leaveAlone']) . '</td></tr>
    //                     </table>
    //                 </div>';
    //             }

    //             $html .= '
    //                 <div class="section">
    //                     <h2>Parent/Guardian Details</h2>
    //                     <table>
    //                         <tr><th>Field</th><th>Value</th></tr>
    //                         <tr><td>First Name</td><td>' . htmlspecialchars($formData['parent1_first_name']) . '</td></tr>
    //                         <tr><td>Last Name</td><td>' . htmlspecialchars($formData['parent1_last_name']) . '</td></tr>
    //                         <tr><td>Address</td><td>' . htmlspecialchars($formData['parent1_Address']) . '</td></tr>
    //                         <tr><td>Address Line 2</td><td>' . htmlspecialchars($formData['parent1_Address_line2']) . '</td></tr>
    //                         <tr><td>City</td><td>' . htmlspecialchars($formData['parent1_city']) . '</td></tr>
    //                         <tr><td>County/State/Region</td><td>' . htmlspecialchars($formData['parent1_country_state_region']) . '</td></tr>
    //                         <tr><td>ZIP/Postal Code</td><td>' . htmlspecialchars($formData['parent1_zipCode']) . '</td></tr>
    //                         <tr><td>Country</td><td>' . htmlspecialchars($formData['parent1_country']) . '</td></tr>
    //                         <tr><td>Email</td><td>' . htmlspecialchars($formData['parent1_email']) . '</td></tr>
    //                         <tr><td>Mobile</td><td>' . htmlspecialchars($formData['parent1_mobile']) . '</td></tr>
    //                     </table>
    //                 </div>
    //                 <div class="section">
    //                     <h2>Emergency Contact Details</h2>
    //                     <table>
    //                         <tr><th>Field</th><th>Value</th></tr>
    //                         <tr><td>First Name</td><td>' . htmlspecialchars($formData['emergency_conatct1_first_name']) . '</td></tr>
    //                         <tr><td>Last Name</td><td>' . htmlspecialchars($formData['emergency_conatct1_last_name']) . '</td></tr>
    //                         <tr><td>Address</td><td>' . htmlspecialchars($formData['emergency_conatct1_Address']) . '</td></tr>
    //                         <tr><td>Address Line 2</td><td>' . htmlspecialchars($formData['emergency_conatct1_Address_line2']) . '</td></tr>
    //                         <tr><td>City</td><td>' . htmlspecialchars($formData['emergency_conatct1_city']) . '</td></tr>
    //                         <tr><td>County/State/Region</td><td>' . htmlspecialchars($formData['emergency_conatct1_country_state_region']) . '</td></tr>
    //                         <tr><td>ZIP/Postal Code</td><td>' . htmlspecialchars($formData['emergency_conatct1_zipCode']) . '</td></tr>
    //                         <tr><td>Country</td><td>' . htmlspecialchars($formData['emergency_conatct1_country']) . '</td></tr>
    //                         <tr><td>Email</td><td>' . htmlspecialchars($formData['emergency_conatct1_email']) . '</td></tr>
    //                         <tr><td>Mobile</td><td>' . htmlspecialchars($formData['emergency_conatct1_mobile']) . '</td></tr>
    //                     </table>
    //                 </div>
    //                 <div class="section">
    //                     <h2>Consent Details</h2>
    //                     <table>
    //                         <tr><th>Field</th><th>Value</th></tr>
    //                         <tr><td>First Name</td><td>' . htmlspecialchars($formData['consent_1_first_name']) . '</td></tr>
    //                         <tr><td>Last Name</td><td>' . htmlspecialchars($formData['consent_1_last_name']) . '</td></tr>
    //                         <tr><td>Date</td><td>' . htmlspecialchars($formData['consent_1date']) . '</td></tr>
    //                         <tr><td>Terms Agreed</td><td>' . htmlspecialchars($formData['consent_1_checkbox']) . '</td></tr>
    //                         <tr><td>Signature</td><td><img src="' . htmlspecialchars($formData['consent_1signature']) . '" class="signature-img" alt="Signature"></td></tr>
    //                     </table>
    //                 </div>
    //                 <div class="section">
    //                     <h2>Additional Information</h2>
    //                     <table>
    //                         <tr><th>Field</th><th>Value</th></tr>
    //                         <tr><td>How did you hear about us?</td><td>' . htmlspecialchars($formData['how_did_you_hear']) . '</td></tr>
    //                     </table>
    //                 </div>
    //             </body>
    //             </html>';

    //             $dompdf->loadHtml($html);
    //             $dompdf->setPaper('A4', 'portrait');
    //             $dompdf->render();
    //             $pdfOutput = $dompdf->output();
    //             $pdfPath = storage_path('app/public/student_form_' . $studentRequest->id . '.pdf');
    //             file_put_contents($pdfPath, $pdfOutput);

    //             // Email template base
    //             $emailTemplate = '
    //             <!DOCTYPE html>
    //             <html>
    //             <head>
    //                 <meta charset="UTF-8">
    //                 <meta name="viewport" content="width=device-width, initial-scale=1.0">
    //                 <title>%s</title>
    //             </head>
    //             <body style="font-family: Arial, sans-serif; background-color: #f5f7fa; padding: 20px; color: #333;">
    //                 <!-- Logo -->
    //                 <div style="text-align: center; margin-bottom: 20px;">
    //                     <img src="https://images.efrobel.com/Frobellogo.png" alt="Frobel Logo" style="max-width: 180px;">
    //                 </div>
    //                 <!-- Main Content -->
    //                 <div style="background-color: #ffffff; padding: 25px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.05);">
    //                     <h2 style="color: #2e6da4;">Dear %s,</h2>
    //                     %s
    //                     <p>If you have any questions or require clarification, feel free to get in touch with us.</p>
    //                     <p>Kind regards,<br>
    //                     <strong>Frobel Education Team</strong><br>
    //                     Frobel Education</p>
    //                 </div>
    //                 <!-- Footer -->
    //                <div style="text-align: center; font-size: 13px; color: #777; margin-top: 30px;">
    //                     <p>
    //                         67–73 Longbridge Road, Barking, Essex, IG11 8TG<br>
    //                         Phone: <a href="tel:02089355931" style="color: #337ab7;">020 8935 5931</a> |
    //                         Email: <a href="mailto:exams@frobel.co.uk" style="color: #337ab7;">exams@frobel.co.uk</a> |
    //                         Email: <a href="mailto:admin@frobel.co.uk" style="color: #337ab7;">admin@frobel.co.uk</a>
    //                     </p>
    //                     <img src="https://images.efrobel.com/FrobelBranding.png" alt="Frobel Branding" style="max-width: 300px; margin-top: 10px;">
    //                 </div>
    //             </body>
    //             </html>';

    //             // Send confirmation email to parent
    //             $parentEmail = $formData['parent1_email'];
    //             if (!empty($parentEmail) && filter_var($parentEmail, FILTER_VALIDATE_EMAIL)) {
    //                 $parentEmailContent = sprintf(
    //                     $emailTemplate,
    //                     'Registration Confirmation',
    //                     'Learner',
    //                     '<p>Thank you for registering with Frobel Education. Your form has been successfully submitted to the ' . htmlspecialchars($branch->branch_name) . ' branch. We will be in touch shortly.</p>'
    //                 );

    //                 Mail::html($parentEmailContent, function ($message) use ($parentEmail) {
    //                     $message->to($parentEmail)
    //                             ->subject('Thank You for Your Registration - Frobel Education')
    //                             ->from('no-reply@frobel.co.uk', 'Frobel Education');
    //                 });
    //             } else {
    //                 Log::warning('Invalid or missing parent email: ' . ($parentEmail ?? 'NULL'));
    //                 throw new \Exception('Invalid or missing parent email', 400);
    //             }

    //             // Send notification email to branch admin with PDF attachment
    //             $adminEmailContent = sprintf(
    //                 $emailTemplate,
    //                 'New Student Registration',
    //                 'Administrator',
    //                 '<p>A new student registration form has been submitted for the ' . htmlspecialchars($branch->branch_name) . ' branch. Please find the details in the attached PDF.</p>'
    //             );

    //             // Uncomment this when ready to send admin email

    //             Mail::html($adminEmailContent, function ($message) use ($branchEmail, $pdfPath, $studentRequest) {
    //                 $message->to($branchEmail)
    //                         ->subject('New Student Registration - ' . $studentRequest->id)
    //                         ->from('exams@frobel.co.uk', 'Frobel Education')
    //                         ->attach($pdfPath, [
    //                             'as' => 'student_form_' . $studentRequest->id . '.pdf',
    //                             'mime' => 'application/pdf',
    //                         ]);
    //             });


    //             // Notify users with access permissions
    //             $userIds = AccessPermission::where('page_name', 'LIKE', '%new_admission%')
    //                 ->pluck('user_id')
    //                 ->toArray();

    //             $users = User::whereIn('id', $userIds)->get();

    //             if ($users->isEmpty()) {
    //                 Log::warning('No users found for notification with user_ids: ' . json_encode($userIds));
    //             } else {
    //                 Notification::send($users, new StudentRequestNotification($studentRequest));
    //             }

    //             // Clean up PDF file
    //             if (file_exists($pdfPath)) {
    //                 unlink($pdfPath);
    //             }

    //             return response()->json(['message' => 'Data stored and emails sent successfully']);
    //         });
    //     } catch (\Exception $e) {
    //         Log::error('Error in studentRequest: ' . $e->getMessage());
    //         return response()->json(['error' => 'An error occurred', 'message' => $e->getMessage()], $e->getCode() ?: 500);
    //     }
    // }
    // public function studentRequest(Request $request)
    // {
    //     dd($request->all()); // Uncomment for debugging
    //     try {
    //         // Start a database transaction
    //         return DB::transaction(function () use ($request) {
    //             // Validate request data
    //             $validated = $request->validate([
    //                 'parent1_email' => 'required|email',
    //                 'branch' => 'required',
    //                 'parent1_first_name' => 'required',
    //                 'parent1_last_name' => 'required',
    //                 'consent_1_checkbox' => 'required',
    //                 'how_did_you_hear' => 'required',
    //             ]);

    //             // Get branch_id from request
    //             $branchId = $request->input('branch');

    //             // Fetch branch details
    //             $branch = User::where('branch_id', $branchId)
    //                 ->where('is_main_branch', 1)
    //                 ->select('branch_id', 'branch_name')
    //                 ->first();

    //             if (!$branch) {
    //                 Log::error('Branch not found for branch_id: ' . $branchId);
    //                 throw new \Exception('Invalid branch selected', 400);
    //             }

    //             // Create student request with branch info
    //             $studentRequest = StudentRequest::create([
    //                 'base64_data' => json_encode($request->except('_token')),
    //                 'is_approved' => '0',
    //                 'branch_id' => $branch->branch_id,
    //                 'branch_name' => $branch->branch_name,
    //             ]);

    //             // Determine branch admin email based on branch name
    //             $branchEmails = [
    //                 'Barking Centre' => 'admin@frobel.co.uk',
    //                 'Stratford Centre' => 'stratford@frobel.co.uk',
    //                 'Grays' => 'grays@frobel.co.uk',
    //             ];

    //             $branchEmail = $branchEmails[$branch->branch_name] ?? null;
    //             if (!$branchEmail) {
    //                 Log::error('No email defined for branch: ' . $branch->branch_name);
    //                 throw new \Exception('No email configured for this branch', 500);
    //             }
    //             //    dd($branchEmails,$branch->branch_name,$branchEmail);

    //             // Generate PDF
    //             $options = new Options();
    //             $options->set('isHtml5ParserEnabled', true);
    //             $options->set('isRemoteEnabled', true);
    //             $dompdf = new Dompdf($options);

    //             // Prepare form data for PDF
    //             $formData = json_decode($studentRequest->base64_data, true);
    //             $students = [];
    //             foreach ($formData['student'] ?? [] as $index => $studentData) {
    //                 $students[] = [
    //                     'firstName' => $studentData['firstName'] ?? '',
    //                     'lastName' => $studentData['lastName'] ?? '',
    //                     'dob' => $studentData['dob'] ?? '',
    //                     'gender' => $studentData['gender'] ?? '',
    //                     'yearInSchool' => $studentData['yearInSchool'] ?? '',
    //                     'tuitionHours' => $studentData['tuitionHours'] ?? '',
    //                     'medicalConditions' => $studentData['medicalConditions'] ?? '',
    //                     'allergies' => $studentData['allergies'] ?? '',
    //                     'additionalNeeds' => $studentData['additionalNeeds'] ?? '',
    //                     'gpPrefix' => $studentData['gpPrefix'] ?? '',
    //                     'gpFirstName' => $studentData['gpFirstName'] ?? '',
    //                     'gpLastName' => $studentData['gpLastName'] ?? '',
    //                     'gpAddress' => $studentData['gpAddress'] ?? '',
    //                     'gpAddressLineTwo' => $studentData['gpAddressLineTwo'] ?? '',
    //                     'gp_city' => $studentData['gp_city'] ?? '',
    //                     'gp_countyStateRegion' => $studentData['gp_countyStateRegion'] ?? '',
    //                     'gpzipCode' => $studentData['gpzipCode'] ?? '',
    //                     'gpcountry' => $studentData['gpcountry'] ?? '',
    //                     'GPPhone' => $studentData['GPPhone'] ?? '',
    //                     'medicalConsent' => $studentData['medicalConsent'] ?? '',
    //                     'photoConsent' => isset($studentData['photoConsent']) && is_array($studentData['photoConsent']) ? implode(', ', $studentData['photoConsent']) : '',
    //                     'leaveAlone' => $studentData['leaveAlone'] ?? '',
    //                 ];
    //             }

    //             // PDF HTML content
    //             $html = '
    //         <!DOCTYPE html>
    //         <html>
    //         <head>
    //             <style>
    //                 body { font-family: Arial, sans-serif; font-size: 12px; }
    //                 h1, h2 { color: #67C0EA; }
    //                 table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    //                 th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    //                 th { background-color: #67C0EA; color: white; }
    //                 .section { margin-bottom: 20px; }
    //                 .signature-img { max-width: 200px; height: auto; }
    //             </style>
    //         </head>
    //         <body>
    //             <h1>Frobel Education Form Submission</h1>
    //             <div class="section">
    //                 <h2>Branch Information</h2>
    //                 <p><strong>Branch:</strong> ' . htmlspecialchars($branch->branch_name) . '</p>
    //             </div>';

    //             foreach ($students as $index => $student) {
    //                 $html .= '
    //             <div class="section">
    //                 <h2>Student ' . ($index + 1) . ' Details</h2>
    //                 <table>
    //                     <tr><th>Field</th><th>Value</th></tr>
    //                     <tr><td>First Name</td><td>' . htmlspecialchars($student['firstName']) . '</td></tr>
    //                     <tr><td>Last Name</td><td>' . htmlspecialchars($student['lastName']) . '</td></tr>
    //                     <tr><td>Date of Birth</td><td>' . htmlspecialchars($student['dob']) . '</td></tr>
    //                     <tr><td>Gender</td><td>' . htmlspecialchars($student['gender']) . '</td></tr>
    //                     <tr><td>Year in School</td><td>' . htmlspecialchars($student['yearInSchool']) . '</td></tr>
    //                     <tr><td>Tuition Hours</td><td>' . htmlspecialchars($student['tuitionHours']) . '</td></tr>
    //                     <tr><td>Medical Conditions</td><td>' . htmlspecialchars($student['medicalConditions']) . '</td></tr>
    //                     <tr><td>Allergies</td><td>' . htmlspecialchars($student['allergies']) . '</td></tr>
    //                     <tr><td>Additional Needs</td><td>' . htmlspecialchars($student['additionalNeeds']) . '</td></tr>
    //                     <tr><td>GP Prefix</td><td>' . htmlspecialchars($student['gpPrefix']) . '</td></tr>
    //                     <tr><td>GP First Name</td><td>' . htmlspecialchars($student['gpFirstName']) . '</td></tr>
    //                     <tr><td>GP Last Name</td><td>' . htmlspecialchars($student['gpLastName']) . '</td></tr>
    //                     <tr><td>GP Address</td><td>' . htmlspecialchars($student['gpAddress']) . '</td></tr>
    //                     <tr><td>GP Address Line 2</td><td>' . htmlspecialchars($student['gpAddressLineTwo']) . '</td></tr>
    //                     <tr><td>City</td><td>' . htmlspecialchars($student['gp_city']) . '</td></tr>
    //                     <tr><td>County/State/Region</td><td>' . htmlspecialchars($student['gp_countyStateRegion']) . '</td></tr>
    //                     <tr><td>ZIP/Postal Code</td><td>' . htmlspecialchars($student['gpzipCode']) . '</td></tr>
    //                     <tr><td>Country</td><td>' . htmlspecialchars($student['gpcountry']) . '</td></tr>
    //                     <tr><td>GP Phone</td><td>' . htmlspecialchars($student['GPPhone']) . '</td></tr>
    //                     <tr><td>Medical Consent</td><td>' . htmlspecialchars($student['medicalConsent']) . '</td></tr>
    //                     <tr><td>Photo Consent</td><td>' . htmlspecialchars($student['photoConsent']) . '</td></tr>
    //                     <tr><td>Leave Alone</td><td>' . htmlspecialchars($student['leaveAlone']) . '</td></tr>
    //                 </table>
    //             </div>';
    //             }

    //             $html .= '
    //             <div class="section">
    //                 <h2>Parent/Guardian Details</h2>
    //                 <table>
    //                     <tr><th>Field</th><th>Value</th></tr>
    //                     <tr><td>First Name</td><td>' . htmlspecialchars($formData['parent1_first_name'] ?? '') . '</td></tr>
    //                     <tr><td>Last Name</td><td>' . htmlspecialchars($formData['parent1_last_name'] ?? '') . '</td></tr>
    //                     <tr><td>Address</td><td>' . htmlspecialchars($formData['parent1_Address'] ?? '') . '</td></tr>
    //                     <tr><td>Address Line 2</td><td>' . htmlspecialchars($formData['parent1_Address_line2'] ?? '') . '</td></tr>
    //                     <tr><td>City</td><td>' . htmlspecialchars($formData['parent1_city'] ?? '') . '</td></tr>
    //                     <tr><td>County/State/Region</td><td>' . htmlspecialchars($formData['parent1_country_state_region'] ?? '') . '</td></tr>
    //                     <tr><td>ZIP/Postal Code</td><td>' . htmlspecialchars($formData['parent1_zipCode'] ?? '') . '</td></tr>
    //                     <tr><td>Country</td><td>' . htmlspecialchars($formData['parent1_country'] ?? '') . '</td></tr>
    //                     <tr><td>Email</td><td>' . htmlspecialchars($formData['parent1_email'] ?? '') . '</td></tr>
    //                     <tr><td>Mobile</td><td>' . htmlspecialchars($formData['parent1_mobile'] ?? '') . '</td></tr>
    //                 </table>
    //             </div>
    //             <div class="section">
    //                 <h2>Emergency Contact Details</h2>
    //                 <table>
    //                     <tr><th>Field</th><th>Value</th></tr>
    //                     <tr><td>First Name</td><td>' . htmlspecialchars($formData['emergency_conatct1_first_name'] ?? '') . '</td></tr>
    //                     <tr><td>Last Name</td><td>' . htmlspecialchars($formData['emergency_conatct1_last_name'] ?? '') . '</td></tr>
    //                     <tr><td>Address</td><td>' . htmlspecialchars($formData['emergency_conatct1_Address'] ?? '') . '</td></tr>
    //                     <tr><td>Address Line 2</td><td>' . htmlspecialchars($formData['emergency_conatct1_Address_line2'] ?? '') . '</td></tr>
    //                     <tr><td>City</td><td>' . htmlspecialchars($formData['emergency_conatct1_city'] ?? '') . '</td></tr>
    //                     <tr><td>County/State/Region</td><td>' . htmlspecialchars($formData['emergency_conatct1_country_state_region'] ?? '') . '</td></tr>
    //                     <tr><td>ZIP/Postal Code</td><td>' . htmlspecialchars($formData['emergency_conatct1_zipCode'] ?? '') . '</td></tr>
    //                     <tr><td>Country</td><td>' . htmlspecialchars($formData['emergency_conatct1_country'] ?? '') . '</td></tr>
    //                     <tr><td>Email</td><td>' . htmlspecialchars($formData['emergency_conatct1_email'] ?? '') . '</td></tr>
    //                     <tr><td>Mobile</td><td>' . htmlspecialchars($formData['emergency_conatct1_mobile'] ?? '') . '</td></tr>
    //                 </table>
    //             </div>
    //             <div class="section">
    //                 <h2>Consent Details</h2>
    //                 <table>
    //                     <tr><th>Field</th><th>Value</th></tr>
    //                     <tr><td>First Name</td><td>' . htmlspecialchars($formData['consent_1_first_name'] ?? '') . '</td></tr>
    //                     <tr><td>Last Name</td><td>' . htmlspecialchars($formData['consent_1_last_name'] ?? '') . '</td></tr>
    //                     <tr><td>Date</td><td>' . htmlspecialchars($formData['consent_1date'] ?? '') . '</td></tr>
    //                     <tr><td>Terms Agreed</td><td>' . htmlspecialchars($formData['consent_1_checkbox'] ?? '') . '</td></tr>
    //                     <tr><td>Signature</td><td><img src="' . htmlspecialchars($formData['consent_1signature'] ?? '') . '" class="signature-img" alt="Signature"></td></tr>
    //                 </table>
    //             </div>
    //             <div class="section">
    //                 <h2>Additional Information</h2>
    //                 <table>
    //                     <tr><th>Field</th><th>Value</th></tr>
    //                     <tr><td>How did you hear about us?</td><td>' . htmlspecialchars($formData['how_did_you_hear'] ?? '') . '</td></tr>
    //                 </table>
    //             </div>
    //         </body>
    //         </html>';

    //             $dompdf->loadHtml($html);
    //             $dompdf->setPaper('A4', 'portrait');
    //             $dompdf->render();
    //             $pdfOutput = $dompdf->output();
    //             $pdfPath = storage_path('app/public/student_form_' . $studentRequest->id . '.pdf');
    //             file_put_contents($pdfPath, $pdfOutput);

    //             // Email template base
    //             $emailTemplate = '
    //         <!DOCTYPE html>
    //         <html>
    //         <head>
    //             <meta charset="UTF-8">
    //             <meta name="viewport" content="width=device-width, initial-scale=1.0">
    //             <title>%s</title>
    //         </head>
    //         <body style="font-family: Arial, sans-serif; background-color: #f5f7fa; padding: 20px; color: #333;">
    //             <!-- Logo -->
    //             <div style="text-align: center; margin-bottom: 20px;">
    //                 <img src="https://images.efrobel.com/Frobellogo.png" alt="Frobel Logo" style="max-width: 180px;">
    //             </div>
    //             <!-- Main Content -->
    //             <div style="background-color: #ffffff; padding: 25px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.05);">
    //                 <h2 style="color: #2e6da4;">Dear %s,</h2>
    //                 %s
    //                 <p>If you have any questions or require clarification, feel free to get in touch with us.</p>
    //                 <p>Kind regards,<br>
    //                 <strong>Frobel Education Team</strong><br>
    //                 Frobel Education</p>
    //             </div>
    //             <!-- Footer -->
    //             <div style="text-align: center; font-size: 13px; color: #777; margin-top: 30px;">
    //                 <p>
    //                     67–73 Longbridge Road, Barking, Essex, IG11 8TG<br>
    //                     Phone: <a href="tel:02089355931" style="color: #337ab7;">020 8935 5931</a> |
    //                     Email: <a href="mailto:exams@frobel.co.uk" style="color: #337ab7;">exams@frobel.co.uk</a> |
    //                     Email: <a href="mailto:admin@frobel.co.uk" style="color: #337ab7;">admin@frobel.co.uk</a>
    //                 </p>
    //                 <img src="https://images.efrobel.com/FrobelBranding.png" alt="Frobel Branding" style="max-width: 300px; margin-top: 10px;">
    //             </div>
    //         </body>
    //         </html>';

    //             // Send confirmation email to parent
    //             $parentEmail = $formData['parent1_email'] ?? '';
    //             if (!empty($parentEmail) && filter_var($parentEmail, FILTER_VALIDATE_EMAIL)) {
    //                 $parentEmailContent = sprintf(
    //                     $emailTemplate,
    //                     'Registration Confirmation',
    //                     'Learner',
    //                     '<p>Thank you for registering with Frobel Education. Your form has been successfully submitted to the ' . htmlspecialchars($branch->branch_name) . ' branch. We will be in touch shortly.</p>'
    //                 );

    //                 // Mail::html($parentEmailContent, function ($message) use ($parentEmail) {
    //                 //     $message->to($parentEmail)
    //                 //             ->subject('Thank You for Your Registration - Frobel Education')
    //                 //             ->from('no-reply@frobel.co.uk', 'Frobel Education');
    //                 // });
    //             } else {
    //                 Log::warning('Invalid or missing parent email: ' . ($parentEmail ?? 'NULL'));
    //                 throw new \Exception('Invalid or missing parent email', 400);
    //             }

    //             // Send notification email to branch admin with PDF attachment
    //             $adminEmailContent = sprintf(
    //                 $emailTemplate,
    //                 'New Student Registration',
    //                 'Administrator',
    //                 '<p>A new student registration form has been submitted for the ' . htmlspecialchars($branch->branch_name) . ' branch. Please find the details in the attached PDF.</p>'
    //             );

    //             // Mail::html($adminEmailContent, function ($message) use ($branchEmail, $pdfPath, $studentRequest) {
    //             //     $message->to($branchEmail)
    //             //             ->subject('New Student Registration - ' . $studentRequest->id)
    //             //             ->from('exams@frobel.co.uk', 'Frobel Education')
    //             //             ->attach($pdfPath, [
    //             //                 'as' => 'student_form_' . $studentRequest->id . '.pdf',
    //             //                 'mime' => 'application/pdf',
    //             //             ]);
    //             // });

    //             // Notify users with access permissions
    //             $userIds = AccessPermission::where('page_name', 'LIKE', '%new_admission%')
    //                 ->pluck('user_id')
    //                 ->toArray();

    //             $users = User::whereIn('id', $userIds)->get();

    //             if ($users->isEmpty()) {
    //                 Log::warning('No users found for notification with user_ids: ' . json_encode($userIds));
    //             } else {
    //                 Notification::send($users, new StudentRequestNotification($studentRequest));
    //             }

    //             // Clean up PDF file
    //             if (file_exists($pdfPath)) {
    //                 unlink($pdfPath);
    //             }

    //             return response()->json(['message' => 'Data stored and emails sent successfully']);
    //         });
    //     } catch (\Exception $e) {
    //         Log::error('Error in studentRequest: ' . $e->getMessage());
    //         return response()->json(['error' => 'An error occurred', 'message' => $e->getMessage()], $e->getCode() ?: 500);
    //     }
    // }
    public function studentRequest(Request $request)
    {
        // dd($request->all()); // Uncomment for debugging
        try {
            // Start a database transaction
            return DB::transaction(function () use ($request) {
                // Validate request data
                $validated = $request->validate([
                    'parent1_email' => 'required|email',
                    'branch' => 'required',
                    'parent1_first_name' => 'required',
                    'parent1_last_name' => 'required',
                    'consent_1_checkbox' => 'required',
                    'how_did_you_hear' => 'required',
                ]);

                // Get branch_id from request
                $branchId = $request->input('branch');

                // Fetch branch details
                $branch = User::where('branch_id', $branchId)
                    ->where('is_main_branch', 1)
                    ->select('branch_id', 'branch_name')
                    ->first();

                if (!$branch) {
                    Log::error('Branch not found for branch_id: ' . $branchId);
                    throw new \Exception('Invalid branch selected', 400);
                }

                // Prepare terms and conditions as a JSON object
                $terms = [
                    'term_1' => $request->input('term_1', ''),
                    'term_2' => $request->input('term_2', ''),
                    'term_3' => $request->input('term_3', ''),
                    'term_4' => $request->input('term_4', ''),
                    'term_5' => $request->input('term_5', ''),
                    'term_6' => $request->input('term_6', ''),
                    'term_7' => $request->input('term_7', ''),
                    'term_8' => $request->input('term_8', ''),
                    'term_9' => $request->input('term_9', ''),
                    'term_10' => $request->input('term_10', ''),
                    'term_11' => $request->input('term_11', ''),
                    'term_12' => $request->input('term_12', ''),
                ];

                // Prepare form data to get student name
                $formData = $request->except('_token');
                $firstName = $formData['firstName'][0] ?? 'Student';
                $lastName = $formData['lastName'][0] ?? '';
                // Sanitize student name for file naming
                $sanitizedName = preg_replace('/[^A-Za-z0-9\-]/', '_', trim($firstName . '_' . $lastName));
                $sanitizedName = $sanitizedName ?: 'Student'; // Fallback if name is empty
                // Generate PDF name with student name and ID
                $pdfFileName = 'student_form_' . $sanitizedName . '_' . (StudentRequest::max('id') + 1) . '.pdf';
                $pdfPath = public_path('student_form/' . $pdfFileName);

                // Create student request with branch info, terms, and PDF path
                $studentRequest = StudentRequest::create([
                    'base64_data' => json_encode($request->except('_token')),
                    'termsAndConditions' => json_encode($terms),
                    'is_approved' => '0',
                    'branch_id' => $branch->branch_id,
                    'branch_name' => $branch->branch_name,
                    'pdf_name' => 'student_form/' . $pdfFileName,
                ]);

                // Determine branch admin email based on branch name
                $branchEmails = [
                    'Barking Centre' => 'admin@frobel.co.uk',
                    'Stratford Centre' => 'stratford@frobel.co.uk',
                    'Grays' => 'grays@frobel.co.uk',
                ];

                $branchEmail = $branchEmails[$branch->branch_name] ?? null;
                // if (!$branchEmail) {
                //     Log::error('No email defined for branch: ' . $branch->branch_name);
                //     throw new \Exception('No email configured for this branch', 500);
                // }

                // Generate PDF
                $options = new Options();
                $options->set('isHtml5ParserEnabled', true);
                $options->set('isRemoteEnabled', true);
                $options->set('defaultCharset', 'UTF-8');
                $options->set('defaultFont', 'DejaVu Sans'); // Set DejaVu Sans for checkmark support
                // Log font settings for debugging
                Log::debug('Dompdf font settings: ' . json_encode($options->get('defaultFont')));
                $dompdf = new Dompdf($options);

                // Prepare form data for PDF
                $formData = json_decode($studentRequest->base64_data, true);
                $termsData = json_decode($studentRequest->termsAndConditions, true);
                $students = [];
                foreach ($formData['firstName'] ?? [] as $index => $firstName) {
                    // Format date of birth to DD/MM/YYYY
                    $dob = $formData['dob'][$index] ?? '';
                    if ($dob) {
                        $dob = (new DateTime($dob))->format('d/m/Y');
                    }
                    $students[] = [
                        'firstName' => $formData['firstName'][$index] ?? '',
                        'lastName' => $formData['lastName'][$index] ?? '',
                        'dob' => $dob,
                        'gender' => $formData['gender'][$index] ?? '',
                        'yearInSchool' => $formData['yearInSchool'][$index] ?? '',
                        'tuitionHours' => $formData['tuitionHours'][$index] ?? '',
                        'medicalConditions' => $formData['medicalConditions'][$index] ?? '',
                        'allergies' => $formData['allergies'][$index] ?? '',
                        'additionalNeeds' => $formData['additionalNeeds'][$index] ?? '',
                        'gpPrefix' => $formData['gpPrefix'][$index] ?? '',
                        'gpFirstName' => $formData['gpFirstName'][$index] ?? '',
                        'gpLastName' => $formData['gpLastName'][$index] ?? '',
                        'gpAddress' => $formData['gpAddress'][$index] ?? '',
                        'gpAddressLineTwo' => $formData['gpAddressLineTwo'][$index] ?? '',
                        'city' => $formData['city'][$index] ?? '',
                        'CountyStateRegion' => $formData['CountyStateRegion'][$index] ?? '',
                        'zipCode' => $formData['zipCode'][$index] ?? '',
                        'country' => $formData['country'][$index] ?? '',
                        'GPPhone' => $formData['GPPhone'][$index] ?? '',
                        'medicalConsent' => $formData['medicalConsent'][$index] ?? '',
                        'photoConsent' => isset($formData['photoConsent'][$index]) && is_array($formData['photoConsent'][$index]) ? implode(', ', $formData['photoConsent'][$index]) : '',
                        'leaveAlone' => $formData['leaveAlone'][$index] ?? '',
                    ];
                }

                // Format consent date to DD/MM/YYYY
                $consentDate = $formData['consent_1date'] ?? '';
                if ($consentDate) {
                    $consentDate = (new DateTime($consentDate))->format('d/m/Y');
                }

                // PDF HTML content
                $html = '
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <style>
                        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 12px; }
                        h1, h2 { color: #67C0EA; }
                        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                        th { background-color: #67C0EA; color: white; }
                        .section { margin-bottom: 20px; }
                        .signature-img { max-width: 200px; height: auto; }
                        ol { padding-left: 20px; }
                        ol li { margin-bottom: 10px; }
                    </style>
                </head>
                <body>
                    <h1>Frobel Education Form Submission</h1>
                    <div class="section">
                        <h2>Branch Information</h2>
                        <p><strong>Branch:</strong> ' . htmlspecialchars($branch->branch_name) . '</p>
                    </div>';

                foreach ($students as $index => $student) {
                    $html .= '
                    <div class="section">
                        <h2>Student ' . ($index + 1) . ' Details</h2>
                        <table>
                            <tr><th>Field</th><th>Value</th></tr>
                            <tr><td>First Name</td><td>' . htmlspecialchars($student['firstName']) . '</td></tr>
                            <tr><td>Last Name</td><td>' . htmlspecialchars($student['lastName']) . '</td></tr>
                            <tr><td>Date of Birth</td><td>' . htmlspecialchars($student['dob']) . '</td></tr>
                            <tr><td>Gender</td><td>' . htmlspecialchars($student['gender']) . '</td></tr>
                            <tr><td>Year in School</td><td>' . htmlspecialchars($student['yearInSchool']) . '</td></tr>
                            <tr><td>Tuition Hours</td><td>' . htmlspecialchars($student['tuitionHours']) . '</td></tr>
                            <tr><td>Medical Conditions</td><td>' . htmlspecialchars($student['medicalConditions']) . '</td></tr>
                            <tr><td>Allergies</td><td>' . htmlspecialchars($student['allergies']) . '</td></tr>
                            <tr><td>Additional Needs</td><td>' . htmlspecialchars($student['additionalNeeds']) . '</td></tr>
                            <tr><td>GP Prefix</td><td>' . htmlspecialchars($student['gpPrefix']) . '</td></tr>
                            <tr><td>GP First Name</td><td>' . htmlspecialchars($student['gpFirstName']) . '</td></tr>
                            <tr><td>GP Last Name</td><td>' . htmlspecialchars($student['gpLastName']) . '</td></tr>
                            <tr><td>GP Address</td><td>' . htmlspecialchars($student['gpAddress']) . '</td></tr>
                            <tr><td>GP Address Line 2</td><td>' . htmlspecialchars($student['gpAddressLineTwo']) . '</td></tr>
                            <tr><td>City</td><td>' . htmlspecialchars($student['city']) . '</td></tr>
                            <tr><td>County/State/Region</td><td>' . htmlspecialchars($student['CountyStateRegion']) . '</td></tr>
                            <tr><td>ZIP/Postal Code</td><td>' . htmlspecialchars($student['zipCode']) . '</td></tr>
                            <tr><td>Country</td><td>' . htmlspecialchars($student['country']) . '</td></tr>
                            <tr><td>GP Phone</td><td>' . htmlspecialchars($student['GPPhone']) . '</td></tr>
                            <tr><td>Medical Consent</td><td>' . htmlspecialchars($student['medicalConsent']) . '</td></tr>
                            <tr><td>Photo Consent</td><td>' . htmlspecialchars($student['photoConsent']) . '</td></tr>
                            <tr><td>Leave Alone</td><td>' . htmlspecialchars($student['leaveAlone']) . '</td></tr>
                        </table>
                    </div>';
                }

                $html .= '
                    <div class="section">
                        <h2>Parent/Guardian Details</h2>
                        <table>
                            <tr><th>Field</th><th>Value</th></tr>
                            <tr><td>First Name</td><td>' . htmlspecialchars($formData['parent1_first_name'] ?? '') . '</td></tr>
                            <tr><td>Last Name</td><td>' . htmlspecialchars($formData['parent1_last_name'] ?? '') . '</td></tr>
                            <tr><td>Address</td><td>' . htmlspecialchars($formData['parent1_Address'] ?? '') . '</td></tr>
                            <tr><td>Address Line 2</td><td>' . htmlspecialchars($formData['parent1_Address_line2'] ?? '') . '</td></tr>
                            <tr><td>City</td><td>' . htmlspecialchars($formData['parent1_city'] ?? '') . '</td></tr>
                            <tr><td>County/State/Region</td><td>' . htmlspecialchars($formData['parent1_country_state_region'] ?? '') . '</td></tr>
                            <tr><td>ZIP/Postal Code</td><td>' . htmlspecialchars($formData['parent1_zipCode'] ?? '') . '</td></tr>
                            <tr><td>Country</td><td>' . htmlspecialchars($formData['parent1_country'] ?? '') . '</td></tr>
                            <tr><td>Email</td><td>' . htmlspecialchars($formData['parent1_email'] ?? '') . '</td></tr>
                            <tr><td>Mobile</td><td>' . htmlspecialchars($formData['parent1_mobile'] ?? '') . '</td></tr>
                        </table>
                    </div>
                    <div class="section">
                        <h2>Emergency Contact Details</h2>
                        <table>
                            <tr><th>Field</th><th>Value</th></tr>
                            <tr><td>First Name</td><td>' . htmlspecialchars($formData['emergency_conatct1_first_name'] ?? '') . '</td></tr>
                            <tr><td>Last Name</td><td>' . htmlspecialchars($formData['emergency_conatct1_last_name'] ?? '') . '</td></tr>
                            <tr><td>Address</td><td>' . htmlspecialchars($formData['emergency_conatct1_Address'] ?? '') . '</td></tr>
                            <tr><td>Address Line 2</td><td>' . htmlspecialchars($formData['emergency_conatct1_Address_line2'] ?? '') . '</td></tr>
                            <tr><td>City</td><td>' . htmlspecialchars($formData['emergency_conatct1_city'] ?? '') . '</td></tr>
                            <tr><td>County/State/Region</td><td>' . htmlspecialchars($formData['emergency_conatct1_country_state_region'] ?? '') . '</td></tr>
                            <tr><td>ZIP/Postal Code</td><td>' . htmlspecialchars($formData['emergency_conatct1_zipCode'] ?? '') . '</td></tr>
                            <tr><td>Country</td><td>' . htmlspecialchars($formData['emergency_conatct1_country'] ?? '') . '</td></tr>
                            <tr><td>Email</td><td>' . htmlspecialchars($formData['emergency_conatct1_email'] ?? '') . '</td></tr>
                            <tr><td>Mobile</td><td>' . htmlspecialchars($formData['emergency_conatct1_mobile'] ?? '') . '</td></tr>
                        </table>
                    </div>
                    <div class="section">
                        <h2>Consent Details</h2>
                        <table>
                            <tr><th>Field</th><th>Value</th></tr>
                            <tr><td>First Name</td><td>' . htmlspecialchars($formData['consent_1_first_name'] ?? '') . '</td></tr>
                            <tr><td>Last Name</td><td>' . htmlspecialchars($formData['consent_1_last_name'] ?? '') . '</td></tr>
                            <tr><td>Terms Agreed</td><td>' . htmlspecialchars($formData['consent_1_checkbox'] ?? '') . '</td></tr>
                        </table>
                    </div>
                    <div class="section">
                        <h2>Additional Information</h2>
                        <table>
                            <tr><th>Field</th><th>Value</th></tr>
                            <tr><td>How did you hear about us?</td><td>' . htmlspecialchars($formData['how_did_you_hear'] ?? '') . '</td></tr>
                        </table>
                    </div>
                    <div class="section">
                        <h2>Terms and Conditions</h2>
                        <ol>';

                foreach ($termsData as $key => $term) {
                    if (!empty($term)) {
                        $html .= '<li>' . htmlspecialchars($term) . ' <span style="color: green; font-size: 16px;">✓</span></li>';
                    }
                }
                $html .= '
                            </ol>
                        </div>
                        <div class="section">
                            <h2>I agree to all the terms and conditions outlined in this form.</h2>
                            <table>
                                <tr><td>Date</td><td>' . htmlspecialchars($consentDate) . '</td></tr>
                                <tr><td>Signature</td><td><img src="' . htmlspecialchars($formData['consent_1signature'] ?? '') . '" class="signature-img" alt="Signature"></td></tr>
                            </table>
                        </div>
                    </body>
                    </html>';

                $html .= '
                        </ol>
                    </div>
                </body>
                </html>';

                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();
                $pdfOutput = $dompdf->output();
                if (!file_exists(public_path('student_form'))) {
                    mkdir(public_path('student_form'), 0755, true);
                }
                file_put_contents($pdfPath, $pdfOutput);

                // Email template base
                $emailTemplate = '
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>%s</title>
                </head>
                <body style="font-family: Arial, sans-serif; background-color: #f5f7fa; padding: 20px; color: #333;">
                    <!-- Logo -->
                    <div style="text-align: center; margin-bottom: 20px;">
                        <img src="https://images.efrobel.com/Frobellogo.png" alt="Frobel Logo" style="max-width: 180px;">
                    </div>
                    <!-- Main Content -->
                    <div style="background-color: #ffffff; padding: 25px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.05);">
                        <h2 style="color: #2e6da4;">Dear %s,</h2>
                        %s
                        <p>If you have any questions or require clarification, feel free to get in touch with us.</p>
                        <p>Kind regards,<br>
                        <strong>Frobel Education Team</strong><br>
                        Frobel Education</p>
                    </div>
                    <!-- Footer -->
                    <div style="text-align: center; font-size: 13px; color: #777; margin-top: 30px;">
                        <p>
                            67–73 Longbridge Road, Barking, Essex, IG11 8TG<br>
                            Phone: <a href="tel:02089355931" style="color: #337ab7;">020 8935 5931</a> |
                            Email: <a href="mailto:admin@frobel.co.uk" style="color: #337ab7;">admin@frobel.co.uk</a> |
                            Email: <a href="mailto:admin@frobel.co.uk" style="color: #337ab7;">admin@frobel.co.uk</a>
                        </p>
                        <img src="https://images.efrobel.com/FrobelBranding.png" alt="Frobel Branding" style="max-width: 300px; margin-top: 10px;">
                    </div>
                </body>
                </html>';

                // Send confirmation email to parent
                $parentEmail = $formData['parent1_email'] ?? '';
                if (!empty($parentEmail) && filter_var($parentEmail, FILTER_VALIDATE_EMAIL)) {
                    $parentEmailContent = sprintf(
                        $emailTemplate,
                        'Registration Confirmation',
                        'Learner',
                        '<p>Thank you for registering with Frobel Education. Your form has been successfully submitted to the ' . htmlspecialchars($branch->branch_name) . ' branch. We will be in touch shortly.</p>'
                    );

                    Mail::html($parentEmailContent, function ($message) use ($parentEmail) {
                        $message->to($parentEmail)
                            ->subject('Thank You for Your Registration - Frobel Education')
                            ->from('no-reply@frobel.co.uk', 'Frobel Learning');
                    });
                } else {
                    Log::warning('Invalid or missing parent email: ' . ($parentEmail ?? 'NULL'));
                    throw new \Exception('Invalid or missing parent email', 400);
                }

                // Send notification email to branch admin with PDF attachment (commented out)
                /*
                $adminEmailContent = sprintf(
                    $emailTemplate,
                    'New Student Registration',
                    'Administrator',
                    '<p>A new student registration form has been submitted for the ' . htmlspecialchars($branch->branch_name) . ' branch. Please find the details in the attached PDF.</p>'
                );

                Mail::html($adminEmailContent, function ($message) use ($branchEmail, $pdfPath, $pdfFileName, $studentRequest) {
                    $message->to($branchEmail)
                        ->subject('New Student Registration - ' . $studentRequest->id)
                        ->from('exams@frobel.co.uk', 'Frobel Education')
                        ->attach($pdfPath, [
                            'as' => $pdfFileName,
                            'mime' => 'application/pdf',
                        ]);
                });
                */

                // Notify users with access permissions
                $userIds = AccessPermission::where('page_name', 'LIKE', '%new_admission%')
                    ->pluck('user_id')
                    ->toArray();

                $users = User::whereIn('id', $userIds)->get();

                if ($users->isEmpty()) {
                    Log::warning('No users found for notification with user_ids: ' . json_encode($userIds));
                } else {
                    Notification::send($users, new StudentRequestNotification($studentRequest));
                }

                return response()->json(['message' => 'Data stored and emails sent successfully']);
            });
        } catch (\Exception $e) {
            Log::error('Error in studentRequest: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred', 'message' => $e->getMessage()], $e->getCode() ?: 500);
        }
    }

    public function publicLearner()
    {
        $branches = DB::table('users')
            ->where('is_main_branch', 1)
            ->select('branch_id', 'branch_name')
            ->get();
        return view('branchFrontend.IAG.publicLearner', compact('branches'));
    }
}
