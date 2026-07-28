<?php

namespace App\Http\Controllers;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\User;
use App\Services\ChatbotService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    protected $chatbotService;

    public function __construct(ChatbotService $chatbotService)
    {
        $this->chatbotService = $chatbotService;
    }

    /**
     * Start a new chat conversation.
     */
    public function startConversation(Request $request)
    {
        $sessionId = Str::uuid()->toString();
        $guestToken = Str::random(64);

        $conversation = ChatConversation::create([
            'user_id' => auth()->id(),
            'session_id' => $sessionId,
            'guest_token_hash' => hash('sha256', $guestToken),
            'status' => 'active',
        ]);

        return response()->json([
            'conversation_id' => $conversation->id,
            'session_id' => $sessionId,
            'access_token' => $guestToken,
            'message' => 'Welcome! How can I help you today?',
        ]);
    }

    /**
     * Send a message in the conversation.
     */
    public function sendMessage(Request $request)
    {
        $validated = $request->validate([
            'session_id' => 'required|string|exists:chat_conversations,session_id',
            'message' => 'required|string|max:1000',
        ]);

        $conversation = ChatConversation::where('session_id', $validated['session_id'])->firstOrFail();
        $this->authorizeConversation($request, $conversation);

        // Check if conversation is escalated
        if ($conversation->isEscalated()) {
            return response()->json([
                'message' => 'This conversation has been escalated to a live agent. An agent will respond to you shortly.',
                'escalated' => true,
            ]);
        }

        // Save user message
        ChatMessage::create([
            'conversation_id' => $conversation->id,
            'message' => $validated['message'],
            'sender_type' => 'user',
            'sender_id' => auth()->id(),
        ]);

        // Process message with AI
        $response = $this->chatbotService->processMessage($validated['message']);

        // Save bot response
        ChatMessage::create([
            'conversation_id' => $conversation->id,
            'message' => $response['message'],
            'sender_type' => 'bot',
            'metadata' => [
                'intent' => $response['intent'],
                'confidence' => $response['confidence'],
            ],
        ]);

        return response()->json([
            'message' => $response['message'],
            'intent' => $response['intent'],
            'confidence' => $response['confidence'],
            'suggest_escalation' => $this->chatbotService->requiresEscalation($response['intent'], $response['confidence']),
        ]);
    }

    /**
     * Get conversation history.
     */
    public function getHistory(Request $request, $sessionId)
    {
        $conversation = ChatConversation::where('session_id', $sessionId)->firstOrFail();
        $this->authorizeConversation($request, $conversation);

        $messages = $conversation->messages()
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'message' => $message->message,
                    'sender_type' => $message->sender_type,
                    'created_at' => $message->created_at->toIso8601String(),
                ];
            });

        return response()->json([
            'messages' => $messages,
            'status' => $conversation->status,
        ]);
    }

    /**
     * Escalate conversation to a live agent.
     */
    public function escalate(Request $request)
    {
        $validated = $request->validate([
            'session_id' => 'required|string|exists:chat_conversations,session_id',
            'reason' => 'nullable|string|max:500',
        ]);

        $conversation = ChatConversation::where('session_id', $validated['session_id'])->firstOrFail();
        $this->authorizeConversation($request, $conversation);

        if ($conversation->isEscalated()) {
            return response()->json([
                'message' => 'This conversation is already escalated to an agent.',
                'success' => false,
            ]);
        }

        // Find available agent (simplified - just get first admin/staff user)
        $agent = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['super_admin', 'admin', 'staff']);
        })->first();

        $conversation->escalate($agent?->id);

        // Save escalation message
        ChatMessage::create([
            'conversation_id' => $conversation->id,
            'message' => $validated['reason'] ?? 'User requested to speak with an agent.',
            'sender_type' => 'user',
            'sender_id' => auth()->id(),
        ]);

        ChatMessage::create([
            'conversation_id' => $conversation->id,
            'message' => 'Your conversation has been escalated to a live agent. An agent will be with you shortly.',
            'sender_type' => 'bot',
        ]);

        return response()->json([
            'message' => 'Your conversation has been escalated to a live agent.',
            'success' => true,
        ]);
    }

    /**
     * Close conversation.
     */
    public function closeConversation(Request $request)
    {
        $validated = $request->validate([
            'session_id' => 'required|string|exists:chat_conversations,session_id',
        ]);

        $conversation = ChatConversation::where('session_id', $validated['session_id'])->firstOrFail();
        $this->authorizeConversation($request, $conversation);

        $conversation->update(['status' => 'closed']);

        return response()->json([
            'message' => 'Conversation closed.',
            'success' => true,
        ]);
    }

    private function authorizeConversation(Request $request, ChatConversation $conversation): void
    {
        if ($request->user() && $conversation->user_id === $request->user()->id) {
            return;
        }

        $token = $request->bearerToken() ?: $request->header('X-Chat-Token');
        if (
            ! is_string($token)
            || ! $conversation->guest_token_hash
            || ! hash_equals($conversation->guest_token_hash, hash('sha256', $token))
        ) {
            abort(403, 'Invalid conversation credentials.');
        }
    }
}
