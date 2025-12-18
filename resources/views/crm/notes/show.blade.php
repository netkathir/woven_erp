@extends('layouts.dashboard')

@section('title', 'View Note - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="color: #333; font-size: 22px; margin: 0;">Note Details</h2>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('notes.edit', $note->id) }}" style="padding: 8px 16px; background: #ffc107; color: #333; text-decoration: none; border-radius: 5px; font-size: 14px;">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('notes.index') }}" style="padding: 8px 16px; background: #6c757d; color: #fff; text-decoration: none; border-radius: 5px; font-size: 14px;">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; margin-bottom: 25px;">
        <div>
            <div style="font-size: 13px; color: #6b7280;">Title</div>
            <div style="font-weight: 600; color: #111827;">{{ $note->title }}</div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">Customer Name</div>
            <div style="font-weight: 600; color: #111827;">{{ $note->customer ? $note->customer->customer_name : '-' }}</div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">Created By</div>
            <div style="font-weight: 600; color: #111827;">{{ $note->creator ? $note->creator->name : '-' }}</div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">Date Created</div>
            <div style="font-weight: 600; color: #111827;">{{ $note->created_at->format('d-m-Y H:i') }}</div>
        </div>
        <div>
            <div style="font-size: 13px; color: #6b7280;">Visibility</div>
            <div style="font-weight: 600; color: #111827;">
                <span style="padding: 4px 8px; background: {{ $note->visibility === 'internal' ? '#17a2b8' : '#28a745' }}; color: white; border-radius: 4px; font-size: 12px;">
                    {{ ucfirst($note->visibility) }}
                </span>
            </div>
        </div>
    </div>

    <div style="margin-bottom: 20px;">
        <div style="font-size: 13px; color: #6b7280; margin-bottom: 6px;">Note Content</div>
        <div style="padding: 15px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; white-space: pre-wrap; color: #111827;">
            {{ $note->note_content }}
        </div>
    </div>

    @if($note->attachments->count() > 0)
        <div style="margin-bottom: 20px;">
            <div style="font-size: 13px; color: #6b7280; margin-bottom: 6px;">Attachments</div>
            <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                @foreach($note->attachments as $attachment)
                    <div style="padding: 10px; background: #f8f9fa; border: 1px solid #ddd; border-radius: 5px;">
                        <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank" 
                           style="color: #667eea; text-decoration: none; display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-file"></i> {{ $attachment->file_name }}
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection

