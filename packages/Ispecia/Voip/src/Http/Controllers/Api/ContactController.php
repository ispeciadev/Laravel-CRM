<?php

namespace Ispecia\Voip\Http\Controllers\Api;

use Illuminate\Http\Request;
use Ispecia\Admin\Http\Controllers\Controller;
use Ispecia\Contact\Models\Person;
use Ispecia\Lead\Models\Lead;

class ContactController extends Controller
{
    /**
     * Get contacts with phone numbers for the softphone
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $limit = $request->get('limit', 100);

        // Get persons with contact numbers
        $persons = Person::select('id', 'name', 'emails', 'contact_numbers')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhereJsonContains('contact_numbers', $search);
                });
            })
            ->whereNotNull('contact_numbers')
            ->limit($limit)
            ->get()
            ->map(function ($person) {
                $contactNumbers = is_array($person->contact_numbers) ? $person->contact_numbers : [];
                $primaryPhone = !empty($contactNumbers) ? ($contactNumbers[0]['value'] ?? null) : null;
                
                return [
                    'id' => $person->id,
                    'name' => $person->name,
                    'phone' => $primaryPhone,
                    'email' => is_array($person->emails) && !empty($person->emails) 
                        ? ($person->emails[0]['value'] ?? null) 
                        : null,
                    'type' => 'person',
                ];
            })
            ->filter(function ($contact) {
                return !is_null($contact['phone']);
            });

        // Get leads with phone numbers
        $leads = Lead::select('id', 'title', 'person_id', 'lead_value')
            ->with('person:id,name,emails,contact_numbers')
            ->when($search, function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                      ->orWhereHas('person', function ($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      });
            })
            ->whereHas('person', function ($q) {
                $q->whereNotNull('contact_numbers');
            })
            ->limit($limit)
            ->get()
            ->map(function ($lead) {
                if (!$lead->person) {
                    return null;
                }
                
                $contactNumbers = is_array($lead->person->contact_numbers) 
                    ? $lead->person->contact_numbers 
                    : [];
                $primaryPhone = !empty($contactNumbers) ? ($contactNumbers[0]['value'] ?? null) : null;
                
                return [
                    'id' => $lead->id,
                    'name' => $lead->person->name . ' (' . $lead->title . ')',
                    'phone' => $primaryPhone,
                    'email' => is_array($lead->person->emails) && !empty($lead->person->emails) 
                        ? ($lead->person->emails[0]['value'] ?? null) 
                        : null,
                    'type' => 'lead',
                    'lead_id' => $lead->id,
                ];
            })
            ->filter(function ($contact) {
                return !is_null($contact) && !is_null($contact['phone']);
            });

        // Merge and deduplicate
        $allContacts = $persons->merge($leads)
            ->unique('phone')
            ->sortBy('name')
            ->values();

        return response()->json([
            'success' => true,
            'data' => $allContacts,
        ]);
    }
}
