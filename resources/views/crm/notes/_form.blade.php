@php
    $editing = isset($note);
@endphp

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; margin-bottom: 25px;">
    <div>
        <label for="title" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Title <span style="color:red">*</span></label>
        <input type="text" name="title" id="title" required
               value="{{ old('title', $editing ? $note->title : '') }}"
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
        @error('title')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="customer_id" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Customer Name</label>
        <select name="customer_id" id="customer_id"
                style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
            <option value="">-- Select Customer --</option>
            @foreach($customers as $customer)
                <option value="{{ $customer->id }}" 
                        {{ old('customer_id', $editing ? $note->customer_id : '') == $customer->id ? 'selected' : '' }}>
                    {{ $customer->customer_name }}
                </option>
            @endforeach
        </select>
        @error('customer_id')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="visibility" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Visibility <span style="color:red">*</span></label>
        <select name="visibility" id="visibility" required
                style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
            <option value="internal" {{ old('visibility', $editing ? $note->visibility : 'internal') === 'internal' ? 'selected' : '' }}>Internal</option>
            <option value="external" {{ old('visibility', $editing ? $note->visibility : '') === 'external' ? 'selected' : '' }}>External</option>
        </select>
        @error('visibility')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>
</div>

<div style="margin-bottom: 20px;">
    <label for="note_content" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Note Content <span style="color:red">*</span></label>
    <textarea name="note_content" id="note_content" rows="6" required
              style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; font-family: inherit;">{{ old('note_content', $editing ? $note->note_content : '') }}</textarea>
    @error('note_content')
        <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
    @enderror
</div>

@if($editing && $note->attachments->count() > 0)
    <div style="margin-bottom: 20px;">
        <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Existing Attachments</label>
        <div style="display: flex; flex-wrap: wrap; gap: 10px;">
            @foreach($note->attachments as $attachment)
                <div style="padding: 10px; background: #f8f9fa; border: 1px solid #ddd; border-radius: 5px; display: flex; align-items: center; gap: 10px;">
                    <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank" 
                       style="color: #667eea; text-decoration: none;">
                        <i class="fas fa-file"></i> {{ $attachment->file_name }}
                    </a>
                    <form action="{{ route('notes.attachments.destroy', $attachment->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this attachment?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="padding: 4px 8px; background: #dc3545; color: white; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
@endif

<div style="margin-bottom: 20px;">
    <label for="attachments" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Attachments</label>
    <input type="file" name="attachments[]" id="attachments" multiple
           accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.txt"
           style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
    <small style="color: #666; font-size: 12px;">You can select multiple files. Max file size: 10MB</small>
    @error('attachments.*')
        <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
    @enderror
</div>

