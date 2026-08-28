<?php

namespace Tests\Feature;

use App\Models\DemoRequest;
use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactAndLeadManagementTest extends TestCase
{
    /**
     * Test public contact form submission creates a database record
     */
    public function test_public_contact_form_creates_database_record_and_returns_success()
    {
        Mail::fake();

        $payload = [
            'name'            => 'Vikram Shah',
            'restaurant_name' => 'Royal Heritage Thali',
            'phone'           => '9876543210',
            'email'           => 'vikram@royalheritage.com',
            'city'            => 'Ahmedabad',
            'preferred_time'  => 'Tomorrow 4 PM',
            'message'         => 'Interested in 20 tables QR ordering setup.',
            'source'          => 'contact_page',
        ];

        $response = $this->postJson('/api/contact/submit', $payload);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Thank you! Your enquiry has been received. Our Tendorabuy team will contact you shortly.',
                 ]);

        $this->assertDatabaseHas('demo_requests', [
            'name'            => 'Vikram Shah',
            'restaurant_name' => 'Royal Heritage Thali',
            'phone'           => '9876543210',
            'email'           => 'vikram@royalheritage.com',
            'city'            => 'Ahmedabad',
            'source'          => 'contact_page',
            'status'          => 'new',
        ]);
    }

    /**
     * Test demo request form submission endpoint
     */
    public function test_demo_request_form_creates_database_record()
    {
        Mail::fake();

        $payload = [
            'name'            => 'Ananya Sharma',
            'restaurant_name' => 'Urban Brew Cafe',
            'phone'           => '+91 9123456789',
            'email'           => 'ananya@urbanbrew.com',
            'city'            => 'Surat',
            'preferred_time'  => 'Friday Afternoon',
            'message'         => 'Would like to test KDS kitchen screen.',
            'source'          => 'demo_page',
        ];

        $response = $this->postJson('/api/demo/request', $payload);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                 ]);

        $this->assertDatabaseHas('demo_requests', [
            'name'            => 'Ananya Sharma',
            'restaurant_name' => 'Urban Brew Cafe',
            'phone'           => '+91 9123456789',
            'source'          => 'demo_page',
        ]);
    }

    /**
     * Test validation fails when required fields are missing
     */
    public function test_validation_fails_when_required_fields_missing()
    {
        $response = $this->postJson('/api/contact/submit', [
            'name' => '',
            'restaurant_name' => '',
            'phone' => '',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name', 'restaurant_name', 'phone']);
    }

    /**
     * Test validation rejects invalid email format
     */
    public function test_validation_rejects_invalid_email()
    {
        $response = $this->postJson('/api/contact/submit', [
            'name'            => 'Test User',
            'restaurant_name' => 'Test Cafe',
            'phone'           => '9876543210',
            'email'           => 'not-a-valid-email',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test validation rejects invalid phone format
     */
    public function test_validation_rejects_invalid_phone()
    {
        $response = $this->postJson('/api/contact/submit', [
            'name'            => 'Test User',
            'restaurant_name' => 'Test Cafe',
            'phone'           => '123', // too short
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['phone']);
    }

    /**
     * Test honeypot silently captures spam bots without saving
     */
    public function test_honeypot_discards_bot_submissions()
    {
        $payload = [
            'name'            => 'Spam Bot',
            'restaurant_name' => 'Spam Food',
            'phone'           => '9999999999',
            'website_hp'      => 'I am a bot', // bot fills this hidden field
        ];

        $response = $this->postJson('/api/contact/submit', $payload);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('demo_requests', [
            'name' => 'Spam Bot',
        ]);
    }

    /**
     * Test Super Admin can view all leads
     */
    public function test_superadmin_can_view_leads()
    {
        $lead = DemoRequest::create([
            'name'            => 'Priya Patel',
            'restaurant_name' => 'Green Leaf Garden',
            'phone'           => '9876543211',
            'email'           => 'priya@greenleaf.com',
            'city'            => 'Vadodara',
            'status'          => 'new',
            'source'          => 'contact_page',
        ]);

        $superadmin = User::firstOrCreate(
            ['username' => 'superadmin_test'],
            [
                'name' => 'Super Admin Test',
                'email' => 'superadmin_test@tendorabuy.in',
                'password' => bcrypt('SuperAdmin123!'),
                'role' => 'superadmin',
            ]
        );

        $response = $this->actingAs($superadmin)->getJson('/api/superadmin/leads');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                 ])
                 ->assertJsonFragment([
                     'restaurant_name' => 'Green Leaf Garden',
                     'name'            => 'Priya Patel',
                 ]);
    }

    /**
     * Test Super Admin can update lead status
     */
    public function test_superadmin_can_update_lead_status()
    {
        $lead = DemoRequest::create([
            'name'            => 'Karan Mehta',
            'restaurant_name' => 'Flavors of Gujarat',
            'phone'           => '9876543212',
            'status'          => 'new',
        ]);

        $superadmin = User::firstOrCreate(
            ['username' => 'superadmin_test'],
            [
                'name' => 'Super Admin Test',
                'email' => 'superadmin_test@tendorabuy.in',
                'password' => bcrypt('SuperAdmin123!'),
                'role' => 'superadmin',
            ]
        );

        $response = $this->actingAs($superadmin)->putJson("/api/superadmin/leads/{$lead->id}/status", [
            'status' => 'contacted',
            'notes'  => 'Called and scheduled zoom walkthrough for tomorrow.',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Lead status updated to Contacted.',
                 ]);

        $this->assertDatabaseHas('demo_requests', [
            'id'     => $lead->id,
            'status' => 'contacted',
            'notes'  => 'Called and scheduled zoom walkthrough for tomorrow.',
        ]);
    }

    /**
     * Test unauthorized users cannot access Super Admin leads
     */
    public function test_unauthorized_users_cannot_access_superadmin_leads()
    {
        // Unauthenticated guest
        $response = $this->getJson('/api/superadmin/leads');
        $response->assertStatus(401);

        // Authenticated non-superadmin (regular manager/waiter)
        $regularUser = User::firstOrCreate(
            ['username' => 'manager_test'],
            [
                'name' => 'Manager Test',
                'email' => 'manager_test@example.com',
                'password' => bcrypt('password123'),
                'role' => 'manager',
                'restaurant_id' => 1,
            ]
        );

        $response = $this->actingAs($regularUser)->getJson('/api/superadmin/leads');
        $response->assertStatus(403);
    }
}
