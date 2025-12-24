<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\NoteAttachment;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class NoteController extends Controller
{
    public function index(Request $request)
    {
        $query = Note::with(['customer', 'creator'])->orderByDesc('id');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('note_content', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($q) use ($search) {
                        $q->where('customer_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($visibility = $request->get('visibility')) {
            $query->where('visibility', $visibility);
        }

        $notes = $query->paginate(15)->withQueryString();

        return view('crm.notes.index', compact('notes'));
    }

    public function create()
    {
        $customers = Customer::orderBy('customer_name')->get();
        return view('crm.notes.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:191'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'note_content' => ['required', 'string'],
            'visibility' => ['required', 'in:internal,external'],
            'attachments.*' => ['nullable', 'file', 'max:10240'], // 10MB max
        ]);

        $note = new Note();
        $note->fill($data);

        $user = Auth::user();
        if ($user) {
            $note->organization_id = $user->organization_id ?? null;
            $note->branch_id = session('active_branch_id');
            $note->created_by = $user->id;
        }

        $note->save();

        // Handle file uploads
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('notes/attachments', $fileName, 'public');

                NoteAttachment::create([
                    'note_id' => $note->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $filePath,
                    'file_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return redirect()->route('notes.index')
            ->with('success', 'Note created successfully.');
    }

    public function show(Note $note)
    {
        $note->load(['customer', 'creator', 'attachments']);
        return view('crm.notes.show', compact('note'));
    }

    public function edit(Note $note)
    {
        $customers = Customer::orderBy('customer_name')->get();
        $note->load('attachments');
        return view('crm.notes.edit', compact('note', 'customers'));
    }

    public function update(Request $request, Note $note)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:191'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'note_content' => ['required', 'string'],
            'visibility' => ['required', 'in:internal,external'],
            'attachments.*' => ['nullable', 'file', 'max:10240'],
        ]);

        $note->fill($data);
        $note->save();

        // Handle new file uploads
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('notes/attachments', $fileName, 'public');

                NoteAttachment::create([
                    'note_id' => $note->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $filePath,
                    'file_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return redirect()->route('notes.index')
            ->with('success', 'Note updated successfully.');
    }

    public function destroy(Note $note)
    {
        // Delete attachments
        foreach ($note->attachments as $attachment) {
            if (Storage::disk('public')->exists($attachment->file_path)) {
                Storage::disk('public')->delete($attachment->file_path);
            }
            $attachment->delete();
        }

        $note->delete();

        return redirect()->route('notes.index')
            ->with('success', 'Note deleted successfully.');
    }

    public function deleteAttachment(NoteAttachment $attachment)
    {
        if (Storage::disk('public')->exists($attachment->file_path)) {
            Storage::disk('public')->delete($attachment->file_path);
        }
        $attachment->delete();

        return back()->with('success', 'Attachment deleted successfully.');
    }
}

