<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\LicenseVerificationService;

class GenerateLicenseCommand extends Command
{
    protected $signature = 'license:generate 
                            {--domain= : Comma-separated allowed domain(s) e.g. restaurant.com,pos.restaurant.com}
                            {--ip= : Comma-separated allowed server IP(s) e.g. 3.110.208.210}
                            {--client= : Client / Company / Restaurant Name}
                            {--tier=enterprise : License Tier (single, enterprise, ultimate)}
                            {--expires= : Expiry Date YYYY-MM-DD or leave blank for lifetime}
                            {--save : Automatically save token to storage/license.key}';

    protected $description = 'Generate a cryptographically signed domain-locked commercial license token for DineFlow AI';

    public function handle(): int
    {
        $domainInput = $this->option('domain') ?: $this->ask('Enter authorized domain(s) [comma-separated]', 'dineflowai.duckdns.org');
        $ipInput = $this->option('ip') ?: $this->ask('Enter authorized server IP(s) [comma-separated, or blank]', '3.110.208.210');
        $client = $this->option('client') ?: $this->ask('Enter Client / Restaurant Name', 'DineFlow Production Client');
        $tier = $this->option('tier') ?: 'enterprise';
        $expires = $this->option('expires') ?: null;

        $domains = array_filter(array_map('trim', explode(',', (string)$domainInput)));
        $ips = array_filter(array_map('trim', explode(',', (string)$ipInput)));

        $data = [
            'client' => $client,
            'domains' => $domains,
            'ips' => $ips,
            'tier' => $tier,
            'expires_at' => $expires,
            'issued_at' => date('Y-m-d'),
        ];

        $token = LicenseVerificationService::generateToken($data);

        $this->newLine();
        $this->info('================================================================');
        $this->info('   DINEFLOW AI - CRYPTOGRAPHIC LICENSE GENERATOR');
        $this->info('   Developed & Owned by: Shubh Patel (+91 7777938338)');
        $this->info('================================================================');
        $this->table(
            ['Property', 'Assigned Value'],
            [
                ['Client / Restaurant', $client],
                ['Authorized Domain(s)', implode(', ', $domains)],
                ['Authorized IP(s)', !empty($ips) ? implode(', ', $ips) : 'Any / Auto'],
                ['License Tier', strtoupper($tier)],
                ['Resell Allowed?', 'STRICTLY PROHIBITED (Single Commercial Copy)'],
                ['Issued Date', date('Y-m-d')],
                ['Expiry Date', $expires ? $expires : 'LIFETIME (Permanent)'],
            ]
        );

        $this->newLine();
        $this->line('<fg=cyan>Encrypted License Token:</>');
        $this->line("<fg=yellow>{$token}</>");
        $this->newLine();

        $save = $this->option('save');
        if (!$save && $this->confirm('Would you like to save this token as the active license in storage/license.key?', true)) {
            $save = true;
        }

        if ($save) {
            $path = config('license.license_file', storage_path('license.key'));
            file_put_contents($path, $token);
            $this->info("✓ Active license successfully written to: {$path}");
        }

        $this->info('Done! This license cannot be pirated or run on unauthorized domains.');
        return Command::SUCCESS;
    }
}
