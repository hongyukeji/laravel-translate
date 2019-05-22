<?php

namespace Hongyukeji\LaravelTranslate\Commands;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Hongyukeji\LaravelTranslate\Translate;

class MissingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'autotrans:missing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Translates all source translations that are not set in your target translations';

    protected $translator;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Translate $translator)
    {
        parent::__construct();
        $this->translator = $translator;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $targetLanguages = Arr::wrap(config('translate.target_language'));

        $foundLanguages = count($targetLanguages);
        $this->line('Found '.$foundLanguages.' '.Str::plural('language', $foundLanguages).' to translate');

        $missingCount = 0;
        foreach ($targetLanguages as $targetLanguage) {
            $missing = $this->translator->getMissingTranslations($targetLanguage);
            $missingCount += $missing->count();
            $this->line('Found '.$missing->count().' missing keys in '.$targetLanguage);
        }

        $bar = $this->output->createProgressBar($missingCount);
        $bar->start();

        foreach ($targetLanguages as $targetLanguage) {
            $missing = $this->translator->getMissingTranslations($targetLanguage);

            $translated = $this->translator->translate($targetLanguage, $missing, function () use ($bar) {
                $bar->advance();
            });

            $this->translator->fillLanguageFiles($targetLanguage, $translated);
        }

        $bar->finish();

        $this->info("\nTranslated ".$missingCount.' missing language keys.');
    }
}
