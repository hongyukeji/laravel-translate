<?php

namespace Hongyukeji\LaravelTranslate\Commands;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Hongyukeji\LaravelTranslate\Translate;
use Hongyukeji\LaravelTranslate\Translates;

class AllCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translate:all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Translates all source translations to target translations';

    protected $translator;

    /**
     * Create a new command instance.
     *
     * @param Translate $translator
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
        $this->line('Found ' . $foundLanguages . ' ' . Str::plural('language', $foundLanguages) . ' to translate');

        $availableTranslations = 0;
        $sourceTranslations = $this->translator->getSourceTranslations();
        $availableTranslations = count(Arr::dot($sourceTranslations)) * count($targetLanguages);

        $bar = $this->output->createProgressBar($availableTranslations);
        $bar->start();

        foreach ($targetLanguages as $targetLanguage) {
            $dottedSource = Arr::dot($sourceTranslations);

            $translated = $this->translator->translate($targetLanguage, $dottedSource, function () use ($bar) {
                $bar->advance();
            });

            $this->translator->fillLanguageFiles($targetLanguage, $translated);
        }

        $bar->finish();

        $this->info("\nTranslated " . $availableTranslations . ' language keys.');
    }
}
