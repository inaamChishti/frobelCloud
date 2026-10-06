<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\WhatsAppMessage;
use Twilio\Rest\Client;

class WhatsAppController extends Controller
{
    private $accessToken;
    private $phoneNumberId;
    private $apiUrl = 'https://graph.facebook.com/v20.0';
    private $testPhoneNumber = '+15550667872'; // Test phone number provided

    public function __construct()
    {
        $this->accessToken = env('WHATSAPP_ACCESS_TOKEN');
        $this->phoneNumberId = env('WHATSAPP_PHONE_NUMBER_ID');
    }

    public function sendWhatsApp()
    {
        // Fetch students with their guardian and kin phone numbers
        $students = DB::table('studentdata')
            ->leftJoin('guardian', 'studentdata.guardianid', '=', 'guardian.Guardianid')
            ->leftJoin('kin', 'studentdata.kinid', '=', 'kin.kinid')
            ->select(
                'studentdata.studentid',
                'studentdata.studentname',
                'studentdata.studentsur',
                'studentdata.admissionid',
                'guardian.guardianmob',
                'kin.kinmob'
            )
            ->get();
            // \Log::info('Students Data:', $students->toArray());
                // dd($students->take(10));
        return view('branchFrontend.whatsApp.index', compact('students'));
    }

public function sendMessage(Request $request)
{
    dd($request->all());
    $request->validate([
        'contact_number' => 'required|string',
        'message' => 'required|string',
    ]);

    // Get the Twilio credentials from .env
    $sid = env('TWILIO_SID');
    $token = env('TWILIO_AUTH_TOKEN');
    $from = env('TWILIO_WHATSAPP_FROM');

    // Split the comma-separated phone numbers into an array
    $phoneNumbers = explode(',', $request->input('contact_number'));

    // Trim whitespace from each number and remove empty entries
    $phoneNumbers = array_map('trim', $phoneNumbers);
    $phoneNumbers = array_filter($phoneNumbers);

    // Remove duplicates
    $phoneNumbers = array_unique($phoneNumbers);

    $success = true;
    $errors = [];
    $twilio = new Client($sid, $token);
    $messageText = $request->input('message');

    foreach ($phoneNumbers as $phoneNumber) {
        try {
            // Format phone number for WhatsApp (ensure it starts with whatsapp:+)
            if (!str_starts_with($phoneNumber, 'whatsapp:+')) {
                $phoneNumber = 'whatsapp:+' . ltrim($phoneNumber, '+');
            }

            // Send message via Twilio
            $message = $twilio->messages->create(
                $phoneNumber,
                [
                    "from" => $from,
                    "body" => $messageText
                ]
            );

            // Log the successful message
            Log::info("Message sent to {$phoneNumber}: {$messageText}");
            Log::info("Twilio SID: " . $message->sid);

            // Save to database if needed
            WhatsAppMessage::create([
                'message_id' => $message->sid,
                'from_phone' => $from,
                'message_text' => $messageText,
                'received_at' => now(),
            ]);

        } catch (\Exception $e) {
            $success = false;
            $errorMsg = "Failed to send message to {$phoneNumber}: " . $e->getMessage();
            $errors[] = $errorMsg;
            Log::error($errorMsg);
        }
    }

    if ($success) {
        return redirect()->back()->with('success', 'Messages sent successfully!');
    } else {
        return redirect()->back()->with('error', 'Some messages failed to send: ' . implode(', ', $errors));
    }
}
    public function handleWebhook(Request $request)
    {
        // Verify webhook (for initial setup)
        if ($request->query('hub_mode') === 'subscribe' && $request->query('hub_verify_token')) {
            $verifyToken = env('WHATSAPP_VERIFY_TOKEN');
            if ($request->query('hub_verify_token') === $verifyToken) {
                return response($request->query('hub_challenge'), 200);
            }
            return response('Invalid verify token', 403);
        }

        // Process incoming messages
        $payload = $request->all();
        Log::info('WhatsApp Webhook Payload: ', $payload);

        if (isset($payload['entry'][0]['changes'][0]['value']['messages'])) {
            $messages = $payload['entry'][0]['changes'][0]['value']['messages'];
            foreach ($messages as $message) {
                $from = $message['from'];
                $text = $message['text']['body'] ?? 'No text';
                $messageId = $message['id'];

                WhatsAppMessage::create([
                    'message_id' => $messageId,
                    'from_phone' => $from,
                    'message_text' => $text,
                    'received_at' => now(),
                ]);

                Log::info("Received message from {$from}: {$text} (ID: {$messageId})");
            }
        }

        return response('Webhook processed', 200);
    }
}
