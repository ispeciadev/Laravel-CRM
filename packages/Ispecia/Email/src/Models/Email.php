<?php

namespace Ispecia\Email\Models;

use Illuminate\Database\Eloquent\Model;
use Ispecia\Contact\Models\PersonProxy;
use Ispecia\Email\Contracts\Email as EmailContract;
use Ispecia\Lead\Models\LeadProxy;
use Ispecia\Tag\Models\TagProxy;

class Email extends Model implements EmailContract
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'emails';

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'folders'       => 'array',
        'sender'        => 'array',
        'from'          => 'array',
        'reply_to'      => 'array',
        'cc'            => 'array',
        'bcc'           => 'array',
        'reference_ids' => 'array',
        'scheduled_at'  => 'datetime',
        'sent_at'       => 'datetime',
        'opened_at'     => 'datetime',
    ];

    /**
     * The attributes that are appended.
     *
     * @var array
     */
    protected $appends = [
        'time_ago',
        'tracking_status',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subject',
        'source',
        'user_type',
        'name',
        'reply',
        'is_read',
        'folders',
        'from',
        'sender',
        'reply_to',
        'cc',
        'bcc',
        'unique_id',
        'message_id',
        'reference_ids',
        'person_id',
        'lead_id',
        'parent_id',
        'scheduled_at',
        'tracking_hash',
        'sent_at',
        'opened_at',
        'open_count',
        'email_account_id',
    ];

    /**
     * Get the parent email.
     */
    public function parent()
    {
        return $this->belongsTo(EmailProxy::modelClass(), 'parent_id');
    }

    /**
     * Get the lead.
     */
    public function lead()
    {
        return $this->belongsTo(LeadProxy::modelClass());
    }

    /**
     * Get the emails.
     */
    public function emails()
    {
        return $this->hasMany(EmailProxy::modelClass(), 'parent_id');
    }

    /**
     * Get the person that owns the thread.
     */
    public function person()
    {
        return $this->belongsTo(PersonProxy::modelClass());
    }

    /**
     * The tags that belong to the lead.
     */
    public function tags()
    {
        return $this->belongsToMany(TagProxy::modelClass(), 'email_tags');
    }

    /**
     * Get the attachments.
     */
    public function attachments()
    {
        return $this->hasMany(AttachmentProxy::modelClass(), 'email_id');
    }

    /**
     * Get the time ago.
     */
    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get the tracking status.
     */
    public function getTrackingStatusAttribute(): string
    {
        if ($this->opened_at) {
            return 'opened';
        }

        if ($this->sent_at) {
            return 'sent';
        }

        return 'draft';
    }
}
