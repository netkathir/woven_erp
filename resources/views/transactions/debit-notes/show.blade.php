@extends('layouts.dashboard')

@section('title', 'View Debit Note - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="color: #333; font-size: 22px; margin: 0;">Debit Note Details</h2>
        <div style="display: flex; gap: 10px;">
            @if($debitNote->isDraft())
                <form action="{{ route('debit-notes.submit', $debitNote->id) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" style="padding: 8px 16px; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px;">
                        <i class="fas fa-check"></i> Submit
                    </button>
                </form>
            @endif
            
            @if(!$debitNote->isCancelled())
                <button type="button" onclick="showCancelModal()" style="padding: 8px 16px; background: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px;">
                    <i class="fas fa-times"></i> Cancel
                </button>
            @endif
            
            <a href="{{ route('debit-notes.index') }}" style="padding: 8px 16px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-size: 14px;">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 5px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 5px; margin-bottom: 20px;">
            {{ session('error') }}
        </div>
    @endif

    {{-- Header Information --}}
    <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
            <div>
                <strong style="color: #666;">Debit Note Number:</strong>
                <p style="margin: 5px 0 0 0; color: #333; font-size: 16px;">{{ $debitNote->debit_note_number }}</p>
            </div>
            <div>
                <strong style="color: #666;">Date:</strong>
                <p style="margin: 5px 0 0 0; color: #333;">{{ optional($debitNote->debit_note_date)->format('d-m-Y') }}</p>
            </div>
            <div>
                <strong style="color: #666;">Status:</strong>
                <p style="margin: 5px 0 0 0;">
                    @if($debitNote->status == 'Draft')
                        <span style="padding: 4px 12px; background: #ffc107; color: #333; border-radius: 12px; font-size: 12px; font-weight: 500;">Draft</span>
                    @elseif($debitNote->status == 'Submitted')
                        <span style="padding: 4px 12px; background: #28a745; color: white; border-radius: 12px; font-size: 12px; font-weight: 500;">Submitted</span>
                    @else
                        <span style="padding: 4px 12px; background: #dc3545; color: white; border-radius: 12px; font-size: 12px; font-weight: 500;">Cancelled</span>
                    @endif
                </p>
            </div>
            <div>
                <strong style="color: #666;">Reference Document:</strong>
                <p style="margin: 5px 0 0 0; color: #333;">
                    @if($debitNote->reference_document_type && $debitNote->reference_document_number)
                        {{ $debitNote->reference_document_type }}: {{ $debitNote->reference_document_number }}
                    @else
                        Manual
                    @endif
                </p>
            </div>
        </div>
    </div>

    {{-- Party Information --}}
    <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Party Information</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
            <div>
                <strong style="color: #666;">Party Type:</strong>
                <p style="margin: 5px 0 0 0; color: #333;">{{ $debitNote->party_type ?? '-' }}</p>
            </div>
            <div>
                <strong style="color: #666;">Party Name:</strong>
                <p style="margin: 5px 0 0 0; color: #333;">{{ $debitNote->party_name ?? '-' }}</p>
            </div>
            <div>
                <strong style="color: #666;">GST Number:</strong>
                <p style="margin: 5px 0 0 0; color: #333;">{{ $debitNote->gst_number ?? '-' }}</p>
            </div>
            <div>
                <strong style="color: #666;">Currency:</strong>
                <p style="margin: 5px 0 0 0; color: #333;">{{ $debitNote->currency ?? 'INR' }}</p>
            </div>
        </div>
    </div>

    {{-- Debit Note Details --}}
    <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Debit Note Details</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
            <div>
                <strong style="color: #666;">Reason:</strong>
                <p style="margin: 5px 0 0 0; color: #333;">{{ $debitNote->debit_note_reason ?? '-' }}</p>
            </div>
            @if($debitNote->remarks)
            <div style="grid-column: 1 / -1;">
                <strong style="color: #666;">Remarks:</strong>
                <p style="margin: 5px 0 0 0; color: #333;">{{ $debitNote->remarks }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Line Items --}}
    <div style="margin-bottom: 20px;">
        <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Line Items</h3>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: left;">Item Name</th>
                        <th style="padding: 12px; text-align: left;">Description</th>
                        <th style="padding: 12px; text-align: right;">Quantity</th>
                        <th style="padding: 12px; text-align: center;">Unit</th>
                        <th style="padding: 12px; text-align: right;">Rate</th>
                        <th style="padding: 12px; text-align: right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($debitNote->items as $item)
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 12px;">{{ $item->item_name ?? ($item->product->product_name ?? '-') }}</td>
                            <td style="padding: 12px;">{{ $item->description ?? '-' }}</td>
                            <td style="padding: 12px; text-align: right;">{{ number_format($item->quantity, 2) }}</td>
                            <td style="padding: 12px; text-align: center;">{{ $item->unit_of_measure ?? '-' }}</td>
                            <td style="padding: 12px; text-align: right;">{{ number_format($item->rate, 2) }}</td>
                            <td style="padding: 12px; text-align: right;">{{ number_format($item->amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 20px; text-align: center; color: #666;">No items found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Totals --}}
    <div style="display: flex; justify-content: flex-end; margin-bottom: 20px;">
        <div style="max-width: 400px; width: 100%; background: #f9fafb; padding: 20px; border-radius: 8px; border: 1px solid #e5e7eb;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                <span style="font-weight: 500; color: #374151;">Subtotal:</span>
                <span style="font-weight: 600; color: #111827;">{{ number_format($debitNote->subtotal, 2) }}</span>
            </div>
            @if($debitNote->cgst_amount > 0)
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                <span style="font-weight: 500; color: #374151;">CGST:</span>
                <span style="font-weight: 600; color: #111827;">{{ number_format($debitNote->cgst_amount, 2) }}</span>
            </div>
            @endif
            @if($debitNote->sgst_amount > 0)
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                <span style="font-weight: 500; color: #374151;">SGST:</span>
                <span style="font-weight: 600; color: #111827;">{{ number_format($debitNote->sgst_amount, 2) }}</span>
            </div>
            @endif
            @if($debitNote->igst_amount > 0)
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                <span style="font-weight: 500; color: #374151;">IGST:</span>
                <span style="font-weight: 600; color: #111827;">{{ number_format($debitNote->igst_amount, 2) }}</span>
            </div>
            @endif
            @if($debitNote->adjustments != 0)
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                <span style="font-weight: 500; color: #374151;">Adjustments:</span>
                <span style="font-weight: 600; color: #111827;">{{ number_format($debitNote->adjustments, 2) }}</span>
            </div>
            @endif
            <div style="height: 1px; background: #e5e7eb; margin: 8px 0 10px;"></div>
            <div style="display: flex; justify-content: space-between;">
                <span style="font-weight: 700; color: #111827;">Total Debit Amount:</span>
                <span style="font-weight: 700; color: #111827;">{{ number_format($debitNote->total_debit_amount, 2) }}</span>
            </div>
        </div>
    </div>

    {{-- Audit Information --}}
    <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Audit Information</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
            <div>
                <strong style="color: #666;">Created By:</strong>
                <p style="margin: 5px 0 0 0; color: #333;">{{ $debitNote->creator->name ?? '-' }}</p>
            </div>
            <div>
                <strong style="color: #666;">Created At:</strong>
                <p style="margin: 5px 0 0 0; color: #333;">{{ $debitNote->created_at->format('d-m-Y H:i:s') }}</p>
            </div>
            @if($debitNote->submitted_by)
            <div>
                <strong style="color: #666;">Submitted By:</strong>
                <p style="margin: 5px 0 0 0; color: #333;">{{ $debitNote->submitter->name ?? '-' }}</p>
            </div>
            <div>
                <strong style="color: #666;">Submitted At:</strong>
                <p style="margin: 5px 0 0 0; color: #333;">{{ optional($debitNote->submitted_at)->format('d-m-Y H:i:s') }}</p>
            </div>
            @endif
            @if($debitNote->cancel_reason)
            <div style="grid-column: 1 / -1;">
                <strong style="color: #666;">Cancel Reason:</strong>
                <p style="margin: 5px 0 0 0; color: #333;">{{ $debitNote->cancel_reason }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Actions --}}
    <div style="display: flex; justify-content: flex-end; gap: 10px;">
        @if($debitNote->isDraft())
            <a href="{{ route('debit-notes.edit', $debitNote->id) }}" style="padding: 10px 20px; background: #ffc107; color: #333; text-decoration: none; border-radius: 5px; font-weight: 500;">
                <i class="fas fa-edit"></i> Edit
            </a>
        @endif
        <a href="{{ route('debit-notes.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">
            Back to List
        </a>
    </div>
</div>

{{-- Cancel Modal --}}
<div id="cancelModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; padding: 30px; border-radius: 10px; max-width: 500px; width: 90%;">
        <h3 style="margin-top: 0; color: #333;">Cancel Debit Note</h3>
        <form action="{{ route('debit-notes.cancel', $debitNote->id) }}" method="POST">
            @csrf
            <div style="margin-bottom: 20px;">
                <label for="cancel_reason" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Cancel Reason <span style="color: red;">*</span></label>
                <textarea name="cancel_reason" id="cancel_reason" rows="4" required
                          style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; resize: vertical;"
                          placeholder="Please provide a reason for cancelling this debit note (minimum 10 characters)"></textarea>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="hideCancelModal()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer;">
                    Close
                </button>
                <button type="submit" style="padding: 10px 20px; background: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer;">
                    Confirm Cancel
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function showCancelModal() {
        document.getElementById('cancelModal').style.display = 'flex';
    }
    
    function hideCancelModal() {
        document.getElementById('cancelModal').style.display = 'none';
    }
    
    // Close modal on outside click
    document.getElementById('cancelModal').addEventListener('click', function(e) {
        if (e.target === this) {
            hideCancelModal();
        }
    });
</script>
@endpush
@endsection

