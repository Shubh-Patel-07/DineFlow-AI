@if(session('impersonator_superadmin_id'))
<div id="superadminImpersonationBanner" style="background: linear-gradient(90deg, #4F46E5 0%, #7C3AED 50%, #6366F1 100%); color: #FFFFFF; padding: 10px 24px; font-size: 13px; font-weight: 600; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; z-index: 999999; position: sticky; top: 0; box-shadow: 0 4px 15px rgba(0,0,0,0.3); border-bottom: 2px solid #A5B4FC;">
  <div style="display: flex; align-items: center; gap: 12px;">
    <span style="background: rgba(255,255,255,0.22); color: #FFFFFF; padding: 3px 10px; border-radius: 6px; font-size: 11px; font-weight: 800; letter-spacing: 0.5px; text-transform: uppercase; border: 1px solid rgba(255,255,255,0.4);">
      <i class="fa-solid fa-user-shield"></i> Superadmin Mode
    </span>
    <span>
      Viewing Restaurant: <strong style="color: #FDE047;">{{ session('impersonated_restaurant_name', auth()->user()?->restaurant?->name ?? 'Restaurant') }}</strong> 
      <span style="opacity: 0.85; font-size: 12px;">(Logged in as: {{ auth()->user()?->name }} &bull; {{ strtoupper(auth()->user()?->role ?? 'STAFF') }})</span>
    </span>
  </div>
  <div style="display: flex; align-items: center; gap: 12px;">
    <span style="font-size: 12px; opacity: 0.9;">1-Click Instant Return</span>
    <a href="{{ route('superadmin.leave-impersonate') }}" style="background: #FFFFFF; color: #4338CA; text-decoration: none; padding: 6px 16px; border-radius: 8px; font-weight: 700; font-size: 12px; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 6px rgba(0,0,0,0.2); transition: all 0.2s ease;">
      <i class="fa-solid fa-arrow-right-from-bracket"></i> Exit to Super Admin
    </a>
  </div>
</div>
@endif
