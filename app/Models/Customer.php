<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_name',
        'code',
        'contact_name',
        'phone_number',
        'email',
        'billing_address_line_1',
        'billing_address_line_2',
        'billing_city',
        'billing_state',
        'billing_postal_code',
        'billing_country',
        'shipping_address_line_1',
        'shipping_address_line_2',
        'shipping_city',
        'shipping_state',
        'shipping_postal_code',
        'shipping_country',
        'gst_number',
        'organization_id',
        'branch_id',
        'created_by',
    ];

    /**
     * Get the organization that owns the customer.
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the branch that owns the customer.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the user who created the customer.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the full billing address as a formatted string.
     */
    public function getBillingAddressAttribute()
    {
        $address = $this->billing_address_line_1 ?? '';
        if ($this->billing_address_line_2) {
            $address .= ', ' . $this->billing_address_line_2;
        }
        if ($this->billing_city) {
            $address .= ', ' . $this->billing_city;
        }
        if ($this->billing_state) {
            $address .= ', ' . $this->billing_state;
        }
        if ($this->billing_postal_code) {
            $address .= ' - ' . $this->billing_postal_code;
        }
        if ($this->billing_country) {
            $address .= ', ' . $this->billing_country;
        }
        return $address;
    }

    /**
     * Get the full shipping address as a formatted string.
     */
    public function getShippingAddressAttribute()
    {
        $address = $this->shipping_address_line_1 ?? '';
        if ($this->shipping_address_line_2) {
            $address .= ', ' . $this->shipping_address_line_2;
        }
        if ($this->shipping_city) {
            $address .= ', ' . $this->shipping_city;
        }
        if ($this->shipping_state) {
            $address .= ', ' . $this->shipping_state;
        }
        if ($this->shipping_postal_code) {
            $address .= ' - ' . $this->shipping_postal_code;
        }
        if ($this->shipping_country) {
            $address .= ', ' . $this->shipping_country;
        }
        return $address;
    }
}
