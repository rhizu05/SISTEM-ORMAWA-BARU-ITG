<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigratePrivateDocuments extends Command
{
    protected $signature = 'dokumen:migrate-private
                            {--dry-run : Tampilkan rencana tanpa memindahkan file}';

    protected $description = 'Pindahkan file proposal & LPJ dari disk public ke disk private (SEC-01)';

    public function handle(): int
    {
        $folders = ['proposals', 'lpj'];
        $moved = 0;
        $skipped = 0;
        $dry = (bool) $this->option('dry-run');

        foreach ($folders as $folder) {
            $files = Storage::disk('public')->files($folder);

            foreach ($files as $file) {
                if (Storage::disk('local')->exists($file)) {
                    $skipped++;
                    $this->line("SKIP (sudah ada): {$file}");
                    continue;
                }

                $this->line(($dry ? 'DRY ' : 'MOVE') . ": {$file}");
                if (! $dry) {
                    Storage::disk('local')->put($file, Storage::disk('public')->get($file));
                    Storage::disk('public')->delete($file);
                }
                $moved++;
            }
        }

        $this->info("Selesai. Dipindahkan: {$moved}, dilewati: {$skipped}." . ($dry ? ' (dry-run)' : ''));

        return self::SUCCESS;
    }
}
